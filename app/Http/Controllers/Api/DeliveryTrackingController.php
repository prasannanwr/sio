<?php

namespace App\Http\Controllers\Api;

use App\Constraints\DeliveryManStatus;
use App\Constraints\DeliveryTrackingStatus;
use App\Constraints\OrderStatus;
use App\Http\Controllers\Controller;
use App\Mail\BookingAcceptedMail;
use App\Models\DeliveryTrackingEvent;
use App\Models\Order;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DeliveryTrackingController extends Controller
{
    public function track(string $trackingId): JsonResponse
    {
        $order = $this->deliveryQuery()
            ->where('tracking_id', $trackingId)
            ->first();

        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery was not found.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Delivery tracking retrieved successfully.',
            'data' => [
                'delivery' => $this->deliveryPayload($order, true),
            ],
        ]);
    }

    public function status(string $trackingId): JsonResponse
    {
        $order = $this->deliveryQuery()
            ->where('tracking_id', $trackingId)
            ->first();

        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery was not found.',
            ], 404);
        }

        $trackingStatus = $this->trackingStatus($order);

        return response()->json([
            'status' => true,
            'message' => 'Delivery tracking status retrieved successfully.',
            'data' => [
                'tracking_id' => $order->tracking_id,
                'tracking_status' => $trackingStatus,
                'tracking_status_label' => $this->statusLabel($trackingStatus),
                'order_status' => $order->status,
                'delivery_man_status' => $order->delivery_man_status,
            ],
        ]);
    }

    /**
     * Delivery man's contact number — exposed only while the order is "out for delivery".
     * GET /api/deliveries/track/{trackingId}/delivery-man
     */
    public function deliveryManContact(string $trackingId): JsonResponse
    {
        $order = $this->deliveryQuery()
            ->where('tracking_id', $trackingId)
            ->first();

        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery was not found.',
            ], 404);
        }

        $trackingStatus = $this->trackingStatus($order);
        $outForDelivery = $trackingStatus === DeliveryTrackingStatus::ON_THE_WAY;

        if (! $outForDelivery || ! $order->deliveryMan) {
            return response()->json([
                'status' => true,
                'message' => 'The delivery man contact becomes available once the order is out for delivery.',
                'data' => [
                    'tracking_id' => $order->tracking_id,
                    'tracking_status' => $trackingStatus,
                    'out_for_delivery' => false,
                    'delivery_man' => null,
                ],
            ]);
        }

        $phone = $order->deliveryMan->phone;
        // wa.me requires a digits-only international number (no +, spaces or dashes).
        $whatsappNumber = preg_replace('/\D/', '', (string) $phone);

        return response()->json([
            'status' => true,
            'message' => 'Delivery man contact retrieved successfully.',
            'data' => [
                'tracking_id' => $order->tracking_id,
                'tracking_status' => $trackingStatus,
                'out_for_delivery' => true,
                'delivery_man' => [
                    'id' => $order->deliveryMan->id,
                    'name' => $order->deliveryMan->name,
                    'phone' => $phone,
                    'whatsapp_url' => $whatsappNumber !== '' ? 'https://wa.me/' . $whatsappNumber : null,
                ],
            ],
        ]);
    }

    public function internalShow(Request $request, string $trackingId): JsonResponse
    {
        if ($response = $this->internalTrackingOnly($request)) {
            return $response;
        }

        return $this->track($trackingId);
    }

    public function internalSync(Request $request): JsonResponse
    {
        if ($response = $this->internalTrackingOnly($request)) {
            return $response;
        }

        $events = $request->input('events');
        $events = is_array($events) && array_is_list($events) ? $events : [$request->all()];
        $processed = [];
        $errors = [];

        foreach ($events as $index => $event) {
            $validator = Validator::make($event, [
                'tracking_id' => ['required', 'string', 'max:120'],
                'status' => ['required', 'string'],
                'note' => ['nullable', 'string', 'max:500'],
                'source' => ['nullable', 'string', 'max:120'],
                'event_reference' => ['nullable', 'string', 'max:160'],
                'occurred_at' => ['nullable', 'date'],
                'current_location_id' => ['nullable', 'integer'],
                'current_location_country_id' => ['nullable', 'integer'],
                'current_location_city_id' => ['nullable', 'integer'],
                'metadata' => ['nullable', 'array'],
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'index' => $index,
                    'tracking_id' => $event['tracking_id'] ?? null,
                    'errors' => $validator->errors(),
                ];
                continue;
            }

            $data = $validator->validated();
            $status = $this->normalizeStatusValue((string) $data['status']);

            if (! in_array($status, DeliveryTrackingStatus::values(), true)) {
                $errors[] = [
                    'index' => $index,
                    'tracking_id' => $data['tracking_id'],
                    'errors' => ['status' => ['The status must be PENDING, ACCEPTED, ON_THE_WAY, or DELIVERED.']],
                ];
                continue;
            }

            $order = Order::where('tracking_id', $data['tracking_id'])->first();

            if (! $order) {
                $errors[] = [
                    'index' => $index,
                    'tracking_id' => $data['tracking_id'],
                    'errors' => ['tracking_id' => ['Delivery was not found.']],
                ];
                continue;
            }

            DB::transaction(function () use ($order, $data, $status) {
                $oldStatus = $order->status;
                $oldDeliveryManStatus = $order->delivery_man_status;
                $updates = $this->orderUpdatesForTrackingStatus($status);

                foreach (['current_location_id', 'current_location_country_id', 'current_location_city_id'] as $field) {
                    if (array_key_exists($field, $data)) {
                        $updates[$field] = $data[$field];
                    }
                }

                $order->forceFill($updates)->save();
                $fresh = $order->fresh();

                $metadata = array_merge($data['metadata'] ?? [], [
                    'source' => $data['source'] ?? 'internal_tracking',
                    'event_reference' => $data['event_reference'] ?? null,
                    'external_status' => $data['status'],
                    'synced_by' => 'internal_tracking_system',
                ]);

                $event = DeliveryTrackingEvent::create([
                    'order_id' => $fresh->id,
                    'tracking_id' => $fresh->tracking_id,
                    'delivery_man_id' => $fresh->dispatcher_id,
                    'performed_by' => null,
                    'old_status' => $oldStatus,
                    'new_status' => $fresh->status,
                    'old_delivery_man_status' => $oldDeliveryManStatus,
                    'new_delivery_man_status' => $fresh->delivery_man_status,
                    'current_location_id' => $fresh->current_location_id,
                    'current_location_country_id' => $fresh->current_location_country_id,
                    'current_location_city_id' => $fresh->current_location_city_id,
                    'note' => $data['note'] ?? null,
                    'metadata' => array_filter($metadata, fn ($value) => $value !== null),
                ]);

                if (! empty($data['occurred_at'])) {
                    $event->created_at = \Carbon\Carbon::parse($data['occurred_at']);
                    $event->save();
                }
            });

            $processed[] = [
                'index' => $index,
                'tracking_id' => $data['tracking_id'],
                'status' => $status,
            ];
        }

        return response()->json([
            'status' => empty($errors),
            'message' => empty($errors)
                ? 'Internal tracking events synced successfully.'
                : 'Internal tracking sync completed with errors.',
            'processed_count' => count($processed),
            'error_count' => count($errors),
            'processed' => $processed,
            'errors' => $errors,
        ], empty($errors) ? 200 : 207);
    }

    public function mine(Request $request): JsonResponse
    {
        $validator = Validator::make($this->normalizedQuery($request), [
            'status' => ['nullable', Rule::in(DeliveryTrackingStatus::values())],
            'search' => ['nullable', 'string', 'max:120'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $filters = $validator->validated();
        $query = $this->deliveryQuery()->latest();
        $user = $request->user();

        if ($user->user_type !== 'admin') {
            $query->where(function (Builder $query) use ($user) {
                $query->where('dispatcher_id', $user->id)
                    ->orWhereHas('customer', function (Builder $query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
            });
        }

        $this->applyDeliveryFilters($query, $filters);

        $deliveries = $query->paginate((int) ($filters['per_page'] ?? 15));

        return response()->json([
            'status' => true,
            'message' => 'Deliveries retrieved successfully.',
            'total' => $deliveries->total(),
            'page' => $deliveries->currentPage(),
            'page_size' => $deliveries->perPage(),
            'data' => $deliveries->getCollection()
                ->map(fn (Order $order) => $this->deliveryPayload($order))
                ->values(),
        ]);
    }

    public function adminIndex(Request $request): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        $validator = Validator::make($this->normalizedQuery($request), [
            'status' => ['nullable', Rule::in(DeliveryTrackingStatus::values())],
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
        $query = $this->deliveryQuery()->latest();

        $this->applyDeliveryFilters($query, $filters);

        $deliveries = $query->paginate((int) ($filters['per_page'] ?? 15));

        return response()->json([
            'status' => true,
            'message' => 'Delivery monitor retrieved successfully.',
            'summary' => $this->adminSummary(),
            'total' => $deliveries->total(),
            'page' => $deliveries->currentPage(),
            'page_size' => $deliveries->perPage(),
            'data' => $deliveries->getCollection()
                ->map(fn (Order $order) => $this->deliveryPayload($order))
                ->values(),
        ]);
    }

    public function adminShow(Request $request, string $trackingId): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        $order = $this->deliveryQuery()->where('tracking_id', $trackingId)->first();

        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery was not found.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'delivery' => $this->deliveryPayload($order),
            ],
        ]);
    }

    public function logisticsIndex(Request $request): JsonResponse
    {
        if ($response = $this->logisticsOnly($request)) {
            return $response;
        }

        $validator = Validator::make($this->normalizedQuery($request), [
            'status' => ['nullable', Rule::in(DeliveryTrackingStatus::values())],
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
        $query = $this->deliveryQuery()->latest();

        $this->applyDeliveryFilters($query, $filters);

        $deliveries = $query->paginate((int) ($filters['per_page'] ?? 15));

        return response()->json([
            'status' => true,
            'message' => 'Logistics deliveries retrieved successfully.',
            'summary' => $this->adminSummary(),
            'total' => $deliveries->total(),
            'page' => $deliveries->currentPage(),
            'page_size' => $deliveries->perPage(),
            'data' => $deliveries->getCollection()
                ->map(fn (Order $order) => $this->deliveryPayload($order))
                ->values(),
        ]);
    }

    public function logisticsShow(Request $request, string $trackingId): JsonResponse
    {
        if ($response = $this->logisticsOnly($request)) {
            return $response;
        }

        $order = $this->deliveryQuery()->where('tracking_id', $trackingId)->first();

        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery was not found.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'delivery' => $this->deliveryPayload($order),
            ],
        ]);
    }

    public function logisticsUpdateStatus(Request $request, string $trackingId): JsonResponse
    {
        if ($response = $this->logisticsOnly($request)) {
            return $response;
        }

        $metadata = $request->input('metadata', []);
        $metadata = is_array($metadata) ? $metadata : [];

        $request->merge([
            'metadata' => array_merge($metadata, [
                'updated_by_team' => 'logistics',
            ]),
        ]);

        return $this->updateStatus($request, $trackingId);
    }

    public function assignDeliveryMan(Request $request, string $trackingId): JsonResponse
    {
        if ($response = $this->adminOnly($request)) {
            return $response;
        }

        $validator = Validator::make($request->all(), [
            'delivery_man_id' => ['required', 'integer', 'exists:users,id'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $order = Order::where('tracking_id', $trackingId)->first();

        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery was not found.',
            ], 404);
        }

        $data = $validator->validated();

        DB::transaction(function () use ($order, $data, $request) {
            $oldStatus = $order->status;
            $oldDeliveryManStatus = $order->delivery_man_status;

            $order->forceFill([
                'dispatcher_id' => $data['delivery_man_id'],
                'status' => OrderStatus::IN_DELIVERY_MAN,
                'delivery_man_status' => DeliveryManStatus::ACCEPTED,
                'dispatcher_date' => $order->dispatcher_date ?: now(),
            ])->save();

            $this->logTrackingEvent($order->fresh(), $request, $oldStatus, $oldDeliveryManStatus, $data['note'] ?? 'Delivery assigned to delivery man.');
        });

        return response()->json([
            'status' => true,
            'message' => 'Delivery man assigned successfully.',
            'data' => [
                'delivery' => $this->deliveryPayload($this->deliveryQuery()->where('tracking_id', $trackingId)->first()),
            ],
        ]);
    }

    public function updateStatus(Request $request, string $trackingId): JsonResponse
    {
        $status = $this->normalizeStatusValue((string) $request->input('status'));
        $request->merge(['status' => $status]);

        $validator = Validator::make($request->all(), [
            'status' => ['required', Rule::in(DeliveryTrackingStatus::values())],
            'delivery_man_id' => ['nullable', 'integer', 'exists:users,id'],
            'current_location_id' => ['nullable', 'integer'],
            'current_location_country_id' => ['nullable', 'integer'],
            'current_location_city_id' => ['nullable', 'integer'],
            'note' => ['nullable', 'string', 'max:500'],
            'metadata' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $order = Order::where('tracking_id', $trackingId)->first();

        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery was not found.',
            ], 404);
        }

        if (! $this->canUpdateDelivery($request, $order)) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot update this delivery.',
            ], 403);
        }

        $data = $validator->validated();

        DB::transaction(function () use ($order, $data, $request) {
            $oldStatus = $order->status;
            $oldDeliveryManStatus = $order->delivery_man_status;
            $updates = $this->orderUpdatesForTrackingStatus($data['status']);

            if ($request->user()->user_type === 'admin' && ! empty($data['delivery_man_id'])) {
                $updates['dispatcher_id'] = $data['delivery_man_id'];
                $updates['dispatcher_date'] = $order->dispatcher_date ?: now();
            }

            foreach (['current_location_id', 'current_location_country_id', 'current_location_city_id'] as $field) {
                if (array_key_exists($field, $data)) {
                    $updates[$field] = $data[$field];
                }
            }

            $order->forceFill($updates)->save();
            $fresh = $order->fresh();

            $this->logTrackingEvent($fresh, $request, $oldStatus, $oldDeliveryManStatus, $data['note'] ?? null, $data['metadata'] ?? []);
        });

        return response()->json([
            'status' => true,
            'message' => 'Delivery status updated successfully.',
            'data' => [
                'delivery' => $this->deliveryPayload($this->deliveryQuery()->where('tracking_id', $trackingId)->first()),
            ],
        ]);
    }

    /**
     * The assigned delivery man accepts a pending booking. Tracking begins after this.
     * POST /api/deliveries/{trackingId}/accept
     */
    public function accept(Request $request, string $trackingId): JsonResponse
    {
        return $this->resolvePendingRequest($request, $trackingId, true);
    }

    /**
     * The assigned delivery man rejects a pending booking. It is unassigned for reassignment.
     * POST /api/deliveries/{trackingId}/reject
     */
    public function reject(Request $request, string $trackingId): JsonResponse
    {
        return $this->resolvePendingRequest($request, $trackingId, false);
    }

    /**
     * Notify the customer by email that their booking was accepted. Failures are logged,
     * never bubbled up — a mail problem must not fail the accept action.
     */
    protected function sendBookingAcceptedEmail(Order $order): void
    {
        $customer = $order->customer;
        $email = optional(optional($customer)->user)->email ?: $order->pickup_email;

        if (! $email) {
            return;
        }

        $customerName = optional(optional($customer)->user)->name ?: $order->pickup_name;
        $deliveryManName = optional($order->deliveryMan)->name;

        try {
            Mail::to($email)->send(new BookingAcceptedMail($order, $customerName, $deliveryManName));
        } catch (\Throwable $e) {
            Log::error('Failed to send booking accepted email', [
                'order_id' => $order->id,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function resolvePendingRequest(Request $request, string $trackingId, bool $accept): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $order = Order::where('tracking_id', $trackingId)->first();

        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery was not found.',
            ], 404);
        }

        if (! $this->canUpdateDelivery($request, $order)) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot update this delivery.',
            ], 403);
        }

        // Accept/reject only applies while the request is still awaiting the delivery man's decision.
        if ($order->delivery_man_status !== DeliveryManStatus::PENDING) {
            return response()->json([
                'status' => false,
                'message' => $accept
                    ? 'This delivery is not awaiting acceptance.'
                    : 'This delivery can no longer be rejected.',
            ], 422);
        }

        DB::transaction(function () use ($order, $request, $accept) {
            $oldStatus = $order->status;
            $oldDeliveryManStatus = $order->delivery_man_status;

            if ($accept) {
                $updates = $this->orderUpdatesForTrackingStatus(DeliveryTrackingStatus::ACCEPTED);
                $note = $request->input('note') ?? 'Delivery man accepted the booking.';
            } else {
                $updates = [
                    'delivery_man_status' => DeliveryManStatus::REJECTED,
                    'dispatcher_id' => null,
                    'dispatcher_date' => null,
                ];
                $note = $request->input('note') ?? 'Delivery man rejected the booking.';
            }

            $order->forceFill($updates)->save();

            $this->logTrackingEvent($order->fresh(), $request, $oldStatus, $oldDeliveryManStatus, $note, []);
        });

        if ($accept) {
            $this->sendBookingAcceptedEmail($order->fresh(['customer.user', 'deliveryMan']));
        }

        return response()->json([
            'status' => true,
            'message' => $accept ? 'Delivery accepted successfully.' : 'Delivery rejected successfully.',
            'data' => [
                'delivery' => $this->deliveryPayload($this->deliveryQuery()->where('tracking_id', $trackingId)->first()),
            ],
        ]);
    }

    protected function deliveryQuery(): Builder
    {
        return Order::with([
            'customer.user',
            'deliveryMan.activeVehicles',
            'currentCountry',
            'currentCity',
            'pickupCountry',
            'pickupCity',
            'deliveryCountry',
            'deliveryCity',
            'trackingEvents.performedBy',
            'trackingEvents.deliveryMan',
        ]);
    }

    protected function applyDeliveryFilters(Builder $query, array $filters): void
    {
        if (! empty($filters['status'])) {
            $this->applyStatusFilter($query, $filters['status']);
        }

        if (! empty($filters['delivery_man_id'])) {
            $query->where('dispatcher_id', $filters['delivery_man_id']);
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
                $query->where('tracking_id', 'like', "%{$search}%")
                    ->orWhere('pickup_name', 'like', "%{$search}%")
                    ->orWhere('delivery_name', 'like', "%{$search}%")
                    ->orWhere('pickup_phone', 'like', "%{$search}%")
                    ->orWhere('delivery_phone', 'like', "%{$search}%");
            });
        }
    }

    protected function applyStatusFilter(Builder $query, string $status): void
    {
        $query->where(function (Builder $query) use ($status) {
            if ($status === DeliveryTrackingStatus::DELIVERED) {
                $query->where('delivery_man_status', DeliveryManStatus::DELIVERED)
                    ->orWhereIn('status', [OrderStatus::DELIVERED, OrderStatus::CUSTOMER_RECEIVED]);
            } elseif ($status === DeliveryTrackingStatus::ON_THE_WAY) {
                $query->where('delivery_man_status', DeliveryManStatus::ON_THE_WAY)
                    ->orWhere('delivery_man_status', DeliveryManStatus::ENGAGE_WITH_PARCEL)
                    ->orWhere('status', OrderStatus::IN_TRANSIT);
            } elseif ($status === DeliveryTrackingStatus::ACCEPTED) {
                $query->where('delivery_man_status', DeliveryManStatus::ACCEPTED)
                    ->orWhereIn('status', [OrderStatus::ASSIGNED, OrderStatus::IN_DELIVERY_MAN]);
            } else {
                $query->whereNull('dispatcher_id')
                    ->orWhereIn('status', [OrderStatus::UNPAID, OrderStatus::PLACED, OrderStatus::PROCESSING, OrderStatus::NEW_ORDER]);
            }
        });
    }

    protected function adminSummary(): array
    {
        return [
            'pending' => $this->statusCount(DeliveryTrackingStatus::PENDING),
            'accepted' => $this->statusCount(DeliveryTrackingStatus::ACCEPTED),
            'on_the_way' => $this->statusCount(DeliveryTrackingStatus::ON_THE_WAY),
            'delivered' => $this->statusCount(DeliveryTrackingStatus::DELIVERED),
        ];
    }

    protected function statusCount(string $status): int
    {
        $query = Order::query();
        $this->applyStatusFilter($query, $status);

        return $query->count();
    }

    protected function orderUpdatesForTrackingStatus(string $status): array
    {
        if ($status === DeliveryTrackingStatus::DELIVERED) {
            return [
                'status' => OrderStatus::DELIVERED,
                'delivery_man_status' => DeliveryManStatus::DELIVERED,
                'delivery_time' => now(),
            ];
        }

        if ($status === DeliveryTrackingStatus::ON_THE_WAY) {
            return [
                'status' => OrderStatus::IN_TRANSIT,
                'delivery_man_status' => DeliveryManStatus::ON_THE_WAY,
            ];
        }

        if ($status === DeliveryTrackingStatus::ACCEPTED) {
            return [
                'status' => OrderStatus::IN_DELIVERY_MAN,
                'delivery_man_status' => DeliveryManStatus::ACCEPTED,
                'dispatcher_date' => now(),
            ];
        }

        return [
            'status' => OrderStatus::PLACED,
            'delivery_man_status' => DeliveryManStatus::PENDING,
            'dispatcher_id' => null,
            'dispatcher_date' => null,
        ];
    }

    protected function trackingStatus(Order $order): string
    {
        return $this->trackingStatusFromValues($order->status, $order->delivery_man_status, $order->dispatcher_id);
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

        // A delivery man may be assigned (dispatcher_id set) but not have accepted yet — that is still PENDING.
        return DeliveryTrackingStatus::PENDING;
    }

    protected function logTrackingEvent(Order $order, Request $request, ?string $oldStatus, ?string $oldDeliveryManStatus, ?string $note = null, array $metadata = []): void
    {
        DeliveryTrackingEvent::create([
            'order_id' => $order->id,
            'tracking_id' => $order->tracking_id,
            'delivery_man_id' => $order->dispatcher_id,
            'performed_by' => $request->user() ? $request->user()->id : null,
            'old_status' => $oldStatus,
            'new_status' => $order->status,
            'old_delivery_man_status' => $oldDeliveryManStatus,
            'new_delivery_man_status' => $order->delivery_man_status,
            'current_location_id' => $order->current_location_id,
            'current_location_country_id' => $order->current_location_country_id,
            'current_location_city_id' => $order->current_location_city_id,
            'note' => $note,
            'metadata' => $metadata ?: null,
        ]);
    }

    protected function deliveryPayload(Order $order, bool $public = false): array
    {
        $payload = [
            'id' => $public ? null : $order->id,
            'tracking_id' => $order->tracking_id,
            'tracking_status' => $this->trackingStatus($order),
            'tracking_status_label' => $this->statusLabel($this->trackingStatus($order)),
            'order_status' => $order->status,
            'delivery_man_status' => $order->delivery_man_status,
            'delivery_man' => $this->deliveryManPayload($order->deliveryMan, $public),
            'current_location' => $this->locationPayload($order->current_location_country_id, $order->current_location_city_id, $order->currentCountry, $order->currentCity),
            'pickup_location' => $this->locationPayload($order->pickup_location_country_id, $order->pickup_location_city_id, $order->pickupCountry, $order->pickupCity),
            'delivery_location' => $this->locationPayload($order->delivery_location_country_id, $order->delivery_location_city_id, $order->deliveryCountry, $order->deliveryCity),
            'pickup_time' => $this->dateTime($order->pickup_time),
            'accepted_at' => $this->dateTime($order->dispatcher_date),
            'delivery_time' => $this->dateTime($order->delivery_time),
            'created_at' => optional($order->created_at)->toIso8601String(),
            'updated_at' => optional($order->updated_at)->toIso8601String(),
            'timeline' => $this->timelinePayload($order),
        ];

        if (! $public) {
            $payload['pickup'] = [
                'name' => $order->pickup_name,
                'phone' => $order->pickup_phone,
                'email' => $order->pickup_email,
                'address' => $order->pickup_address1,
            ];

            $payload['delivery'] = [
                'name' => $order->delivery_name,
                'phone' => $order->delivery_phone,
                'email' => $order->delivery_email,
                'address' => $order->delivery_address1,
            ];
        }

        return $payload;
    }

    protected function timelinePayload(Order $order): array
    {
        $events = $order->relationLoaded('trackingEvents') ? $order->trackingEvents : collect();

        if ($events->isNotEmpty()) {
            return $events->sortBy('created_at')->map(function (DeliveryTrackingEvent $event) {
                $status = $this->trackingStatusFromValues($event->new_status, $event->new_delivery_man_status, $event->delivery_man_id);

                return [
                    'status' => $status,
                    'status_label' => $this->statusLabel($status),
                    'order_status' => $event->new_status,
                    'delivery_man_status' => $event->new_delivery_man_status,
                    'note' => $event->note,
                    'performed_by' => $event->relationLoaded('performedBy') && $event->performedBy ? [
                        'id' => $event->performedBy->id,
                        'name' => $event->performedBy->name,
                    ] : null,
                    'created_at' => optional($event->created_at)->toIso8601String(),
                ];
            })->values()->all();
        }

        $timeline = [[
            'status' => DeliveryTrackingStatus::PENDING,
            'status_label' => $this->statusLabel(DeliveryTrackingStatus::PENDING),
            'note' => 'Delivery created.',
            'created_at' => optional($order->created_at)->toIso8601String(),
        ]];

        if ($order->dispatcher_id) {
            $timeline[] = [
                'status' => DeliveryTrackingStatus::ACCEPTED,
                'status_label' => $this->statusLabel(DeliveryTrackingStatus::ACCEPTED),
                'note' => 'Delivery assigned to delivery man.',
                'created_at' => $this->dateTime($order->dispatcher_date ?: $order->updated_at),
            ];
        }

        if ($this->trackingStatus($order) === DeliveryTrackingStatus::ON_THE_WAY) {
            $timeline[] = [
                'status' => DeliveryTrackingStatus::ON_THE_WAY,
                'status_label' => $this->statusLabel(DeliveryTrackingStatus::ON_THE_WAY),
                'note' => 'Delivery is on the way.',
                'created_at' => optional($order->updated_at)->toIso8601String(),
            ];
        }

        if ($this->trackingStatus($order) === DeliveryTrackingStatus::DELIVERED) {
            $timeline[] = [
                'status' => DeliveryTrackingStatus::DELIVERED,
                'status_label' => $this->statusLabel(DeliveryTrackingStatus::DELIVERED),
                'note' => 'Delivery completed.',
                'created_at' => $this->dateTime($order->delivery_time ?: $order->updated_at),
            ];
        }

        return $timeline;
    }

    protected function deliveryManPayload(?User $user, bool $public = false): ?array
    {
        if (! $user) {
            return null;
        }

        return [
            'id' => $public ? null : $user->id,
            'name' => $user->name,
            'phone' => $public ? null : $user->phone,
            'vehicles' => $public ? [] : $this->deliveryManVehiclesPayload($user),
        ];
    }

    protected function deliveryManVehiclesPayload(User $user): array
    {
        $vehicles = $user->relationLoaded('activeVehicles')
            ? $user->activeVehicles
            : $user->activeVehicles()->with('shippingCost')->get();

        return $vehicles->map(function (Vehicle $vehicle) {
            return $this->vehiclePayload($vehicle, true);
        })->values()->all();
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
            'shipping_rate' => $vehicle->shippingCost ? [
                'id' => $vehicle->shippingCost->id,
                'label' => $vehicle->shippingCost->display_label,
                'weight' => $vehicle->shippingCost->weight,
                'country_name' => $vehicle->shippingCost->country_name,
                'country_iso_2' => $vehicle->shippingCost->country_iso_2,
                'cost' => $vehicle->shippingCost->cost,
            ] : null,
            'documents' => $includeDocuments ? $this->vehicleDocumentsPayload($vehicle) : [],
        ];
    }

    protected function vehicleDocumentsPayload(Vehicle $vehicle): array
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

    protected function locationPayload($countryId, $cityId, $country, $city): array
    {
        return [
            'country_id' => $countryId,
            'country' => $country ? $country->name : null,
            'city_id' => $cityId,
            'city' => $city ? $city->name : null,
        ];
    }

    protected function canUpdateDelivery(Request $request, Order $order): bool
    {
        $user = $request->user();

        return $user && (
            in_array($user->user_type, ['admin', 'logistics'], true)
            || (int) $order->dispatcher_id === (int) $user->id
        );
    }

    protected function adminOnly(Request $request): ?JsonResponse
    {
        $user = $request->user();

        if (! $user || $user->user_type !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Only admins can access delivery tracking administration.',
            ], 403);
        }

        return null;
    }

    protected function logisticsOnly(Request $request): ?JsonResponse
    {
        $user = $request->user();

        if (! $user || ! in_array($user->user_type, ['admin', 'logistics'], true)) {
            return response()->json([
                'status' => false,
                'message' => 'Only logistics team members can access this delivery action.',
            ], 403);
        }

        return null;
    }

    protected function internalTrackingOnly(Request $request): ?JsonResponse
    {
        $configuredKey = env('INTERNAL_TRACKING_API_KEY');

        if (! $configuredKey) {
            return response()->json([
                'status' => false,
                'message' => 'Internal tracking integration is not configured.',
            ], 503);
        }

        $providedKey = $request->bearerToken() ?: $request->header('X-Internal-Tracking-Key');

        if (! $providedKey || ! hash_equals((string) $configuredKey, (string) $providedKey)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid internal tracking credentials.',
            ], 401);
        }

        return null;
    }

    protected function normalizedQuery(Request $request): array
    {
        $query = $request->query();

        if (isset($query['status'])) {
            $query['status'] = $this->normalizeStatusValue((string) $query['status']);
        }

        return $query;
    }

    protected function normalizeStatusValue(string $status): string
    {
        $value = strtolower(str_replace([' ', '-'], '_', trim($status)));

        return match ($value) {
            'pending', 'placed', 'processing', 'new_order' => DeliveryTrackingStatus::PENDING,
            'accepted', 'accept' => DeliveryTrackingStatus::ACCEPTED,
            'on_the_way', 'ontheway', 'in_transit' => DeliveryTrackingStatus::ON_THE_WAY,
            'delivered', 'done', 'completed' => DeliveryTrackingStatus::DELIVERED,
            default => strtoupper($status),
        };
    }

    protected function statusLabel(string $status): string
    {
        return match ($status) {
            DeliveryTrackingStatus::ACCEPTED => 'Accepted',
            DeliveryTrackingStatus::ON_THE_WAY => 'On the way',
            DeliveryTrackingStatus::DELIVERED => 'Delivered',
            default => 'Pending',
        };
    }

    protected function dateTime($value): ?string
    {
        if (! $value) {
            return null;
        }

        if (is_object($value) && method_exists($value, 'toIso8601String')) {
            return $value->toIso8601String();
        }

        return \Carbon\Carbon::parse($value)->toIso8601String();
    }

    protected function validationResponse($validator): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }
}
