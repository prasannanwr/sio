<?php

namespace App\Http\Controllers\Api;

use App\Constraints\DeliveryManStatus;
use App\Constraints\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\DeliveryRating;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryRatingController extends Controller
{
    /**
     * Submit (or update) a rating for a delivered cargo order.
     * POST /api/deliveries/{trackingId}/rating  { rating: 1-5, comment? }
     */
    public function store(Request $request, string $trackingId): JsonResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $order = Order::with('customer')->where('tracking_id', $trackingId)->first();

        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery was not found.',
            ], 404);
        }

        $user = $request->user();
        $isOwner = $order->customer && (int) $order->customer->user_id === (int) $user->id;

        if (! $isOwner && $user->user_type !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'You can only rate your own delivery.',
            ], 403);
        }

        if (! $this->isDelivered($order)) {
            return response()->json([
                'status' => false,
                'message' => 'You can rate a delivery only after it has been delivered.',
            ], 422);
        }

        $rating = DeliveryRating::updateOrCreate(
            ['order_id' => $order->id],
            [
                'delivery_man_id' => $order->dispatcher_id,
                'user_id' => $user->id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Thank you! Your rating has been submitted.',
            'data' => $this->ratingPayload($rating->fresh('user')),
        ], 201);
    }

    /**
     * The current user's rating for an order (to know if it's already rated).
     * GET /api/deliveries/{trackingId}/rating
     */
    public function show(Request $request, string $trackingId): JsonResponse
    {
        $order = Order::where('tracking_id', $trackingId)->first();

        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery was not found.',
            ], 404);
        }

        $rating = DeliveryRating::with('user')
            ->where('order_id', $order->id)
            ->where('user_id', $request->user()->id)
            ->first();

        return response()->json([
            'status' => true,
            'message' => $rating ? 'Rating retrieved successfully.' : 'This delivery has not been rated yet.',
            'data' => [
                'tracking_id' => $order->tracking_id,
                'is_rated' => (bool) $rating,
                'can_rate' => $this->isDelivered($order),
                'rating' => $rating ? $this->ratingPayload($rating) : null,
            ],
        ]);
    }

    /**
     * Aggregate rating summary + reviews for a delivery man (the rating screen).
     * GET /api/drivers/{deliveryManId}/ratings
     */
    public function deliveryManRatings(Request $request, int $deliveryManId): JsonResponse
    {
        $base = DeliveryRating::where('delivery_man_id', $deliveryManId);

        $total = (clone $base)->count();
        $average = $total ? round((float) (clone $base)->avg('rating'), 1) : 0.0;

        $counts = (clone $base)
            ->select('rating', DB::raw('count(*) as total'))
            ->groupBy('rating')
            ->pluck('total', 'rating');

        $distribution = collect([5, 4, 3, 2, 1])->map(function ($star) use ($counts, $total) {
            $count = (int) ($counts[$star] ?? 0);

            return [
                'rating' => $star,
                'label' => DeliveryRating::label($star),
                'count' => $count,
                'percentage' => $total ? round(($count / $total) * 100, 1) : 0.0,
            ];
        })->values();

        $reviews = (clone $base)->with('user')->latest()->paginate(
            (int) $request->integer('per_page', 10)
        );

        return response()->json([
            'status' => true,
            'message' => 'Delivery ratings retrieved successfully.',
            'data' => [
                'delivery_man_id' => $deliveryManId,
                'average_rating' => $average,
                'total_reviews' => $total,
                'distribution' => $distribution,
                'reviews' => $reviews->getCollection()->map(fn (DeliveryRating $r) => $this->ratingPayload($r))->values(),
            ],
            'page' => $reviews->currentPage(),
            'page_size' => $reviews->perPage(),
            'total' => $reviews->total(),
        ]);
    }

    protected function isDelivered(Order $order): bool
    {
        return $order->delivery_man_status === DeliveryManStatus::DELIVERED
            || in_array($order->status, [OrderStatus::DELIVERED, OrderStatus::CUSTOMER_RECEIVED], true);
    }

    protected function ratingPayload(DeliveryRating $rating): array
    {
        return [
            'id' => $rating->id,
            'reviewer_name' => optional($rating->user)->name,
            'rating' => $rating->rating,
            'label' => DeliveryRating::label($rating->rating),
            'comment' => $rating->comment,
            'created_at' => optional($rating->created_at)->toIso8601String(),
            'created_at_human' => optional($rating->created_at)->diffForHumans(),
        ];
    }
}
