<?php

namespace App\Http\Controllers\Api;

use App\Constraints\OrderStatus;
use App\Constraints\PreferredCurrency;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\CityShippingCost;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderPackage;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PickupRequestController extends Controller
{
    public function store(Request $request)
    {
        if ($request->filled('currency')) {
            $request->merge(['currency' => strtoupper(trim((string) $request->input('currency')))]);
        }

        $validated = $request->validate([
            'pickup_location_id' => 'required',
            'destination_location_id' => 'required',
            'pickup_date' => 'required|date',
            'currency' => 'nullable|in:' . PreferredCurrency::EUR . ',' . PreferredCurrency::NGN,
            'weight' => 'required|numeric|min:0.1',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'count' => 'nullable|integer|min:1',
            'item_desc' => 'nullable|string',
            'item_value' => 'nullable|numeric|min:0',
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

        // Pickup is supported when a location shipping rate exists for the route + weight.
        $rate = $this->locationShippingCost(
            $validated['pickup_location_id'],
            $validated['destination_location_id'],
            (float) $validated['weight']
        );

        if (! $rate) {
            return response()->json([
                'status' => false,
                'message' => 'Pickup is not supported for this route and package weight.',
            ], 422);
        }

        // A delivery man is assigned when a vehicle covers the route; otherwise it's assigned later.
        $vehicle = $this->supportedVehicle(
            $validated['pickup_location_id'],
            $validated['destination_location_id'],
            (float) $validated['weight']
        );

        // Price from the location rate's currency cost (cost_eur / cost_naira) per the chosen currency.
        $currency = $validated['currency'] ?? PreferredCurrency::DEFAULT;
        $amount = $currency === PreferredCurrency::NGN ? $rate->cost_naira : $rate->cost_eur;

        if ($amount === null) {
            return response()->json([
                'status' => false,
                'message' => 'This route is not priced in ' . $currency . '. Please choose another currency.',
            ], 422);
        }

        $shippingCost = round((float) $amount, 2);

        $pickupCity = City::find($validated['pickup_location_id']);
        $destinationCity = City::find($validated['destination_location_id']);

        // Ensure the booking user has a customer record so the order is owned by them
        // (required for the payment ownership check on /api/payments/initialize).
        $customer = Customer::firstOrCreate(['user_id' => $request->user()->id]);

        $order = DB::transaction(function () use ($validated, $vehicle, $shippingCost, $currency, $pickupCity, $destinationCity, $customer) {
            $order = new Order();
            $order->customer_id = $customer->id;
            $order->dispatcher_id = optional($vehicle)->delivery_man_id;
            $order->pickup_location_id = $validated['pickup_location_id'];
            $order->delivery_location_id = $validated['destination_location_id'];
            $order->current_location_id = $validated['pickup_location_id'];

            // Populate the city/country fields used by the order detail map and the admin order lists.
            $order->pickup_location_city_id = optional($pickupCity)->id;
            $order->pickup_location_country_id = optional($pickupCity)->country_id;
            $order->delivery_location_city_id = optional($destinationCity)->id;
            $order->delivery_location_country_id = optional($destinationCity)->country_id;
            $order->current_location_city_id = optional($pickupCity)->id;
            $order->current_location_country_id = optional($pickupCity)->country_id;

            $order->shipping_rate_id = null; // shipping_rates table no longer used; priced via city_shipping_costs
            $order->tracking_id = 'SIOPICKUP' . time() . Str::upper(Str::random(6));
            $order->status = OrderStatus::UNPAID;
            $order->provider = 'SIOPAY';
            $order->shipping_cost = $shippingCost;
            $order->shipping_currency = $currency;
            $order->return_date = $validated['pickup_date'];

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
            $package->type = 'parcel';
            $package->length = $validated['length'] ?? 0;
            $package->width = $validated['width'] ?? 0;
            $package->height = $validated['height'] ?? 0;
            $package->weight = $validated['weight'];
            $package->qty = $validated['count'] ?? 1;
            $package->item_desc = $validated['item_desc'] ?? 'Pickup request';
            $package->item_value = $validated['item_value'] ?? 0;
            $package->save();

            return $order;
        });

        return response()->json([
            'status' => true,
            'message' => 'Pickup request created successfully.',
            'data' => [
                'tracking_id' => $order->tracking_id,
                'pickup_supported' => true,
                'delivery_man_id' => optional($vehicle)->delivery_man_id,
                'vehicle_id' => optional($vehicle)->id,
                'shipping_cost' => $shippingCost,
                'currency' => $currency,
            ],
        ], 201);
    }

    protected function supportedVehicle($pickupLocationId, $destinationLocationId, float $weight): ?Vehicle
    {
        return Vehicle::query()
            ->where('status', Vehicle::STATUS_ACTIVE)
            ->where('origin_location', $pickupLocationId)
            ->where('destination_location', $destinationLocationId)
            ->whereNotNull('weight_range')
            ->where(function ($query) use ($weight) {
                $query->whereRaw('CAST(SUBSTRING_INDEX(weight_range, \'-\', 1) AS DECIMAL(10,2)) <= ?', [$weight])
                    ->whereRaw('CAST(SUBSTRING_INDEX(weight_range, \'-\', -1) AS DECIMAL(10,2)) >= ?', [$weight]);
            })
            ->first();
    }

    protected function locationShippingCost($originCityId, $destinationCityId, float $weight): ?CityShippingCost
    {
        return CityShippingCost::query()
            ->where('origin_city_id', $originCityId)
            ->where('destination_city_id', $destinationCityId)
            ->where(function ($query) use ($weight) {
                $query->whereNull('weight')
                    ->orWhere('weight', '>=', $weight);
            })
            ->orderByRaw('CASE WHEN weight IS NULL THEN 1 ELSE 0 END')
            ->orderBy('weight')
            ->first();
    }

    protected function locationShippingCostPrice(CityShippingCost $rate, float $weight): float
    {
        if (Schema::hasColumn('city_shipping_costs', 'distance_km')
            && Schema::hasColumn('city_shipping_costs', 'rate_per_km_per_kg')
            && $rate->distance_km && $rate->rate_per_km_per_kg) {
            return round((float) $rate->distance_km * (float) $rate->rate_per_km_per_kg * $weight, 2);
        }

        return round((float) $rate->cost, 2);
    }
}
