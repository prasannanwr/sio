<?php

namespace App\Http\Controllers;

use App\Models\Batchlog;
use App\Models\BatchorderLog;
use App\Models\City;
use App\Models\Country;
use App\Models\Dispatcher;
use App\Models\Order;
use App\Models\OrderBatch;
use App\Models\State;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function getCities($stateId)
    {
        $cities = City::select('name', 'id')->where('state_id', $stateId)->orderBy('name')->get();
        return response()->json([
            'success' => true,
            'cities' => $cities->toArray(),
        ], 200);
    }

    public function getCountryCities($countryId)
    {
        $cities = City::select('name', 'id')->where('country_id', $countryId)->orderBy('name')->get();
        return response()->json([
            'success' => true,
            'cities' => $cities->toArray(),
        ], 200);
    }

    public function getCountryState($countryId)
    {
        $states = State::select('name', 'id')->where('country_id', $countryId)->orderBy('name')->get();
        return response()->json([
            'success' => true,
            'cities' => $states->toArray(),
        ], 200);
    }


    public function getOrder()
    {
        $ordres = Order::select('id', 'tracking_id')->where('batch_id', null)->get();

        // return $ordres;
        return response()->json([
            'success' => true,
            'ordres' => $ordres->toArray(),
        ], 200);
    }


    public function editCountriy()
    {
        $countries = Country::select('name', 'id')->get();
        return response()->json([
            'success' => true,
            'countries' => $countries->toArray(),
        ], 200);
    }

    //SIO1725136407
    public function getBatchLogs(Request $request)
    {
        $order = Order::where('tracking_id', $request->tarckingId)->first();
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => "Order not found for the given tracking ID",
            ]);
        }

        if (!$order->batch_id) {
            return response()->json([
                'success' => false,
                'message' => "No batch assigned to this order yet",
            ]);
        }

        $batch = OrderBatch::where('id', $order->batch_id)->first();
        if (!$batch) {
            return response()->json([
                'success' => false,
                'message' => "Batch not found",
            ]);
        }

        $batch_logs = Batchlog::where('batch_id', $batch->id)->get();
        if ($batch_logs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "No tracking logs available for this batch",
            ]);
        }

        $data[] = [
            'country_name' => Country::where('id', $batch_logs[0]->ship_to_country_id)->first()->name,
            'city_name' => City::where('id', $batch_logs[0]->ship_to_city_id)->first()->name,
            'created_at' => $batch_logs[0]->created_at,
            'status' => $batch->status,
            'type' => 'Delivery'
        ];
        $data[] = [
            'country_name' => Country::where('id', $batch_logs[0]->ship_from_country_id)->first()->name,
            'city_name' => City::where('id', $batch_logs[0]->ship_from_city_id)->first()->name,
            'created_at' => $batch_logs[0]->created_at,
            'type' => "Pickup"
        ];

        foreach ($batch_logs as $batch_log) {
            $data[] = [
                'country_name' => Country::where('id', $batch_log->current_location_county_id)->first()->name,
                'city_name' => City::where('id', $batch_log->current_location_city_id)->first()->name,
                'created_at' => $batch_log->created_at,
                'type' => "Current"
            ];
        }

        return response()->json([
            'success' => true,
            'logs' => $data,
        ]);
    }
}
