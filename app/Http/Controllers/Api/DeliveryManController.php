<?php

// app/Http/Controllers/Api/DeliveryManController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryMan;
use App\Models\Vehicle;

class DeliveryManController extends Controller
{
    public function availableDeliveryMen(Request $request)
    {
        $request->validate([
            'pickup_location_id' => 'required',
            'destination_location_id' => 'required',
            'weight' => 'required|numeric|min:0.1',
        ]);

        $pickupLocationId = $request->pickup_location_id;
        $destinationLocationId = $request->destination_location_id;
        $weight = $request->weight;

        /*
        |--------------------------------------------------------------------------
        | Conditions:
        | 1. Vehicle must be active
        | 2. Route must match
        | 3. Weight must fall within vehicle weight range
        |--------------------------------------------------------------------------
        */

        $deliveryMen = Vehicle::query()

            ->where('status', 'active')

            ->where('origin_location', $pickupLocationId)

            ->where('destination_location', $destinationLocationId)

            /*
            |--------------------------------------------------------------------------
            | Example weight_range values:
            | 0-5
            | 5-10
            | 10-20
            |--------------------------------------------------------------------------
            */
            ->where(function ($query) use ($weight) {

                $query->whereRaw("
                    CAST(SUBSTRING_INDEX(weight_range, '-', 1) AS DECIMAL(10,2)) <= ?
                ", [$weight])

                ->whereRaw("
                    CAST(SUBSTRING_INDEX(weight_range, '-', -1) AS DECIMAL(10,2)) >= ?
                ", [$weight]);
            })

            ->select([
                'id',
                'delivery_man_id',
                'type',
                'brand',
                'model',
                'plate_number',
                'color',
                'year',
                'origin_location',
                'destination_location',
                'weight_range',
                'rate_per_km_per_kg',
                'rate_eur',
                'rate_naira',
                'distance_km',
                'status'
            ])

            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Available delivery men fetched successfully.',
            'total' => $deliveryMen->count(),
            'data' => $deliveryMen
        ]);
    }
}