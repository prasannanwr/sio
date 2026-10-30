<?php

namespace App\Http\Controllers\Api;

use App\Constraints\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\CityShippingCost;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LocationShippingRateController extends Controller
{
    protected const FREIGHT_MODES = ['Air', 'Sea', 'Road'];

    public function quote(Request $request): JsonResponse
    {
        $this->normalizeLocationNameInputs($request);

        $validator = Validator::make($request->all(), [
            'origin_city_id' => ['nullable', 'integer', Rule::exists('cities', 'id')],
            'destination_city_id' => ['nullable', 'integer', Rule::exists('cities', 'id')],
            'origin_country' => ['required_without:origin_city_id', 'nullable', 'string', 'max:120'],
            'origin_city' => ['required_without:origin_city_id', 'nullable', 'string', 'max:120'],
            'destination_country' => ['required_without:destination_city_id', 'nullable', 'string', 'max:120'],
            'destination_city' => ['required_without:destination_city_id', 'nullable', 'string', 'max:120'],
            'freight_mode' => ['nullable', 'string', Rule::in(['Air', 'Sea', 'Road', 'air', 'sea', 'road', 'AIR', 'SEA', 'ROAD'])],
            'mode' => ['nullable', 'string', Rule::in(['Air', 'Sea', 'Road', 'air', 'sea', 'road', 'AIR', 'SEA', 'ROAD'])],
            'weight' => ['required', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (! Schema::hasColumn('city_shipping_costs', 'origin_city_id')) {
            return response()->json([
                'status' => false,
                'message' => 'Location shipping rates are not configured.',
            ], 404);
        }

        $data = $validator->validated();
        if ($response = $this->resolveLocationInput($data)) {
            return $response;
        }

        $weight = (float) $data['weight'];
        $freightMode = $request->input('freight_mode', $request->input('mode'));
        $freightMode = $freightMode ? ucfirst(strtolower($freightMode)) : null;
        $rate = $this->locationShippingCost($data['origin_city_id'], $data['destination_city_id'], $weight, $freightMode);

        if (! $rate) {
            return response()->json([
                'status' => false,
                'message' => 'No location shipping rate found for these cities and weight.',
            ], 404);
        }

        $originCity = City::with('country:id,name,iso2')->find($data['origin_city_id']);
        $destinationCity = City::with('country:id,name,iso2')->find($data['destination_city_id']);
        $shippingCost = $this->locationShippingCostPrice($rate, $weight);

        return response()->json([
            'status' => true,
            'message' => 'Location shipping cost retrieved successfully.',
            'data' => [
                'rate_id' => $rate->id,
                'carrier' => 'siopay',
                'origin' => $this->cityPayload($originCity),
                'destination' => $this->cityPayload($destinationCity),
                'weight' => $weight,
                'matched_rate' => [
                    'max_weight' => $rate->weight !== null ? (float) $rate->weight : null,
                    'weight_range' => $rate->weight_range,
                    'freight_mode' => $rate->freight_mode,
                    'base_cost' => round((float) $rate->cost, 2),
                    'distance_km' => $rate->distance_km !== null ? (float) $rate->distance_km : null,
                    'rate_per_km_per_kg' => $rate->rate_per_km_per_kg !== null ? (float) $rate->rate_per_km_per_kg : null,
                    'day' => $rate->day,
                ],
                'shipping_cost' => $shippingCost,
                'total' => $shippingCost,
            ],
        ]);
    }

    public function freightQuotation(Request $request): JsonResponse
    {
        $this->normalizeLocationNameInputs($request);

        $validator = Validator::make($request->all(), [
            'origin_city_id' => ['nullable', 'integer', Rule::exists('cities', 'id')],
            'destination_city_id' => ['nullable', 'integer', Rule::exists('cities', 'id')],
            'origin_country' => ['required_without:origin_city_id', 'nullable', 'string', 'max:120'],
            'origin_city' => ['required_without:origin_city_id', 'nullable', 'string', 'max:120'],
            'destination_country' => ['required_without:destination_city_id', 'nullable', 'string', 'max:120'],
            'destination_city' => ['required_without:destination_city_id', 'nullable', 'string', 'max:120'],
            'freight_mode' => ['nullable', 'string', Rule::in(['Air', 'Sea', 'Road', 'air', 'sea', 'road', 'AIR', 'SEA', 'ROAD'])],
            'mode' => ['nullable', 'string', Rule::in(['Air', 'Sea', 'Road', 'air', 'sea', 'road', 'AIR', 'SEA', 'ROAD'])],
            'weight' => ['required', 'numeric', 'min:0.1'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (! Schema::hasColumn('city_shipping_costs', 'origin_city_id')) {
            return response()->json([
                'status' => false,
                'message' => 'Location shipping rates are not configured.',
            ], 404);
        }

        $data = $validator->validated();
        if ($response = $this->resolveLocationInput($data)) {
            return $response;
        }

        $weight = (float) $data['weight'];
        $requestedMode = $request->input('freight_mode', $request->input('mode'));
        $requestedMode = $requestedMode ? ucfirst(strtolower($requestedMode)) : null;
        $modes = $requestedMode ? [$requestedMode] : self::FREIGHT_MODES;

        $originCity = City::with('country:id,name,iso2')->find($data['origin_city_id']);
        $destinationCity = City::with('country:id,name,iso2')->find($data['destination_city_id']);

        $quotes = collect($modes)->map(function (string $mode) use ($data, $weight) {
            $rate = $this->locationShippingCost(
                $data['origin_city_id'],
                $data['destination_city_id'],
                $weight,
                $mode
            );

            if (! $rate) {
                return [
                    'freight_mode' => $mode,
                    'booking_supported' => false,
                    'message' => 'No shipping rate configured for this route, mode, and weight.',
                    'rate_id' => null,
                    'shipping_cost' => null,
                    'total' => null,
                    'matched_rate' => null,
                ];
            }

            $shippingCost = $this->locationShippingCostPrice($rate, $weight);

            return [
                'freight_mode' => $mode,
                'booking_supported' => true,
                'message' => 'Shipping rate found.',
                'rate_id' => $rate->id,
                'shipping_cost' => $shippingCost,
                'total' => $shippingCost,
                'matched_rate' => [
                    'max_weight' => $rate->weight !== null ? (float) $rate->weight : null,
                    'weight_range' => $rate->weight_range,
                    'base_cost' => round((float) $rate->cost, 2),
                    'distance_km' => $rate->distance_km !== null ? (float) $rate->distance_km : null,
                    'rate_per_km_per_kg' => $rate->rate_per_km_per_kg !== null ? (float) $rate->rate_per_km_per_kg : null,
                    'day' => $rate->day,
                ],
            ];
        })->values();

        $hasSupportedQuote = $quotes->contains(fn (array $quote) => $quote['booking_supported']);

        return response()->json([
            'status' => $hasSupportedQuote,
            'message' => $hasSupportedQuote
                ? 'Freight quotation retrieved successfully.'
                : 'No freight quotations are configured for this route and weight.',
            'data' => [
                'carrier' => 'siopay',
                'origin' => $this->cityPayload($originCity),
                'destination' => $this->cityPayload($destinationCity),
                'weight' => $weight,
                'requested_freight_mode' => $requestedMode,
                'quotes' => $quotes,
            ],
        ], $hasSupportedQuote ? 200 : 404);
    }

    public function generateTrackingNumber(Request $request): JsonResponse
    {
        $prefix = strtoupper((string) $request->input('prefix', 'SIO'));
        $prefix = preg_replace('/[^A-Z0-9]/', '', $prefix) ?: 'SIO';

        return response()->json([
            'status' => true,
            'message' => 'Tracking number generated successfully.',
            'data' => [
                'tracking_id' => $this->uniqueTrackingId($prefix),
            ],
        ]);
    }

    public function bookCargo(Request $request): JsonResponse
    {
        $this->normalizeLocationNameInputs($request);

        $validator = Validator::make($request->all(), [
            'origin_city_id' => ['nullable', 'integer', Rule::exists('cities', 'id')],
            'destination_city_id' => ['nullable', 'integer', Rule::exists('cities', 'id')],
            'origin_country' => ['required_without:origin_city_id', 'nullable', 'string', 'max:120'],
            'origin_city' => ['required_without:origin_city_id', 'nullable', 'string', 'max:120'],
            'destination_country' => ['required_without:destination_city_id', 'nullable', 'string', 'max:120'],
            'destination_city' => ['required_without:destination_city_id', 'nullable', 'string', 'max:120'],
            'freight_mode' => ['nullable', 'string', Rule::in(['Air', 'Sea', 'Road', 'air', 'sea', 'road', 'AIR', 'SEA', 'ROAD'])],
            'mode' => ['nullable', 'string', Rule::in(['Air', 'Sea', 'Road', 'air', 'sea', 'road', 'AIR', 'SEA', 'ROAD'])],
            'booking_date' => ['required', 'date'],
            'weight' => ['required', 'numeric', 'min:0.1'],
            'length' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'cargo_description' => ['required', 'string', 'max:1000'],
            'cargo_value' => ['nullable', 'numeric', 'min:0'],
            'pickup_name' => ['required', 'string', 'max:255'],
            'pickup_phone' => ['required', 'string', 'max:80'],
            'pickup_email' => ['nullable', 'email', 'max:255'],
            'pickup_address1' => ['required', 'string', 'max:255'],
            'pickup_address2' => ['nullable', 'string', 'max:255'],
            'pickup_zip' => ['nullable', 'string', 'max:80'],
            'pickup_state' => ['nullable', 'string', 'max:120'],
            'delivery_name' => ['required', 'string', 'max:255'],
            'delivery_phone' => ['required', 'string', 'max:80'],
            'delivery_email' => ['nullable', 'email', 'max:255'],
            'delivery_address1' => ['required', 'string', 'max:255'],
            'delivery_address2' => ['nullable', 'string', 'max:255'],
            'delivery_zip' => ['nullable', 'string', 'max:80'],
            'delivery_state' => ['nullable', 'string', 'max:120'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (! Schema::hasColumn('city_shipping_costs', 'origin_city_id')) {
            return response()->json([
                'status' => false,
                'message' => 'Location shipping rates are not configured.',
            ], 404);
        }

        $data = $validator->validated();
        if ($response = $this->resolveLocationInput($data)) {
            return $response;
        }

        $weight = (float) $data['weight'];
        $freightMode = $request->input('freight_mode', $request->input('mode'));
        $freightMode = $freightMode ? ucfirst(strtolower($freightMode)) : null;
        $rate = $this->locationShippingCost($data['origin_city_id'], $data['destination_city_id'], $weight, $freightMode);

        if (! $rate) {
            return response()->json([
                'status' => false,
                'message' => 'No location shipping rate found for this cargo route, freight mode, and weight.',
            ], 422);
        }

        $originCity = City::with('country:id,name,iso2')->find($data['origin_city_id']);
        $destinationCity = City::with('country:id,name,iso2')->find($data['destination_city_id']);
        $shippingCost = $this->locationShippingCostPrice($rate, $weight);

        $order = DB::transaction(function () use ($request, $data, $rate, $originCity, $destinationCity, $shippingCost, $freightMode) {
            $order = new Order();
            $order->customer_id = optional(optional($request->user())->customer)->id;
            $order->tracking_id = $this->uniqueTrackingId('SIOCARGO');
            $order->status = OrderStatus::UNPAID;
            $order->provider = 'SIOPAY_LOCATION_RATE_CARGO';
            $order->freight_mode = $freightMode ?: $rate->freight_mode;
            $order->shipping_cost = $shippingCost;
            $order->return_date = $data['booking_date'];
            $order->cond_of_goods = 'Cargo Booking Request';
            $order->val_of_goods = $data['cargo_value'] ?? null;

            $order->pickup_location_country_id = $originCity ? $originCity->country_id : null;
            $order->pickup_location_city_id = $data['origin_city_id'];
            $order->delivery_location_country_id = $destinationCity ? $destinationCity->country_id : null;
            $order->delivery_location_city_id = $data['destination_city_id'];
            $order->current_location_country_id = $originCity ? $originCity->country_id : null;
            $order->current_location_city_id = $data['origin_city_id'];

            $order->pickup_name = $data['pickup_name'];
            $order->pickup_phone = $data['pickup_phone'];
            $order->pickup_email = $data['pickup_email'] ?? null;
            $order->pickup_address1 = $data['pickup_address1'];
            $order->pickup_address2 = $data['pickup_address2'] ?? null;
            $order->pickup_zip = $data['pickup_zip'] ?? null;
            $order->pickup_city = $originCity ? $originCity->name : null;
            $order->pickup_state = $data['pickup_state'] ?? null;
            $order->pickup_country = $originCity && $originCity->country ? $originCity->country->name : null;

            $order->delivery_name = $data['delivery_name'];
            $order->delivery_phone = $data['delivery_phone'];
            $order->delivery_email = $data['delivery_email'] ?? null;
            $order->delivery_address1 = $data['delivery_address1'];
            $order->delivery_address2 = $data['delivery_address2'] ?? null;
            $order->delivery_zip = $data['delivery_zip'] ?? null;
            $order->delivery_city = $destinationCity ? $destinationCity->name : null;
            $order->delivery_state = $data['delivery_state'] ?? null;
            $order->delivery_country = $destinationCity && $destinationCity->country ? $destinationCity->country->name : null;
            $order->save();

            $package = new OrderPackage();
            $package->order_id = $order->id;
            $package->type = 'cargo';
            $package->length = $data['length'] ?? 0;
            $package->width = $data['width'] ?? 0;
            $package->height = $data['height'] ?? 0;
            $package->weight = $data['weight'];
            $package->qty = $data['quantity'] ?? 1;
            $package->item_desc = $data['cargo_description'];
            $package->item_value = $data['cargo_value'] ?? 0;
            $package->save();

            return $order;
        });

        return response()->json([
            'status' => true,
            'message' => 'Cargo booking request created successfully.',
            'data' => [
                'order_id' => $order->id,
                'tracking_id' => $order->tracking_id,
                'booking_supported' => true,
                'rate_id' => $rate->id,
                'freight_mode' => $order->freight_mode,
                'shipping_cost' => $shippingCost,
                'booking_date' => $data['booking_date'],
                'origin' => $this->cityPayload($originCity),
                'destination' => $this->cityPayload($destinationCity),
            ],
        ], 201);
    }

    protected function normalizeLocationNameInputs(Request $request): void
    {
        $aliases = [
            'origin_country' => ['origin_country_name', 'from_country', 'ship_from_country'],
            'origin_city' => ['origin_city_name', 'from_city', 'ship_from_city'],
            'destination_country' => ['destination_country_name', 'to_country', 'ship_to_country'],
            'destination_city' => ['destination_city_name', 'to_city', 'ship_to_city'],
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

    protected function resolveLocationInput(array &$data): ?JsonResponse
    {
        foreach (['origin', 'destination'] as $side) {
            $cityIdKey = $side . '_city_id';

            if (! empty($data[$cityIdKey])) {
                continue;
            }

            $country = $this->findCountryByNameOrCode($data[$side . '_country'] ?? null);

            if (! $country) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        $side . '_country' => ['The selected ' . str_replace('_', ' ', $side) . ' country was not found.'],
                    ],
                ], 422);
            }

            $city = $this->findCityByNameAndCountry($data[$side . '_city'] ?? null, $country->id);

            if (! $city) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        $side . '_city' => ['The selected ' . str_replace('_', ' ', $side) . ' city was not found for ' . $country->name . '.'],
                    ],
                ], 422);
            }

            $data[$cityIdKey] = $city->id;
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

    protected function locationShippingCost($originCityId, $destinationCityId, float $weight, ?string $freightMode = null): ?CityShippingCost
    {
        $query = CityShippingCost::query()
            ->where('origin_city_id', $originCityId)
            ->where('destination_city_id', $destinationCityId)
            ->where(function ($query) use ($weight) {
                $query->whereNull('weight')
                    ->orWhere('weight', '>=', $weight);
            });

        if ($freightMode && Schema::hasColumn('city_shipping_costs', 'freight_mode')) {
            $query->where('freight_mode', $freightMode);
        }

        return $query
            ->orderByRaw('CASE WHEN weight IS NULL THEN 1 ELSE 0 END')
            ->orderBy('weight')
            ->first();
    }

    protected function locationShippingCostPrice(CityShippingCost $rate, float $weight): float
    {
        if ($rate->distance_km && $rate->rate_per_km_per_kg) {
            return round((float) $rate->distance_km * (float) $rate->rate_per_km_per_kg * $weight, 2);
        }

        return round((float) $rate->cost, 2);
    }

    protected function cityPayload(?City $city): ?array
    {
        if (! $city) {
            return null;
        }

        return [
            'city_id' => $city->id,
            'city' => $city->name,
            'country_id' => $city->country_id,
            'country' => $city->country ? $city->country->name : null,
            'country_iso_2' => $city->country ? $city->country->iso2 : null,
        ];
    }

    protected function uniqueTrackingId(string $prefix): string
    {
        do {
            $trackingId = $prefix . now()->format('YmdHis') . random_int(1000, 9999);
        } while (Order::where('tracking_id', $trackingId)->exists());

        return $trackingId;
    }
}
