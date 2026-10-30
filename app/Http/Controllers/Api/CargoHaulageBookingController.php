<?php

namespace App\Http\Controllers\Api;

use App\Constraints\DeliveryManStatus;
use App\Constraints\OrderStatus;
use App\Constraints\PreferredCurrency;
use App\Http\Controllers\Controller;
use App\Http\Requests\FreightQuotationRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderPackage;
use App\Models\ShippingRate;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CargoHaulageBookingController extends Controller
{
    public function store(Request $request)
    {
        $this->normalizeLocationNameInputs($request);

        if ($request->filled('currency')) {
            $request->merge(['currency' => strtoupper(trim((string) $request->input('currency')))]);
        }

        $validated = $request->validate([
            'pickup_location_id' => 'nullable|integer|exists:cities,id',
            'destination_location_id' => 'nullable|integer|exists:cities,id',
            'pickup_location_country' => 'required_without:pickup_location_id|nullable|string|max:120',
            'pickup_location_city' => 'required_without:pickup_location_id|nullable|string|max:120',
            'destination_location_country' => 'required_without:destination_location_id|nullable|string|max:120',
            'destination_location_city' => 'required_without:destination_location_id|nullable|string|max:120',
            'freight_mode' => 'nullable|in:Air,Sea,Road,air,sea,road,AIR,SEA,ROAD',
            'booking_date' => 'required|date',
            'vehicle_id' => 'nullable|integer|exists:vehicles,id',
            'vehicle_type' => 'nullable|in:' . Vehicle::TYPE_VAN . ',' . Vehicle::TYPE_TRUCK,
            'currency' => 'nullable|in:' . PreferredCurrency::EUR . ',' . PreferredCurrency::NGN,
            'weight' => 'required|numeric|min:0.1',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'cargo_description' => 'required|string',
            'cargo_value' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'pickup_name' => 'required|string',
            'pickup_phone' => 'required|string',
            'pickup_email' => 'nullable|email',
            'pickup_address1' => 'required|string',
            'pickup_address2' => 'nullable|string',
            'pickup_zip' => 'nullable|string',
            'pickup_city' => 'nullable|string',
            'pickup_state' => 'nullable|string',
            'pickup_country' => 'nullable|string',
            'delivery_name' => 'required|string',
            'delivery_phone' => 'required|string',
            'delivery_email' => 'nullable|email',
            'delivery_address1' => 'required|string',
            'delivery_address2' => 'nullable|string',
            'delivery_zip' => 'nullable|string',
            'delivery_city' => 'nullable|string',
            'delivery_state' => 'nullable|string',
            'delivery_country' => 'nullable|string',
        ]);

        if ($response = $this->resolveLocationInput($validated)) {
            return $response;
        }

        $vehicleId = $validated['vehicle_id'] ?? null;

        $vehicle = $this->supportedCargoVehicle(
            $validated['pickup_location_id'],
            $validated['destination_location_id'],
            (float) $validated['weight'],
            $validated['vehicle_type'] ?? null,
            $vehicleId
        );

        if (! $vehicle) {
            return response()->json([
                'status' => false,
                'message' => $vehicleId
                    ? 'The selected vehicle is not available for this route, vehicle type, and cargo weight.'
                    : 'Cargo/haulage booking is not supported for this route, vehicle type, and cargo weight.',
            ], 422);
        }

        // Without a delivery man, the order's dispatcher_id would be null and the booking would never
        // surface in the delivery man's orders (it would only be visible to admins). Reject it instead.
        if (! $vehicle->delivery_man_id) {
            return response()->json([
                'status' => false,
                'message' => 'The selected vehicle has no delivery man assigned and cannot be booked. Please choose another vehicle.',
            ], 422);
        }

        $currency = $validated['currency'] ?? PreferredCurrency::DEFAULT;
        $rate = $this->matchingRate($validated);
        $shippingCost = $this->shippingCost($vehicle, $rate, (float) $validated['weight'], $currency);

        if ($shippingCost === null) {
            return response()->json([
                'status' => false,
                'message' => 'This vehicle is not priced in ' . $currency . '. Please choose another currency or vehicle.',
            ], 422);
        }

        $pickupCity = City::find($validated['pickup_location_id']);
        $destinationCity = City::find($validated['destination_location_id']);

        // Normal app users may not have a customer record yet. Ensure one exists so the order is
        // tied to the booking user and they can later upload its cargo documents.
        $customer = Customer::firstOrCreate(['user_id' => $request->user()->id]);

        $order = DB::transaction(function () use ($validated, $vehicle, $rate, $shippingCost, $currency, $pickupCity, $destinationCity, $customer) {
            $order = new Order();
            $order->customer_id = $customer->id;
            $order->dispatcher_id = $vehicle->delivery_man_id;
            // Assigned to the chosen delivery man but awaiting their acceptance — not engaged yet.
            // dispatcher_date (accepted-at) is set only once the delivery man accepts the request.
            $order->delivery_man_status = DeliveryManStatus::PENDING;
            $order->pickup_location_id = $validated['pickup_location_id'];
            $order->delivery_location_id = $validated['destination_location_id'];
            $order->current_location_id = $validated['pickup_location_id'];

            // Populate the country/city fields rendered by the delivery man orders list (allOrdersDeliveryMan).
            $order->pickup_location_city_id = optional($pickupCity)->id;
            $order->pickup_location_country_id = optional($pickupCity)->country_id;
            $order->delivery_location_city_id = optional($destinationCity)->id;
            $order->delivery_location_country_id = optional($destinationCity)->country_id;
            $order->current_location_city_id = optional($pickupCity)->id;
            $order->current_location_country_id = optional($pickupCity)->country_id;

            $order->shipping_rate_id = optional($rate)->id;
            $order->tracking_id = 'SIOCARGO' . time() . Str::upper(Str::random(6));
            $order->status = OrderStatus::UNPAID;
            $order->provider = 'SIOPAY_CARGO_HAULAGE';
            $order->freight_mode = $this->freightMode($validated);
            $order->shipping_cost = $shippingCost;
            $order->shipping_currency = $currency;
            $order->return_date = $validated['booking_date'];
            $order->cond_of_goods = 'Cargo/Haulage';
            $order->val_of_goods = $validated['cargo_value'] ?? null;

            $order->pickup_name = $validated['pickup_name'];
            $order->pickup_phone = $validated['pickup_phone'];
            $order->pickup_email = $validated['pickup_email'] ?? null;
            $order->pickup_address1 = $validated['pickup_address1'];
            $order->pickup_address2 = $validated['pickup_address2'] ?? null;
            $order->pickup_zip = $validated['pickup_zip'] ?? null;
            $order->pickup_city = $validated['pickup_city'] ?? null;
            $order->pickup_state = $validated['pickup_state'] ?? null;
            $order->pickup_country = $validated['pickup_country'] ?? null;

            $order->delivery_name = $validated['delivery_name'];
            $order->delivery_phone = $validated['delivery_phone'];
            $order->delivery_email = $validated['delivery_email'] ?? null;
            $order->delivery_address1 = $validated['delivery_address1'];
            $order->delivery_address2 = $validated['delivery_address2'] ?? null;
            $order->delivery_zip = $validated['delivery_zip'] ?? null;
            $order->delivery_city = $validated['delivery_city'] ?? null;
            $order->delivery_state = $validated['delivery_state'] ?? null;
            $order->delivery_country = $validated['delivery_country'] ?? null;
            $order->save();

            $package = new OrderPackage();
            $package->order_id = $order->id;
            $package->type = 'cargo';
            $package->length = $validated['length'] ?? 0;
            $package->width = $validated['width'] ?? 0;
            $package->height = $validated['height'] ?? 0;
            $package->weight = $validated['weight'];
            $package->qty = $validated['quantity'] ?? 1;
            $package->item_desc = $validated['cargo_description'];
            $package->item_value = $validated['cargo_value'] ?? 0;
            $package->save();

            return $order;
        });

        return response()->json([
            'status' => true,
            'message' => 'Cargo/haulage booking created successfully.',
            'data' => [
                'tracking_id' => $order->tracking_id,
                'booking_supported' => true,
                'delivery_man_id' => $vehicle->delivery_man_id,
                'vehicle_id' => $vehicle->id,
                'vehicle_type' => $vehicle->type,
                'shipping_rate_id' => optional($rate)->id,
                'shipping_cost' => $shippingCost,
                'currency' => $currency,
            ],
        ], 201);
    }

    protected function supportedCargoVehicle($pickupLocationId, $destinationLocationId, float $weight, ?string $vehicleType, ?int $vehicleId = null): ?Vehicle
    {
        $query = Vehicle::query()
            ->where('status', Vehicle::STATUS_ACTIVE)
            //this filters supportedCargoVehicle — for cargo haulage. 
            // Bikes and cars generally can't haul cargo at the weights this method filters on, so the line acts as a guard ensuring only cargo-capable vehicle types (Van/Truck) are ever considered.
            //->whereIn('type', [Vehicle::TYPE_VAN, Vehicle::TYPE_TRUCK])
            ->where('origin_location', $pickupLocationId)
            ->where('destination_location', $destinationLocationId)
            ->whereNotNull('weight_range')
            ->where(function ($query) use ($weight) {
                $query->whereRaw('CAST(SUBSTRING_INDEX(weight_range, \'-\', 1) AS DECIMAL(10,2)) <= ?', [$weight])
                    ->whereRaw('CAST(SUBSTRING_INDEX(weight_range, \'-\', -1) AS DECIMAL(10,2)) >= ?', [$weight]);
            });

        if ($vehicleType) {
            $query->where('type', $vehicleType);
        }

        // When the user picked a specific vehicle from the rates list, book that exact vehicle —
        // but only if it still satisfies the route, weight, and type constraints above.
        if ($vehicleId) {
            $query->where('id', $vehicleId);
        }

        return $query->orderByRaw("FIELD(type, '" . Vehicle::TYPE_TRUCK . "', '" . Vehicle::TYPE_VAN . "')")
            ->first();
    }

    protected function normalizeLocationNameInputs(Request $request): void
    {
        $aliases = [
            'pickup_location_country' => ['pickup_location_country_name', 'pickup_country', 'pickup_country_name', 'origin_country', 'origin_country_name', 'from_country', 'ship_from_country'],
            'pickup_location_city' => ['pickup_location_city_name', 'origin_city', 'origin_city_name', 'from_city', 'ship_from_city'],
            'destination_location_country' => ['destination_location_country_name', 'destination_country', 'destination_country_name', 'to_country', 'ship_to_country'],
            'destination_location_city' => ['destination_location_city_name', 'destination_city', 'destination_city_name', 'to_city', 'ship_to_city'],
        ];

        $updates = [];

        foreach ($aliases as $target => $sources) {
            if ($request->filled($target)) {
                $updates[$target] = trim((string) $request->input($target));
                continue;
            }

            foreach ($sources as $source) {
                if ($request->filled($source)) {
                    $updates[$target] = trim((string) $request->input($source));
                    break;
                }
            }
        }

        if ($updates) {
            $request->merge($updates);
        }
    }

    protected function resolveLocationInput(array &$validated)
    {
        $locations = [
            'pickup_location' => 'pickup_location_id',
            'destination_location' => 'destination_location_id',
        ];

        foreach ($locations as $side => $locationIdKey) {
            if (! empty($validated[$locationIdKey])) {
                continue;
            }

            $country = $this->findCountryByNameOrCode($validated[$side . '_country'] ?? null);

            if (! $country) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        $side . '_country' => ['The selected ' . str_replace('_', ' ', $side) . ' country was not found.'],
                    ],
                ], 422);
            }

            $city = $this->findCityByNameAndCountry($validated[$side . '_city'] ?? null, $country->id);

            if (! $city) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        $side . '_city' => ['The selected ' . str_replace('_', ' ', $side) . ' city was not found for ' . $country->name . '.'],
                    ],
                ], 422);
            }

            $validated[$locationIdKey] = $city->id;
        }

        return null;
    }

    protected function findCountryByNameOrCode(?string $value): ?Country
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $needle = strtolower($value);

        return Country::query()
            ->whereRaw('LOWER(name) = ?', [$needle])
            ->orWhereRaw('LOWER(iso2) = ?', [$needle])
            ->orWhereRaw('LOWER(iso3) = ?', [$needle])
            ->first();
    }

    protected function findCityByNameAndCountry(?string $value, int $countryId): ?City
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return City::query()
            ->where('country_id', $countryId)
            ->whereRaw('LOWER(name) = ?', [strtolower($value)])
            ->first();
    }

    protected function matchingRate(array $validated): ?ShippingRate
    {
        $query = ShippingRate::query()
            ->where('origin_id', $validated['pickup_location_id'])
            ->where('destination_id', $validated['destination_location_id'])
            ->where('weight_start', '<=', $validated['weight'])
            ->where('weight_end', '>=', $validated['weight']);

        foreach (['length', 'width', 'height'] as $dimension) {
            if (isset($validated[$dimension])) {
                $query->where($dimension, '>=', $validated[$dimension]);
            }
        }

        return $query->first();
    }

    protected function shippingCost(Vehicle $vehicle, ?ShippingRate $rate, float $weight, string $currency = PreferredCurrency::EUR): ?float
    {
        // EUR falls back to the legacy per-km rate; NGN requires an explicit Naira rate.
        $perKm = $currency === PreferredCurrency::NGN
            ? $vehicle->rate_naira
            : ($vehicle->rate_eur ?? $vehicle->rate_per_km_per_kg);

        if ($vehicle->distance_km && $perKm) {
            return round((float) $vehicle->distance_km * (float) $perKm * $weight, 2);
        }

        // A configured shipping-rate price is treated as EUR, so only use it for EUR bookings.
        if ($currency === PreferredCurrency::EUR) {
            return optional($rate)->price;
        }

        return null;
    }

    protected function freightMode(array $validated): string
    {
        $mode = $validated['freight_mode'] ?? FreightQuotationRequest::MODE_ROAD;

        return ucfirst(strtolower($mode));
    }
}
