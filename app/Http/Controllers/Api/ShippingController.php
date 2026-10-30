<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\CityShippingCost;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShippingController extends Controller
{
    public function calculateShipping(Request $request)
    {
        $request->validate([
            // Each location may be supplied as a name OR an id (the *_id field, or a numeric value).
            'ship_from_country' => 'required_without:ship_from_country_id',
            'ship_from_city' => 'required_without:ship_from_city_id',
            'ship_to_country' => 'required_without:ship_to_country_id',
            'ship_to_city' => 'required_without:ship_to_city_id',
            'ship_from_country_id' => 'nullable|integer|exists:countries,id',
            'ship_from_city_id' => 'nullable|integer|exists:cities,id',
            'ship_to_country_id' => 'nullable|integer|exists:countries,id',
            'ship_to_city_id' => 'nullable|integer|exists:cities,id',
            'freight_mode' => 'nullable|in:Air,Sea,Road,air,sea,road,AIR,SEA,ROAD',
            'mode' => 'nullable|in:Air,Sea,Road,air,sea,road,AIR,SEA,ROAD',
            'weightTotal' => 'required|numeric',
            'heightTotal' => 'required|numeric',
            'widthTotal' => 'required|numeric',
            'lengthTotal' => 'required|numeric',
            'valueTotal' => 'required|numeric',
            'countTotal' => 'required|numeric',
        ]);

        // The rates-fetch service matches on ids, so resolve any supplied names to ids first.
        $shipFromCountryId = $this->resolveCountryId($request, 'ship_from_country');
        $shipFromCityId = $this->resolveCityId($request, 'ship_from_city', $shipFromCountryId);
        $shipToCountryId = $this->resolveCountryId($request, 'ship_to_country');
        $shipToCityId = $this->resolveCityId($request, 'ship_to_city', $shipToCountryId);

        $unresolved = collect([
            'ship_from_country' => $shipFromCountryId,
            'ship_from_city' => $shipFromCityId,
            'ship_to_country' => $shipToCountryId,
            'ship_to_city' => $shipToCityId,
        ])->filter(fn ($id) => empty($id))->keys();

        if ($unresolved->isNotEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Could not resolve the supplied location(s). Check the country/city name or id.',
                'unresolved' => $unresolved->values(),
            ], 422);
        }

        try {
            $freightMode = $request->input('freight_mode', $request->input('mode'));
            $freightMode = $freightMode ? ucfirst(strtolower($freightMode)) : null;

            $currencyCosts = $this->currencyCosts($shipFromCityId, $shipToCityId, (float) $request->weightTotal, $freightMode);

            $response = Http::timeout(30)
                ->get('https://www.sioshipping.eu/rates-fetch', [
                    'ship_from_country' => $shipFromCountryId,
                    'ship_from_city' => $shipFromCityId,
                    'ship_to_country' => $shipToCountryId,
                    'ship_to_city' => $shipToCityId,
                    'freight_mode' => $freightMode,
                    'origin' => config('app.url'),

                    'weightTotal' => $request->weightTotal,
                    'heightTotal' => $request->heightTotal,
                    'widthTotal' => $request->widthTotal,
                    'lengthTotal' => $request->lengthTotal,
                    'valueTotal' => $request->valueTotal,
                    'countTotal' => $request->countTotal,
                ]);

            if (!$response->successful()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unable to fetch shipping rates',
                    'error' => $response->body()
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'Shipping rates fetched successfully',
                'freight_mode' => $freightMode,
                'currency_costs' => $currencyCosts,
                'data' => $response->json()
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Shipping API error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Look up the local location shipping rate for the route and return its EUR / NGN costs.
     */
    protected function currencyCosts($originCityId, $destinationCityId, float $weight, ?string $freightMode): array
    {
        $rate = CityShippingCost::query()
            ->where('origin_city_id', $originCityId)
            ->where('destination_city_id', $destinationCityId)
            ->where(function ($query) use ($weight) {
                $query->whereNull('weight')->orWhere('weight', '>=', $weight);
            })
            ->when($freightMode, fn ($query) => $query->where('freight_mode', $freightMode))
            ->orderByRaw('CASE WHEN weight IS NULL THEN 1 ELSE 0 END')
            ->orderBy('weight')
            ->first();

        return [
            'EUR' => $rate && $rate->cost_eur !== null ? (float) $rate->cost_eur : null,
            'NGN' => $rate && $rate->cost_naira !== null ? (float) $rate->cost_naira : null,
        ];
    }

    /**
     * Resolve a country to its id from the `{key}_id` field, a numeric `{key}` value, or a country name/ISO code.
     */
    protected function resolveCountryId(Request $request, string $key): ?int
    {
        if ($request->filled($key . '_id')) {
            return (int) $request->input($key . '_id');
        }

        $value = trim((string) $request->input($key));

        if ($value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $needle = strtolower($value);

        $country = Country::query()
            ->whereRaw('LOWER(name) = ?', [$needle])
            ->orWhereRaw('LOWER(iso2) = ?', [$needle])
            ->orWhereRaw('LOWER(iso3) = ?', [$needle])
            ->first();

        return optional($country)->id;
    }

    /**
     * Resolve a city to its id from the `{key}_id` field, a numeric `{key}` value, or a city name (within the country).
     */
    protected function resolveCityId(Request $request, string $key, ?int $countryId): ?int
    {
        if ($request->filled($key . '_id')) {
            return (int) $request->input($key . '_id');
        }

        $value = trim((string) $request->input($key));

        if ($value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $query = City::query()->whereRaw('LOWER(name) = ?', [strtolower($value)]);

        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        return optional($query->first())->id;
    }
}
