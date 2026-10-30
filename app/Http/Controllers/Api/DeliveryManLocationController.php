<?php

namespace App\Http\Controllers\Api;

use App\Constraints\DeliveryManStatus;
use App\Constraints\DeliveryTrackingStatus;
use App\Constraints\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\DeliveryManLocation;
use App\Models\DeliveryManLocationPing;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class DeliveryManLocationController extends Controller
{
    /**
     * Delivery man app pushes background GPS coordinates (recommended every 60-120 seconds).
     * Accepts either a single point or a batch of buffered points:
     *   single: { latitude, longitude, timestamp? }
     *   batch:  { locations: [ { latitude, longitude, timestamp? }, ... ] }
     * POST /api/deliveries/location
     */
    public function update(Request $request): JsonResponse
    {
        $isBatch = $request->has('locations');

        $rules = $isBatch
            ? [
                'delivery_man_id' => ['nullable', 'integer', 'exists:users,id'],
                'locations' => ['required', 'array', 'min:1', 'max:500'],
                'locations.*.latitude' => ['required', 'numeric', 'between:-90,90'],
                'locations.*.longitude' => ['required', 'numeric', 'between:-180,180'],
                'locations.*.timestamp' => ['nullable', 'date'],
            ]
            : [
                'delivery_man_id' => ['nullable', 'integer', 'exists:users,id'],
                'latitude' => ['required', 'numeric', 'between:-90,90'],
                'longitude' => ['required', 'numeric', 'between:-180,180'],
                'timestamp' => ['nullable', 'date'],
            ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // A delivery man may only report their own location; admins may post on behalf of one.
        $deliveryManId = $user->id;
        if ($user->user_type === 'admin' && $request->filled('delivery_man_id')) {
            $deliveryManId = (int) $request->input('delivery_man_id');
        }

        $points = $isBatch
            ? collect($request->input('locations'))
            : collect([$request->only(['latitude', 'longitude', 'timestamp'])]);

        // Normalize + sort chronologically so the "latest" reflects the newest reading.
        $points = $points->map(function ($point) {
            return [
                'delivery_man_id' => null, // set below
                'latitude' => $point['latitude'],
                'longitude' => $point['longitude'],
                'recorded_at' => ! empty($point['timestamp']) ? Carbon::parse($point['timestamp']) : now(),
            ];
        })->sortBy(fn ($p) => $p['recorded_at']->getTimestamp())->values();

        $rows = $points->map(function ($p) use ($deliveryManId) {
            return [
                'delivery_man_id' => $deliveryManId,
                'latitude' => $p['latitude'],
                'longitude' => $p['longitude'],
                'recorded_at' => $p['recorded_at'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->all();

        // Record every ping for the trail/history.
        DeliveryManLocationPing::insert($rows);

        // Keep the single "current" location pointed at the most recent reading.
        $latest = $points->last();
        $location = DeliveryManLocation::updateOrCreate(
            ['delivery_man_id' => $deliveryManId],
            [
                'latitude' => $latest['latitude'],
                'longitude' => $latest['longitude'],
                'recorded_at' => $latest['recorded_at'],
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Location updated successfully.',
            'recorded' => count($rows),
            'data' => $this->locationPayload($location),
        ]);
    }

    /**
     * Online delivery men within a radius of the given point (default 10 km).
     * "Online" = GPS updated within the freshness window (default 5 minutes).
     * GET /api/drivers/nearby?latitude=&longitude=&radius_km=
     */
    public function nearby(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_km' => ['nullable', 'numeric', 'min:0.1', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $lat = (float) $request->input('latitude');
        $lng = (float) $request->input('longitude');
        $radius = (float) ($request->input('radius_km') ?? 10);
        $onlineSince = now()->subMinutes((int) config('tracking.online_threshold_minutes', 5));

        // Haversine great-circle distance in kilometres (Earth radius 6371 km).
        $haversine = '(6371 * acos(least(1, cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))))';

        $drivers = DeliveryManLocation::query()
            ->select('delivery_man_locations.*')
            ->selectRaw($haversine . ' AS distance_km', [$lat, $lng, $lat])
            ->where('recorded_at', '>=', $onlineSince)
            ->whereHas('deliveryMan', function ($query) {
                $query->whereIn('user_type', ['dispatcher', 'courier'])
                    ->where('blocked', 0);
            })
            ->with('deliveryMan')
            ->having('distance_km', '<=', $radius)
            ->orderBy('distance_km')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Nearby online drivers retrieved successfully.',
            'total' => $drivers->count(),
            'radius_km' => $radius,
            'data' => $drivers->map(function (DeliveryManLocation $driver) {
                return [
                    'delivery_man_id' => $driver->delivery_man_id,
                    'name' => optional($driver->deliveryMan)->name,
                    'distance_km' => round((float) $driver->distance_km, 2),
                    'latitude' => (float) $driver->latitude,
                    'longitude' => (float) $driver->longitude,
                    'last_seen' => optional($driver->recorded_at)->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    /**
     * Customer polls the assigned delivery man's live location for an order.
     * GET /api/deliveries/track/{trackingId}/location
     */
    public function show(string $trackingId): JsonResponse
    {
        $order = Order::where('tracking_id', $trackingId)->first();

        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery was not found.',
            ], 404);
        }

        $trackingStatus = $this->trackingStatusFromValues($order->status, $order->delivery_man_status, $order->dispatcher_id);

        // Live location is only shared once the delivery man has accepted/engaged and before delivery completes.
        $trackingActive = in_array($trackingStatus, [DeliveryTrackingStatus::ACCEPTED, DeliveryTrackingStatus::ON_THE_WAY], true);

        if (! $trackingActive || ! $order->dispatcher_id) {
            return response()->json([
                'status' => true,
                'message' => 'Live location is not available for this delivery yet.',
                'data' => [
                    'tracking_id' => $order->tracking_id,
                    'tracking_status' => $trackingStatus,
                    'tracking_active' => false,
                    'location' => null,
                ],
            ]);
        }

        $location = DeliveryManLocation::where('delivery_man_id', $order->dispatcher_id)->first();

        return response()->json([
            'status' => true,
            'message' => $location
                ? 'Delivery man location retrieved successfully.'
                : 'Delivery man location is not available yet.',
            'data' => [
                'tracking_id' => $order->tracking_id,
                'tracking_status' => $trackingStatus,
                'tracking_active' => true,
                'location' => $location ? $this->locationPayload($location) : null,
            ],
        ]);
    }

    protected function locationPayload(DeliveryManLocation $location): array
    {
        return [
            'delivery_man_id' => $location->delivery_man_id,
            'latitude' => (float) $location->latitude,
            'longitude' => (float) $location->longitude,
            'recorded_at' => optional($location->recorded_at)->toIso8601String(),
            'updated_at' => optional($location->updated_at)->toIso8601String(),
        ];
    }

    protected function trackingStatusFromValues(?string $status, ?string $deliveryManStatus, $deliveryManId = null): string
    {
        if ($deliveryManStatus === DeliveryManStatus::DELIVERED
            || in_array($status, [OrderStatus::DELIVERED, OrderStatus::CUSTOMER_RECEIVED], true)) {
            return DeliveryTrackingStatus::DELIVERED;
        }

        if (in_array($deliveryManStatus, [DeliveryManStatus::ON_THE_WAY, DeliveryManStatus::ENGAGE_WITH_PARCEL, DeliveryManStatus::PICKED_UP, DeliveryManStatus::ARRIVED], true)
            || $status === OrderStatus::IN_TRANSIT) {
            return DeliveryTrackingStatus::ON_THE_WAY;
        }

        if ($deliveryManStatus === DeliveryManStatus::ACCEPTED
            || in_array($status, [OrderStatus::ASSIGNED, OrderStatus::IN_DELIVERY_MAN], true)) {
            return DeliveryTrackingStatus::ACCEPTED;
        }

        // Assigned but not yet accepted by the delivery man — tracking must not start yet.
        return DeliveryTrackingStatus::PENDING;
    }
}
