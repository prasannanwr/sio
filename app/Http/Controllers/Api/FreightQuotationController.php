<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FreightQuotationRequest;
use App\Models\ShippingRate;
use App\Models\Vehicle;

class FreightQuotationController extends Controller
{
    public function store(FreightQuotationRequest $request)
    {
        $validated = $request->validated();
        $mode = $request->freightMode();

        $rate = $this->matchingRate($validated);
        $vehicle = null;
        $shippingCost = optional($rate)->price;

        if ($mode === FreightQuotationRequest::MODE_ROAD) {
            $vehicle = $this->supportedRoadVehicle(
                $validated['pickup_location_id'],
                $validated['destination_location_id'],
                (float) $validated['weight'],
                $validated['vehicle_type'] ?? null
            );

            $shippingCost = $vehicle
                ? $this->roadShippingCost($vehicle, $rate, (float) $validated['weight'])
                : null;
        }

        $supported = $shippingCost !== null;

        return response()->json([
            'status' => $supported,
            'message' => $supported
                ? 'Freight quotation fetched successfully.'
                : 'Freight quotation is not supported for this route, mode, and cargo weight.',
            'data' => [
                'freight_mode' => $mode,
                'booking_supported' => $supported,
                'pickup_location_id' => $validated['pickup_location_id'],
                'destination_location_id' => $validated['destination_location_id'],
                'weight' => (float) $validated['weight'],
                'length' => isset($validated['length']) ? (float) $validated['length'] : null,
                'width' => isset($validated['width']) ? (float) $validated['width'] : null,
                'height' => isset($validated['height']) ? (float) $validated['height'] : null,
                'quantity' => (int) ($validated['quantity'] ?? 1),
                'cargo_value' => isset($validated['cargo_value']) ? (float) $validated['cargo_value'] : null,
                'shipping_rate_id' => optional($rate)->id,
                'shipping_cost' => $shippingCost,
                'vehicle' => $vehicle ? [
                    'id' => $vehicle->id,
                    'delivery_man_id' => $vehicle->delivery_man_id,
                    'type' => $vehicle->type,
                    'distance_km' => $vehicle->distance_km,
                    'rate_per_km_per_kg' => $vehicle->rate_per_km_per_kg,
                ] : null,
            ],
        ], $supported ? 200 : 422);
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

    protected function supportedRoadVehicle($pickupLocationId, $destinationLocationId, float $weight, ?string $vehicleType): ?Vehicle
    {
        $query = Vehicle::query()
            ->where('status', Vehicle::STATUS_ACTIVE)
            ->whereIn('type', [Vehicle::TYPE_VAN, Vehicle::TYPE_TRUCK])
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

        return $query->orderByRaw("FIELD(type, '" . Vehicle::TYPE_TRUCK . "', '" . Vehicle::TYPE_VAN . "')")
            ->first();
    }

    protected function roadShippingCost(Vehicle $vehicle, ?ShippingRate $rate, float $weight): ?float
    {
        if ($vehicle->distance_km && $vehicle->rate_per_km_per_kg) {
            return round((float) $vehicle->distance_km * (float) $vehicle->rate_per_km_per_kg * $weight, 2);
        }

        return optional($rate)->price;
    }
}
