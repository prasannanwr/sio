<?php

namespace App\Http\Controllers;

use App\Constraints\DeliveryManStatus;
use App\Constraints\DeliveryTrackingStatus;
use App\Constraints\OrderStatus;
use App\Http\Controllers\Api\DeliveryTrackingController as ApiDeliveryTrackingController;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class DeliveryTrackingAdminController extends Controller
{
    protected ApiDeliveryTrackingController $api;

    public function __construct(ApiDeliveryTrackingController $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $filters = $this->normalizedFilters($request);
        $query = $this->deliveryQuery()->latest();
        $this->applyDeliveryFilters($query, $filters);

        $deliveries = $query->paginate(15);
        $deliveries->getCollection()->transform(function (Order $delivery) {
            $status = $this->trackingStatus($delivery);
            $delivery->tracking_status = $status;
            $delivery->tracking_status_label = $this->statusLabel($status);
            $delivery->tracking_status_class = $this->statusClass($status);

            return $delivery;
        });

        $deliveryMen = $this->deliveryMen();
        $statuses = DeliveryTrackingStatus::values();
        $summary = $this->summary();

        return view('admin.delivery_tracking.index', compact('deliveries', 'deliveryMen', 'filters', 'statuses', 'summary'));
    }

    public function show(string $trackingId)
    {
        $this->authorizeAdmin();

        $delivery = $this->deliveryQuery()->where('tracking_id', $trackingId)->firstOrFail();
        $status = $this->trackingStatus($delivery);
        $delivery->tracking_status = $status;
        $delivery->tracking_status_label = $this->statusLabel($status);
        $delivery->tracking_status_class = $this->statusClass($status);

        $deliveryMen = $this->deliveryMen();
        $statuses = DeliveryTrackingStatus::values();

        return view('admin.delivery_tracking.show', compact('delivery', 'deliveryMen', 'statuses'));
    }

    public function assignDeliveryMan(Request $request, string $trackingId): RedirectResponse
    {
        $this->authorizeAdmin();

        $request->validate([
            'delivery_man_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereIn('user_type', ['dispatcher', 'courier']);
                }),
            ],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            return $this->redirectFromApiResponse($this->api->assignDeliveryMan($request, $trackingId), 'Delivery man assigned successfully.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            return back()->withInput()->with(['message' => $e->getMessage(), 'message_type' => 'danger']);
        }
    }

    public function updateStatus(Request $request, string $trackingId): RedirectResponse
    {
        $this->authorizeAdmin();

        $request->merge([
            'status' => $this->normalizeStatusValue((string) $request->input('status')),
        ]);

        $request->validate([
            'status' => ['required', Rule::in(DeliveryTrackingStatus::values())],
            'delivery_man_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereIn('user_type', ['dispatcher', 'courier']);
                }),
            ],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        if (! $request->filled('delivery_man_id')) {
            $request->request->remove('delivery_man_id');
        }

        try {
            return $this->redirectFromApiResponse($this->api->updateStatus($request, $trackingId), 'Delivery status updated successfully.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            return back()->withInput()->with(['message' => $e->getMessage(), 'message_type' => 'danger']);
        }
    }

    protected function redirectFromApiResponse($response, string $defaultSuccessMessage): RedirectResponse
    {
        $payload = $response->getData(true);

        if (($payload['status'] ?? false) !== true) {
            if (! empty($payload['errors'])) {
                return back()->withInput()->withErrors($payload['errors']);
            }

            return back()->withInput()->with([
                'message' => $payload['message'] ?? 'The delivery action could not be completed.',
                'message_type' => 'danger',
            ]);
        }

        return back()->with([
            'message' => $payload['message'] ?? $defaultSuccessMessage,
            'message_type' => 'success',
        ]);
    }

    protected function deliveryQuery(): Builder
    {
        return Order::with([
            'customer.user',
            'deliveryMan.activeVehicles',
            'pickupCountry',
            'pickupCity',
            'deliveryCountry',
            'deliveryCity',
            'currentCountry',
            'currentCity',
            'trackingEvents.performedBy',
            'trackingEvents.deliveryMan',
        ]);
    }

    protected function normalizedFilters(Request $request): array
    {
        return [
            'status' => $request->filled('status') ? $this->normalizeStatusValue((string) $request->status) : null,
            'delivery_man_id' => $request->input('delivery_man_id'),
            'search' => $request->input('search'),
            'from' => $request->input('from'),
            'to' => $request->input('to'),
        ];
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

    protected function summary(): array
    {
        $summary = [];

        foreach (DeliveryTrackingStatus::values() as $status) {
            $query = Order::query();
            $this->applyStatusFilter($query, $status);
            $summary[$status] = $query->count();
        }

        return $summary;
    }

    public function trackingStatus(Order $order): string
    {
        if ($order->delivery_man_status === DeliveryManStatus::DELIVERED
            || in_array($order->status, [OrderStatus::DELIVERED, OrderStatus::CUSTOMER_RECEIVED], true)) {
            return DeliveryTrackingStatus::DELIVERED;
        }

        if (in_array($order->delivery_man_status, [DeliveryManStatus::ON_THE_WAY, DeliveryManStatus::ENGAGE_WITH_PARCEL], true)
            || $order->status === OrderStatus::IN_TRANSIT) {
            return DeliveryTrackingStatus::ON_THE_WAY;
        }

        if ($order->delivery_man_status === DeliveryManStatus::ACCEPTED
            || in_array($order->status, [OrderStatus::ASSIGNED, OrderStatus::IN_DELIVERY_MAN], true)
            || $order->dispatcher_id) {
            return DeliveryTrackingStatus::ACCEPTED;
        }

        return DeliveryTrackingStatus::PENDING;
    }

    public function statusLabel(string $status): string
    {
        if ($status === DeliveryTrackingStatus::ACCEPTED) {
            return 'Accepted';
        }

        if ($status === DeliveryTrackingStatus::ON_THE_WAY) {
            return 'On the way';
        }

        if ($status === DeliveryTrackingStatus::DELIVERED) {
            return 'Delivered';
        }

        return 'Pending';
    }

    public function statusClass(string $status): string
    {
        if ($status === DeliveryTrackingStatus::DELIVERED) {
            return 'success';
        }

        if ($status === DeliveryTrackingStatus::ON_THE_WAY) {
            return 'info';
        }

        if ($status === DeliveryTrackingStatus::ACCEPTED) {
            return 'primary';
        }

        return 'warning';
    }

    protected function normalizeStatusValue(string $status): string
    {
        $value = strtolower(str_replace([' ', '-'], '_', trim($status)));

        if (in_array($value, ['pending', 'placed', 'processing', 'new_order'], true)) {
            return DeliveryTrackingStatus::PENDING;
        }

        if (in_array($value, ['accepted', 'accept'], true)) {
            return DeliveryTrackingStatus::ACCEPTED;
        }

        if (in_array($value, ['on_the_way', 'ontheway', 'in_transit'], true)) {
            return DeliveryTrackingStatus::ON_THE_WAY;
        }

        if (in_array($value, ['delivered', 'done', 'completed'], true)) {
            return DeliveryTrackingStatus::DELIVERED;
        }

        return strtoupper($status);
    }

    protected function deliveryMen()
    {
        return User::whereIn('user_type', ['dispatcher', 'courier'])
            ->where('blocked', 0)
            ->orderBy('name')
            ->get();
    }

    protected function authorizeAdmin(): void
    {
        AdminController::canAdmin();
    }
}
