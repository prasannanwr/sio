<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    public function mine(Request $request): JsonResponse
    {
        $vehicles = Vehicle::where('delivery_man_id', $request->user()->id)
            ->with('shippingCost')
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Assigned vehicles retrieved successfully.',
            'total' => $vehicles->count(),
            'data' => $vehicles->map(function (Vehicle $vehicle) {
                return $this->vehiclePayload($vehicle, true);
            })->values(),
        ]);
    }

    public function rates(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'origin_location' => 'required|integer|exists:cities,id',
            'destination_location' => 'required|integer|exists:cities,id',
            'weight' => 'required|numeric|min:0',
            'min_weight' => 'nullable|numeric|min:0',
            'max_weight' => 'nullable|numeric|min:0|gte:min_weight',
            'vehicle_type' => 'nullable|in:' . Vehicle::TYPE_VAN . ',' . Vehicle::TYPE_TRUCK,
        ]);

        $weight = isset($validated['weight']) ? (float) $validated['weight'] : null;
        $minWeight = isset($validated['min_weight']) ? (float) $validated['min_weight'] : null;
        $maxWeight = isset($validated['max_weight']) ? (float) $validated['max_weight'] : null;

        $query = Vehicle::query()
            ->where('status', Vehicle::STATUS_ACTIVE)
            //delivery filter by van or truck
            //->whereIn('type', [Vehicle::TYPE_VAN, Vehicle::TYPE_TRUCK])
            ->where('origin_location', $validated['origin_location'])
            ->where('destination_location', $validated['destination_location'])
            ->whereNotNull('weight_range')
            // Only offer vehicles that have a delivery man, otherwise the resulting booking
            // could not be assigned to anyone and would never reach a delivery man's orders.
            ->whereNotNull('delivery_man_id')
            ->with('shippingCost');

        if (! empty($validated['vehicle_type'])) {
            $query->where('type', $validated['vehicle_type']);
        }

        // Match the requested weight (or weight range) against each vehicle's stored "min-max" weight_range.
        if ($weight !== null) {
            $query->whereRaw('CAST(SUBSTRING_INDEX(weight_range, \'-\', 1) AS DECIMAL(10,2)) <= ?', [$weight])
                ->whereRaw('CAST(SUBSTRING_INDEX(weight_range, \'-\', -1) AS DECIMAL(10,2)) >= ?', [$weight]);
        } elseif ($minWeight !== null || $maxWeight !== null) {
            if ($maxWeight !== null) {
                $query->whereRaw('CAST(SUBSTRING_INDEX(weight_range, \'-\', 1) AS DECIMAL(10,2)) <= ?', [$maxWeight]);
            }
            if ($minWeight !== null) {
                $query->whereRaw('CAST(SUBSTRING_INDEX(weight_range, \'-\', -1) AS DECIMAL(10,2)) >= ?', [$minWeight]);
            }
        }

        $vehicles = $query
            ->orderByRaw("FIELD(type, '" . Vehicle::TYPE_TRUCK . "', '" . Vehicle::TYPE_VAN . "')")
            ->get();

        $origin = City::with('country')->find($validated['origin_location']);
        $destination = City::with('country')->find($validated['destination_location']);

        return response()->json([
            'status' => true,
            'message' => 'Cargo/vehicle rates retrieved successfully.',
            'total' => $vehicles->count(),
            'query' => [
                'origin_location' => $this->locationPayload($origin),
                'destination_location' => $this->locationPayload($destination),
                'weight' => $weight,
                'min_weight' => $minWeight,
                'max_weight' => $maxWeight,
                'vehicle_type' => $validated['vehicle_type'] ?? null,
            ],
            'data' => $vehicles->map(function (Vehicle $vehicle) use ($weight) {
                return $this->ratePayload($vehicle, $weight);
            })->values(),
        ]);
    }

    public function searchLocations(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        $query = DB::table('cities')
            ->join('countries', 'cities.country_id', '=', 'countries.id')
            ->select(
                'cities.id',
                'cities.name as city_name',
                'countries.name as country_name'
            );

        if ($term !== '') {
            $query->where(function ($query) use ($term) {
                $query->where('cities.name', 'like', "%{$term}%")
                    ->orWhere('countries.name', 'like', "%{$term}%");
            });
        }

        $results = $query
            ->orderBy('cities.name')
            ->limit(25)
            ->get()
            ->map(function ($location) {
                return [
                    'id' => $location->id,
                    'text' => $location->city_name . ', ' . $location->country_name,
                    'city_name' => $location->city_name,
                    'country_name' => $location->country_name,
                ];
            })
            ->values();

        return response()->json([
            'status' => true,
            'message' => 'Vehicle locations retrieved successfully.',
            'results' => $results,
            'data' => $results,
        ]);
    }

    protected function vehiclePayload(Vehicle $vehicle, bool $includeDocuments = false): array
    {
        return [
            'id' => $vehicle->id,
            'type' => $vehicle->type,
            'brand' => $vehicle->brand,
            'model' => $vehicle->model,
            'plate_number' => $vehicle->plate_number,
            'color' => $vehicle->color,
            'year' => $vehicle->year,
            'status' => $vehicle->status,
            'shipping_rate' => $this->shippingCostPayload($vehicle),
            'documents' => $includeDocuments ? $this->documentsPayload($vehicle) : [],
            'created_at' => optional($vehicle->created_at)->toIso8601String(),
            'updated_at' => optional($vehicle->updated_at)->toIso8601String(),
        ];
    }

    protected function ratePayload(Vehicle $vehicle, ?float $weight): array
    {
        return [
            'id' => $vehicle->id,
            'type' => $vehicle->type,
            'brand' => $vehicle->brand,
            'model' => $vehicle->model,
            'plate_number' => $vehicle->plate_number,
            'delivery_man_id' => $vehicle->delivery_man_id,
            'origin_location' => $vehicle->origin_location,
            'destination_location' => $vehicle->destination_location,
            'weight_range' => $vehicle->weight_range,
            'rate_per_km_per_kg' => $vehicle->rate_per_km_per_kg,
            'rate_eur' => $vehicle->rate_eur,
            'rate_naira' => $vehicle->rate_naira,
            'distance_km' => $vehicle->distance_km,
            'total_cost' => $weight !== null ? $this->totalCost($vehicle, $vehicle->rate_per_km_per_kg, $weight) : null,
            // Per-currency pricing so the user can choose EUR or Naira. Null when the
            // delivery man did not set a price in that currency.
            'currency_rates' => [
                // Fall back to the legacy per-km rate as EUR when no explicit EUR rate is set.
                'EUR' => $this->currencyRatePayload($vehicle, $vehicle->rate_eur ?? $vehicle->rate_per_km_per_kg, $weight),
                'NGN' => $this->currencyRatePayload($vehicle, $vehicle->rate_naira, $weight),
            ],
            'shipping_rate' => $this->shippingCostPayload($vehicle),
            'status' => $vehicle->status,
        ];
    }

    protected function currencyRatePayload(Vehicle $vehicle, $rate, ?float $weight): array
    {
        return [
            'available' => $rate !== null,
            'rate_per_km_per_kg' => $rate !== null ? (float) $rate : null,
            'total_cost' => $weight !== null ? $this->totalCost($vehicle, $rate, $weight) : null,
        ];
    }

    // total_cost = distance_km * rate * weight
    protected function totalCost(Vehicle $vehicle, $rate, float $weight): ?float
    {
        if (! $vehicle->distance_km || ! $rate) {
            return null;
        }

        return round((float) $vehicle->distance_km * (float) $rate * $weight, 3);
    }

    protected function locationPayload(?City $city): ?array
    {
        if (! $city) {
            return null;
        }

        return [
            'id' => $city->id,
            'city_name' => $city->name,
            'country_name' => optional($city->country)->name,
        ];
    }

    protected function shippingCostPayload(Vehicle $vehicle): ?array
    {
        if (! $vehicle->shippingCost) {
            return null;
        }

        return [
            'id' => $vehicle->shippingCost->id,
            'label' => $vehicle->shippingCost->display_label,
            'weight' => $vehicle->shippingCost->weight,
            'country_name' => $vehicle->shippingCost->country_name,
            'country_iso_2' => $vehicle->shippingCost->country_iso_2,
            'cost' => $vehicle->shippingCost->cost,
        ];
    }

    protected function documentsPayload(Vehicle $vehicle): array
    {
        return collect($vehicle->documents ?: [])->map(function (array $document) {
            return [
                'name' => $document['name'] ?? basename($document['path'] ?? ''),
                'url' => ! empty($document['path']) ? asset($document['path']) : null,
                'mime' => $document['mime'] ?? null,
                'size' => $document['size'] ?? null,
                'uploaded_at' => $document['uploaded_at'] ?? null,
            ];
        })->values()->all();
    }
}
