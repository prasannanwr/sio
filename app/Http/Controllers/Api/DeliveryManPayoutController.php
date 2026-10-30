<?php

namespace App\Http\Controllers\Api;

use App\Constraints\DeliveryManStatus;
use App\Constraints\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\DeliveryManPayout;
use App\Models\DeliveryManPayoutItem;
use App\Models\DeliveryManPayoutLog;
use App\Models\Order;
use App\Models\User;
use App\Services\DeliveryPayoutPaymentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class DeliveryManPayoutController extends Controller
{
    public function __construct(protected DeliveryPayoutPaymentService $payments)
    {
    }

    public function dashboard(Request $request): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        $paidAmount = (float) DeliveryManPayoutItem::where('status', DeliveryManPayoutItem::STATUS_PAID)->sum('amount');
        $pendingPayoutAmount = (float) DeliveryManPayout::whereIn('status', [
            DeliveryManPayout::STATUS_PENDING,
            DeliveryManPayout::STATUS_PROCESSING,
        ])->sum('total_amount');

        $pendingDeliveries = $this->eligibleDeliveryQuery($request)->with('deliveryMan.bankDetail')->get();
        $pendingDeliveriesAmount = $this->sumOrderEarnings($pendingDeliveries);

        return response()->json([
            'status' => true,
            'message' => 'Delivery man payout dashboard retrieved successfully.',
            'data' => [
                'summary' => [
                    'pending_deliveries' => $pendingDeliveries->count(),
                    'pending_delivery_amount' => $this->money($pendingDeliveriesAmount),
                    'pending_payout_amount' => $this->money($pendingPayoutAmount),
                    'paid_payout_amount' => $this->money($paidAmount),
                    'pending_payouts' => DeliveryManPayout::whereIn('status', [
                        DeliveryManPayout::STATUS_PENDING,
                        DeliveryManPayout::STATUS_PROCESSING,
                    ])->count(),
                    'paid_payouts' => DeliveryManPayout::where('status', DeliveryManPayout::STATUS_PAID)->count(),
                    'currency' => config('delivery_payout.currency', 'EUR'),
                ],
                'recent_payouts' => DeliveryManPayout::with(['deliveryMan.bankDetail', 'processedBy'])
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function (DeliveryManPayout $payout) {
                        return $this->payoutPayload($payout);
                    })
                    ->values(),
            ],
        ]);
    }

    public function deliveryMen(Request $request): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        $orders = $this->deliveredOrderQuery($request)
            ->with(['deliveryMan.bankDetail', 'deliveryPayoutItems'])
            ->get()
            ->filter(function (Order $order) {
                return $order->deliveryMan;
            })
            ->groupBy('dispatcher_id');

        $data = $orders->map(function ($deliveryOrders) {
            /** @var Order $first */
            $first = $deliveryOrders->first();
            $deliveryMan = $first->deliveryMan;
            $totalEarned = $this->sumOrderEarnings($deliveryOrders);
            $paidAmount = $deliveryOrders->sum(function (Order $order) {
                return $order->deliveryPayoutItems
                    ->where('status', DeliveryManPayoutItem::STATUS_PAID)
                    ->sum('amount');
            });
            $lockedAmount = $deliveryOrders->sum(function (Order $order) {
                return $order->deliveryPayoutItems
                    ->whereIn('status', [
                        DeliveryManPayoutItem::STATUS_PENDING,
                        DeliveryManPayoutItem::STATUS_PROCESSING,
                    ])
                    ->sum('amount');
            });

            return [
                'delivery_man' => $this->deliveryManPayload($deliveryMan),
                'commission_percentage' => $this->commissionPercentageFor($deliveryMan),
                'delivered_orders' => $deliveryOrders->count(),
                'paid_orders' => $deliveryOrders->filter(function (Order $order) {
                    return $order->deliveryPayoutItems
                        ->where('status', DeliveryManPayoutItem::STATUS_PAID)
                        ->isNotEmpty();
                })->count(),
                'pending_orders' => $deliveryOrders->filter(function (Order $order) {
                    return $order->deliveryPayoutItems
                        ->whereIn('status', [
                            DeliveryManPayoutItem::STATUS_PENDING,
                            DeliveryManPayoutItem::STATUS_PROCESSING,
                            DeliveryManPayoutItem::STATUS_PAID,
                        ])
                        ->isEmpty();
                })->count(),
                'total_earned' => $this->money($totalEarned),
                'total_paid' => $this->money($paidAmount),
                'locked_in_pending_payouts' => $this->money($lockedAmount),
                'pending_amount' => $this->money(max($totalEarned - $paidAmount - $lockedAmount, 0)),
            ];
        })->values();

        return response()->json([
            'status' => true,
            'message' => 'Delivery men earnings retrieved successfully.',
            'data' => $data,
        ]);
    }

    public function pendingDeliveries(Request $request): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        $validator = Validator::make($request->query(), [
            'delivery_man_id' => ['nullable', 'integer', 'exists:users,id'],
            'search' => ['nullable', 'string', 'max:120'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $filters = $validator->validated();
        $query = $this->eligibleDeliveryQuery($request)->with('deliveryMan.bankDetail')->latest();

        if (! empty($filters['delivery_man_id'])) {
            $query->where('dispatcher_id', $filters['delivery_man_id']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $query) use ($search) {
                $query->where('tracking_id', 'like', "%{$search}%")
                    ->orWhere('pickup_name', 'like', "%{$search}%")
                    ->orWhere('delivery_name', 'like', "%{$search}%")
                    ->orWhere('delivery_phone', 'like', "%{$search}%");
            });
        }

        $perPage = (int) ($filters['per_page'] ?? 15);
        $orders = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Pending delivery payouts retrieved successfully.',
            'total' => $orders->total(),
            'page' => $orders->currentPage(),
            'page_size' => $orders->perPage(),
            'data' => $orders->getCollection()
                ->map(function (Order $order) {
                    return $this->orderPayoutPayload($order);
                })
                ->values(),
        ]);
    }

    public function pendingPayouts(Request $request): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        return $this->payoutList($request, [
            DeliveryManPayout::STATUS_PENDING,
            DeliveryManPayout::STATUS_PROCESSING,
        ], 'Pending delivery man payouts retrieved successfully.');
    }

    public function history(Request $request): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        return $this->payoutList($request, null, 'Delivery man payout history retrieved successfully.');
    }

    public function makePayout(Request $request): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        $this->normalizeBodyStatus($request);

        $validator = Validator::make($request->all(), [
            'delivery_man_id' => ['required', 'integer', 'exists:users,id'],
            'order_ids' => ['nullable', 'array'],
            'order_ids.*' => ['integer', 'distinct'],
            'status' => ['nullable', Rule::in([DeliveryManPayout::STATUS_PENDING, DeliveryManPayout::STATUS_PAID])],
            'payout_method' => ['nullable', 'string', 'max:80'],
            'payment_reference' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:500'],
            'metadata' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        try {
            $payout = DB::transaction(function () use ($validator, $request) {
                $data = $validator->validated();

                return $this->createPayoutForDeliveryMan(
                    (int) $data['delivery_man_id'],
                    $data['order_ids'] ?? null,
                    $this->shouldUseMonnify($request) ? DeliveryManPayout::STATUS_PENDING : ($data['status'] ?? DeliveryManPayout::STATUS_PAID),
                    $data,
                    $request
                );
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not create delivery man payout.',
                'error' => $e->getMessage(),
            ], 500);
        }

        if ($this->shouldUseMonnify($request)) {
            $payout = $this->payments->initiatePayout($payout, $request);
        }

        return response()->json([
            'status' => true,
            'message' => 'Delivery man payout created successfully.',
            'data' => [
                'payout' => $this->payoutPayload($payout->fresh(['deliveryMan.bankDetail', 'processedBy', 'items'])),
            ],
        ], 201);
    }

    public function bulkPayout(Request $request): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        $this->normalizeBodyStatus($request);

        $validator = Validator::make($request->all(), [
            'status' => ['nullable', Rule::in([DeliveryManPayout::STATUS_PENDING, DeliveryManPayout::STATUS_PAID])],
            'payout_method' => ['nullable', 'string', 'max:80'],
            'payment_reference' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:500'],
            'metadata' => ['nullable', 'array'],
            'payouts' => ['required', 'array', 'min:1'],
            'payouts.*.delivery_man_id' => ['required', 'integer', 'exists:users,id'],
            'payouts.*.order_ids' => ['nullable', 'array'],
            'payouts.*.order_ids.*' => ['integer', 'distinct'],
            'payouts.*.payment_reference' => ['nullable', 'string', 'max:120'],
            'payouts.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        try {
            $payouts = DB::transaction(function () use ($validator, $request) {
                $data = $validator->validated();
                $created = collect();

                foreach ($data['payouts'] as $payoutData) {
                    $options = array_merge($data, $payoutData);
                    unset($options['payouts']);

                    $created->push($this->createPayoutForDeliveryMan(
                        (int) $payoutData['delivery_man_id'],
                        $payoutData['order_ids'] ?? null,
                        $this->shouldUseMonnify($request) ? DeliveryManPayout::STATUS_PENDING : ($options['status'] ?? DeliveryManPayout::STATUS_PAID),
                        $options,
                        $request
                    ));
                }

                return $created;
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not create bulk delivery man payouts.',
                'error' => $e->getMessage(),
            ], 500);
        }

        if ($this->shouldUseMonnify($request)) {
            $payouts = $payouts->map(function (DeliveryManPayout $payout) use ($request) {
                return $this->payments->initiatePayout($payout, $request);
            });
        }

        return response()->json([
            'status' => true,
            'message' => 'Bulk delivery man payouts created successfully.',
            'data' => $payouts->map(function (DeliveryManPayout $payout) {
                return $this->payoutPayload($payout->fresh(['deliveryMan.bankDetail', 'processedBy', 'items']));
            })->values(),
        ], 201);
    }

    public function markDeliveriesPaid(Request $request): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        $validator = Validator::make($request->all(), [
            'order_ids' => ['required', 'array', 'min:1'],
            'order_ids.*' => ['required', 'integer', 'distinct'],
            'payout_method' => ['nullable', 'string', 'max:80'],
            'payment_reference' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:500'],
            'metadata' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        try {
            $payouts = DB::transaction(function () use ($validator, $request) {
                $data = $validator->validated();
                $orders = $this->eligibleDeliveryQuery($request)
                    ->whereIn('id', $data['order_ids'])
                    ->lockForUpdate()
                    ->get()
                    ->groupBy('dispatcher_id');

                if ($orders->flatten()->count() !== count($data['order_ids'])) {
                    throw ValidationException::withMessages([
                        'order_ids' => ['One or more deliveries are not eligible for payout or are already in a payout.'],
                    ]);
                }

                $created = collect();

                foreach ($orders as $deliveryManId => $deliveryOrders) {
                    $created->push($this->createPayoutForDeliveryMan(
                        (int) $deliveryManId,
                        $deliveryOrders->pluck('id')->all(),
                        $this->shouldUseMonnify($request) ? DeliveryManPayout::STATUS_PENDING : DeliveryManPayout::STATUS_PAID,
                        $data,
                        $request
                    ));
                }

                return $created;
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not mark deliveries as paid.',
                'error' => $e->getMessage(),
            ], 500);
        }

        if ($this->shouldUseMonnify($request)) {
            $payouts = $payouts->map(function (DeliveryManPayout $payout) use ($request) {
                return $this->payments->initiatePayout($payout, $request);
            });
        }

        return response()->json([
            'status' => true,
            'message' => 'Deliveries marked as paid successfully.',
            'data' => $payouts->map(function (DeliveryManPayout $payout) {
                return $this->payoutPayload($payout->fresh(['deliveryMan.bankDetail', 'processedBy', 'items']));
            })->values(),
        ]);
    }

    public function show(Request $request, string $reference): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        $payout = DeliveryManPayout::with(['deliveryMan.bankDetail', 'processedBy', 'items.order', 'logs.performedBy'])
            ->where('reference', $reference)
            ->first();

        if (! $payout) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery man payout not found.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'payout' => $this->payoutPayload($payout, true),
            ],
        ]);
    }

    public function markPaid(Request $request, string $reference): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        $validator = Validator::make($request->all(), [
            'payment_reference' => ['nullable', 'string', 'max:120'],
            'payout_method' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string', 'max:500'],
            'metadata' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $payout = DeliveryManPayout::where('reference', $reference)->first();

        if (! $payout) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery man payout not found.',
            ], 404);
        }

        DB::transaction(function () use ($payout, $request, $validator) {
            $data = $validator->validated();
            $oldStatus = $payout->status;

            $payout->update([
                'status' => DeliveryManPayout::STATUS_PAID,
                'payment_reference' => $data['payment_reference'] ?? $payout->payment_reference,
                'payout_method' => $data['payout_method'] ?? $payout->payout_method,
                'notes' => $data['notes'] ?? $payout->notes,
                'metadata' => $data['metadata'] ?? $payout->metadata,
                'paid_at' => $payout->paid_at ?: now(),
                'processed_by' => $request->user()->id,
            ]);

            DeliveryManPayoutItem::where('delivery_man_payout_id', $payout->id)->update([
                'status' => DeliveryManPayoutItem::STATUS_PAID,
                'paid_at' => now(),
            ]);

            $this->recordWalletPayout($payout->fresh());
            $this->logPayout($payout->fresh(), 'marked_paid', $request, $oldStatus, DeliveryManPayout::STATUS_PAID, 'Payout marked as paid.', $data);
        });

        return response()->json([
            'status' => true,
            'message' => 'Delivery man payout marked as paid successfully.',
            'data' => [
                'payout' => $this->payoutPayload($payout->fresh(['deliveryMan.bankDetail', 'processedBy', 'items'])),
            ],
        ]);
    }

    public function markFailed(Request $request, string $reference): JsonResponse
    {
        return $this->setTerminalStatus($request, $reference, DeliveryManPayout::STATUS_FAILED, 'Payout marked as failed.');
    }

    public function cancel(Request $request, string $reference): JsonResponse
    {
        return $this->setTerminalStatus($request, $reference, DeliveryManPayout::STATUS_CANCELLED, 'Payout cancelled successfully.');
    }

    public function authorizeMonnify(Request $request, string $reference): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        $validator = Validator::make($request->all(), [
            'authorization_code' => ['required', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $payout = DeliveryManPayout::where('reference', $reference)->first();

        if (! $payout) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery man payout not found.',
            ], 404);
        }

        try {
            $payout = $this->payments->authorizeOtp($payout, $validator->validated()['authorization_code'], $request);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not authorize Monnify payout.',
                'error' => $e->getMessage(),
            ], 502);
        }

        return response()->json([
            'status' => true,
            'message' => 'Monnify payout authorization submitted successfully.',
            'data' => [
                'payout' => $this->payoutPayload($payout->fresh(['deliveryMan.bankDetail', 'processedBy', 'items'])),
            ],
        ]);
    }

    public function resendMonnifyOtp(Request $request, string $reference): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        $payout = DeliveryManPayout::where('reference', $reference)->first();

        if (! $payout) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery man payout not found.',
            ], 404);
        }

        try {
            $response = $this->payments->resendOtp($payout, $request);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not resend Monnify payout OTP.',
                'error' => $e->getMessage(),
            ], 502);
        }

        return response()->json([
            'status' => true,
            'message' => 'Monnify payout OTP resent successfully.',
            'data' => $response,
        ]);
    }

    public function refreshMonnify(Request $request, string $reference): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        $payout = DeliveryManPayout::where('reference', $reference)->first();

        if (! $payout) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery man payout not found.',
            ], 404);
        }

        try {
            $payout = $this->payments->refreshStatus($payout, $request);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not refresh Monnify payout status.',
                'error' => $e->getMessage(),
            ], 502);
        }

        return response()->json([
            'status' => true,
            'message' => 'Monnify payout status refreshed successfully.',
            'data' => [
                'payout' => $this->payoutPayload($payout->fresh(['deliveryMan.bankDetail', 'processedBy', 'items'])),
            ],
        ]);
    }

    public function walletBalance(Request $request): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        try {
            $balance = $this->payments->walletBalance();
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not retrieve Monnify payout wallet balance.',
                'error' => $e->getMessage(),
            ], 502);
        }

        return response()->json([
            'status' => true,
            'message' => 'Monnify payout wallet balance retrieved successfully.',
            'data' => $balance,
        ]);
    }

    public function logs(Request $request, string $reference): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        $payout = DeliveryManPayout::where('reference', $reference)->first();

        if (! $payout) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery man payout not found.',
            ], 404);
        }

        $logs = DeliveryManPayoutLog::with('performedBy')
            ->where('delivery_man_payout_id', $payout->id)
            ->latest()
            ->paginate((int) $request->query('per_page', 20));

        return response()->json([
            'status' => true,
            'message' => 'Delivery man payout logs retrieved successfully.',
            'total' => $logs->total(),
            'page' => $logs->currentPage(),
            'page_size' => $logs->perPage(),
            'data' => $logs->getCollection()->map(function (DeliveryManPayoutLog $log) {
                return $this->logPayload($log);
            })->values(),
        ]);
    }

    protected function payoutList(Request $request, ?array $statuses, string $message): JsonResponse
    {
        $queryParams = $request->query();

        if (isset($queryParams['status'])) {
            $queryParams['status'] = strtoupper((string) $queryParams['status']);
        }

        $validator = Validator::make($queryParams, [
            'delivery_man_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', Rule::in([
                DeliveryManPayout::STATUS_PENDING,
                DeliveryManPayout::STATUS_PROCESSING,
                DeliveryManPayout::STATUS_PAID,
                DeliveryManPayout::STATUS_FAILED,
                DeliveryManPayout::STATUS_CANCELLED,
            ])],
            'search' => ['nullable', 'string', 'max:120'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $filters = $validator->validated();
        $query = DeliveryManPayout::with(['deliveryMan.bankDetail', 'processedBy'])->latest();

        if ($statuses !== null) {
            $query->whereIn('status', $statuses);
        }

        if (! empty($filters['status'])) {
            $query->where('status', strtoupper($filters['status']));
        }

        if (! empty($filters['delivery_man_id'])) {
            $query->where('delivery_man_id', $filters['delivery_man_id']);
        }

        if (! empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $query) use ($search) {
                $query->where('reference', 'like', "%{$search}%")
                    ->orWhere('payment_reference', 'like', "%{$search}%");
            });
        }

        $payouts = $query->paginate((int) ($filters['per_page'] ?? 15));

        return response()->json([
            'status' => true,
            'message' => $message,
            'total' => $payouts->total(),
            'page' => $payouts->currentPage(),
            'page_size' => $payouts->perPage(),
            'data' => $payouts->getCollection()
                ->map(function (DeliveryManPayout $payout) {
                    return $this->payoutPayload($payout);
                })
                ->values(),
        ]);
    }

    protected function createPayoutForDeliveryMan(int $deliveryManId, ?array $orderIds, string $status, array $options, Request $request): DeliveryManPayout
    {
        $deliveryMan = User::find($deliveryManId);

        if (! $deliveryMan) {
            throw ValidationException::withMessages([
                'delivery_man_id' => ['Delivery man was not found.'],
            ]);
        }

        $query = $this->eligibleDeliveryQuery($request)
            ->where('dispatcher_id', $deliveryManId)
            ->with('deliveryMan.bankDetail')
            ->lockForUpdate();

        if ($orderIds) {
            $query->whereIn('id', $orderIds);
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            throw ValidationException::withMessages([
                'order_ids' => ['No eligible unpaid deliveries were found for this delivery man.'],
            ]);
        }

        if ($orderIds && $orders->count() !== count($orderIds)) {
            $available = $orders->pluck('id')->map(function ($id) {
                return (int) $id;
            })->all();

            throw ValidationException::withMessages([
                'order_ids' => ['Some selected deliveries are not eligible for payout: ' . implode(', ', array_diff($orderIds, $available))],
            ]);
        }

        $currency = config('delivery_payout.currency', 'EUR');
        $totalAmount = $this->sumOrderEarnings($orders);
        $reference = $this->generateReference();
        $isPaid = $status === DeliveryManPayout::STATUS_PAID;

        $payout = DeliveryManPayout::create([
            'reference' => $reference,
            'delivery_man_id' => $deliveryMan->id,
            'processed_by' => $request->user()->id,
            'total_amount' => $totalAmount,
            'orders_count' => $orders->count(),
            'currency' => $currency,
            'status' => $status,
            'payout_method' => $options['payout_method'] ?? null,
            'payment_reference' => $options['payment_reference'] ?? null,
            'notes' => $options['notes'] ?? null,
            'metadata' => $options['metadata'] ?? null,
            'requested_at' => now(),
            'paid_at' => $isPaid ? now() : null,
        ]);

        foreach ($orders as $order) {
            DeliveryManPayoutItem::create([
                'delivery_man_payout_id' => $payout->id,
                'delivery_man_id' => $deliveryMan->id,
                'order_id' => $order->id,
                'tracking_id' => $order->tracking_id,
                'amount' => $this->earningAmount($order, $deliveryMan),
                'currency' => $currency,
                'status' => $isPaid ? DeliveryManPayoutItem::STATUS_PAID : DeliveryManPayoutItem::STATUS_PENDING,
                'paid_at' => $isPaid ? now() : null,
                'metadata' => [
                    'shipping_cost' => $order->shipping_cost,
                    'commission_percentage' => $this->commissionPercentageFor($deliveryMan),
                ],
            ]);
        }

        if ($isPaid) {
            $this->recordWalletPayout($payout);
        }

        $this->logPayout($payout, 'created', $request, null, $status, 'Payout created.', [
            'order_ids' => $orders->pluck('id')->values()->all(),
            'status' => $status,
        ]);

        return $payout;
    }

    protected function setTerminalStatus(Request $request, string $reference, string $status, string $message): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        $validator = Validator::make($request->all(), [
            'notes' => ['nullable', 'string', 'max:500'],
            'metadata' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $payout = DeliveryManPayout::where('reference', $reference)->first();

        if (! $payout) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery man payout not found.',
            ], 404);
        }

        if ($payout->status === DeliveryManPayout::STATUS_PAID) {
            return response()->json([
                'status' => false,
                'message' => 'Paid payouts cannot be changed.',
            ], 422);
        }

        DB::transaction(function () use ($payout, $status, $request, $validator, $message) {
            $oldStatus = $payout->status;
            $data = $validator->validated();

            $payout->update([
                'status' => $status,
                'notes' => $data['notes'] ?? $payout->notes,
                'metadata' => $data['metadata'] ?? $payout->metadata,
                'processed_by' => $request->user()->id,
            ]);

            DeliveryManPayoutItem::where('delivery_man_payout_id', $payout->id)->update([
                'status' => $status,
            ]);

            $this->logPayout($payout->fresh(), strtolower($status), $request, $oldStatus, $status, $message, $data);
        });

        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => [
                'payout' => $this->payoutPayload($payout->fresh(['deliveryMan.bankDetail', 'processedBy', 'items'])),
            ],
        ]);
    }

    protected function deliveredOrderQuery(Request $request): Builder
    {
        $query = Order::query()
            ->whereNotNull('dispatcher_id')
            ->where(function (Builder $query) {
                $query->where('delivery_man_status', config('delivery_payout.eligible_delivery_man_status', DeliveryManStatus::DELIVERED))
                    ->orWhereIn('status', config('delivery_payout.eligible_order_statuses', [
                        OrderStatus::DELIVERED,
                        OrderStatus::CUSTOMER_RECEIVED,
                        OrderStatus::ACCEPT_BY_AGENT,
                    ]));
            });

        if ($request->filled('from')) {
            $query->whereDate('delivery_time', '>=', $request->query('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('delivery_time', '<=', $request->query('to'));
        }

        return $query;
    }

    protected function eligibleDeliveryQuery(Request $request): Builder
    {
        return $this->deliveredOrderQuery($request)
            ->whereDoesntHave('deliveryPayoutItems', function (Builder $query) {
                $query->whereIn('status', [
                    DeliveryManPayoutItem::STATUS_PENDING,
                    DeliveryManPayoutItem::STATUS_PROCESSING,
                    DeliveryManPayoutItem::STATUS_PAID,
                ]);
            });
    }

    protected function sumOrderEarnings($orders): float
    {
        return (float) $orders->sum(function (Order $order) {
            return $this->earningAmount($order, $order->deliveryMan);
        });
    }

    protected function earningAmount(Order $order, ?User $deliveryMan): float
    {
        if (! $deliveryMan) {
            return 0.0;
        }

        $baseAmount = (float) ($order->shipping_cost ?: $order->val_of_goods ?: 0);
        $percentage = $this->commissionPercentageFor($deliveryMan);

        return round($baseAmount * ($percentage / 100), 2);
    }

    protected function commissionPercentageFor(User $deliveryMan): float
    {
        $percentage = (float) ($deliveryMan->pickup_comission_percentage
            ?? $deliveryMan->pickup_comission_persentage
            ?? 0);

        if ($percentage <= 0) {
            $percentage = (float) config('delivery_payout.default_commission_percentage', 1);
        }

        return $percentage;
    }

    protected function recordWalletPayout(DeliveryManPayout $payout): void
    {
        if (function_exists('accountTxExists') && accountTxExists($payout->reference)) {
            return;
        }

        if (function_exists('updateAccountBalance')) {
            updateAccountBalance(
                $payout->delivery_man_id,
                $payout->total_amount,
                $payout->reference,
                'credit',
                'Delivery man payout ' . $payout->reference,
                'payout',
                $payout->currency
            );
        }
    }

    protected function logPayout(?DeliveryManPayout $payout, string $action, Request $request, ?string $oldStatus, ?string $newStatus, string $message, array $payload = []): void
    {
        DeliveryManPayoutLog::create([
            'delivery_man_payout_id' => $payout ? $payout->id : null,
            'payout_reference' => $payout ? $payout->reference : null,
            'action' => $action,
            'performed_by' => $request->user() ? $request->user()->id : null,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'message' => $message,
            'payload' => $payload,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
        ]);
    }

    protected function payoutPayload(DeliveryManPayout $payout, bool $includeDetails = false): array
    {
        $payload = [
            'id' => $payout->id,
            'reference' => $payout->reference,
            'delivery_man' => $payout->relationLoaded('deliveryMan') ? $this->deliveryManPayload($payout->deliveryMan) : null,
            'processed_by' => $payout->relationLoaded('processedBy') ? $this->userPayload($payout->processedBy) : null,
            'total_amount' => $payout->total_amount,
            'orders_count' => $payout->orders_count,
            'currency' => $payout->currency,
            'status' => $payout->status,
            'payout_method' => $payout->payout_method,
            'payment_reference' => $payout->payment_reference,
            'notes' => $payout->notes,
            'metadata' => $payout->metadata,
            'requested_at' => optional($payout->requested_at)->toIso8601String(),
            'paid_at' => optional($payout->paid_at)->toIso8601String(),
            'created_at' => optional($payout->created_at)->toIso8601String(),
            'updated_at' => optional($payout->updated_at)->toIso8601String(),
        ];

        if ($includeDetails || $payout->relationLoaded('items')) {
            $payload['items'] = $payout->items->map(function (DeliveryManPayoutItem $item) {
                return $this->payoutItemPayload($item);
            })->values();
        }

        if ($includeDetails && $payout->relationLoaded('logs')) {
            $payload['logs'] = $payout->logs->map(function (DeliveryManPayoutLog $log) {
                return $this->logPayload($log);
            })->values();
        }

        return $payload;
    }

    protected function payoutItemPayload(DeliveryManPayoutItem $item): array
    {
        return [
            'id' => $item->id,
            'order_id' => $item->order_id,
            'tracking_id' => $item->tracking_id,
            'amount' => $item->amount,
            'currency' => $item->currency,
            'status' => $item->status,
            'paid_at' => optional($item->paid_at)->toIso8601String(),
            'order' => $item->relationLoaded('order') && $item->order ? $this->orderSummaryPayload($item->order) : null,
        ];
    }

    protected function orderPayoutPayload(Order $order): array
    {
        return array_merge($this->orderSummaryPayload($order), [
            'delivery_man' => $order->deliveryMan ? $this->deliveryManPayload($order->deliveryMan) : null,
            'commission_percentage' => $order->deliveryMan ? $this->commissionPercentageFor($order->deliveryMan) : 0,
            'payout_amount' => $this->money($this->earningAmount($order, $order->deliveryMan)),
            'currency' => config('delivery_payout.currency', 'EUR'),
        ]);
    }

    protected function orderSummaryPayload(Order $order): array
    {
        return [
            'id' => $order->id,
            'tracking_id' => $order->tracking_id,
            'status' => $order->status,
            'delivery_man_status' => $order->delivery_man_status,
            'shipping_cost' => $order->shipping_cost,
            'pickup_name' => $order->pickup_name,
            'pickup_phone' => $order->pickup_phone,
            'pickup_address' => $order->pickup_address1,
            'delivery_name' => $order->delivery_name,
            'delivery_phone' => $order->delivery_phone,
            'delivery_address' => $order->delivery_address1,
            'dispatcher_date' => $this->dateTime($order->dispatcher_date),
            'delivery_time' => $this->dateTime($order->delivery_time),
            'created_at' => optional($order->created_at)->toIso8601String(),
        ];
    }

    protected function deliveryManPayload(?User $user): ?array
    {
        if (! $user) {
            return null;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?: ($user->mobile ?? null),
            'user_type' => $user->user_type,
            'commission_percentage' => $this->commissionPercentageFor($user),
            'bank_detail_id' => $user->bank_detail_id,
            'bank_detail' => $user->relationLoaded('bankDetail') && $user->bankDetail ? [
                'id' => $user->bankDetail->id,
                'bank_name' => $user->bankDetail->bank_name,
                'bank_code' => $user->bankDetail->bank_code ?: $user->bankDetail->routing_number,
                'account_name' => $user->bankDetail->account_name,
                'account_number' => $user->bankDetail->acount_number,
                'iban' => $user->bankDetail->iban,
                'routing_number' => $user->bankDetail->routing_number,
            ] : null,
        ];
    }

    protected function userPayload(?User $user): ?array
    {
        if (! $user) {
            return null;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }

    protected function logPayload(DeliveryManPayoutLog $log): array
    {
        return [
            'id' => $log->id,
            'action' => $log->action,
            'old_status' => $log->old_status,
            'new_status' => $log->new_status,
            'message' => $log->message,
            'payload' => $log->payload,
            'performed_by' => $log->relationLoaded('performedBy') ? $this->userPayload($log->performedBy) : null,
            'ip_address' => $log->ip_address,
            'created_at' => optional($log->created_at)->toIso8601String(),
        ];
    }

    protected function generateReference(): string
    {
        do {
            $reference = 'DMP-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(8));
        } while (DeliveryManPayout::where('reference', $reference)->exists());

        return $reference;
    }

    protected function money($amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }

    protected function dateTime($value): ?string
    {
        if (! $value) {
            return null;
        }

        if (is_object($value) && method_exists($value, 'toIso8601String')) {
            return $value->toIso8601String();
        }

        try {
            return \Carbon\Carbon::parse($value)->toIso8601String();
        } catch (Throwable $e) {
            return (string) $value;
        }
    }

    protected function adminOnly(Request $request): ?JsonResponse
    {
        $user = $request->user();

        if (! $user || $user->user_type !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Only admins can access delivery man payouts.',
            ], 403);
        }

        return null;
    }

    protected function validationResponse($validator): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }

    protected function normalizeBodyStatus(Request $request): void
    {
        if ($request->filled('status')) {
            $request->merge([
                'status' => strtoupper((string) $request->input('status')),
            ]);
        }
    }

    protected function shouldUseMonnify(Request $request): bool
    {
        return strtolower((string) $request->input('payout_method', config('delivery_payout.payment_provider', 'monnify'))) === 'monnify';
    }
}
