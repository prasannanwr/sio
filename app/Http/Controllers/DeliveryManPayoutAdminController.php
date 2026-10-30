<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\DeliveryManPayoutController as ApiDeliveryManPayoutController;
use App\Models\BankDetail;
use App\Models\DeliveryManPayout;
use App\Models\DeliveryManPayoutItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Throwable;

class DeliveryManPayoutAdminController extends Controller
{
    protected ApiDeliveryManPayoutController $api;

    public function __construct(ApiDeliveryManPayoutController $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $deliveryMen = $this->deliveryMen();
        $pendingDeliveriesQuery = $this->eligibleDeliveryQuery($request)->with(['deliveryMan.bankDetail']);
        $this->applyPendingDeliveryFilters($pendingDeliveriesQuery, $request);

        $pendingDeliveries = (clone $pendingDeliveriesQuery)
            ->latest()
            ->paginate(10, ['*'], 'pending_deliveries_page');
        $pendingDeliveries->getCollection()->transform(function (Order $order) {
            $order->payout_amount = $this->money($this->earningAmount($order, $order->deliveryMan));
            $order->commission_percentage = $order->deliveryMan ? $this->commissionPercentageFor($order->deliveryMan) : 0;

            return $order;
        });

        $pendingPayouts = $this->payoutQuery($request, [
            DeliveryManPayout::STATUS_PENDING,
            DeliveryManPayout::STATUS_PROCESSING,
        ])->paginate(10, ['*'], 'pending_payouts_page');

        $history = $this->payoutQuery($request)
            ->paginate(10, ['*'], 'history_page');

        $summaryOrders = $this->eligibleDeliveryQuery($request)->with('deliveryMan')->get();
        $earnings = $this->deliveryMenEarnings($request);
        $summary = [
            'pending_deliveries' => $summaryOrders->count(),
            'pending_delivery_amount' => $this->money($this->sumOrderEarnings($summaryOrders)),
            'pending_payout_amount' => $this->money(DeliveryManPayout::whereIn('status', [
                DeliveryManPayout::STATUS_PENDING,
                DeliveryManPayout::STATUS_PROCESSING,
            ])->sum('total_amount')),
            'paid_payout_amount' => $this->money(DeliveryManPayoutItem::where('status', DeliveryManPayoutItem::STATUS_PAID)->sum('amount')),
            'pending_payouts' => DeliveryManPayout::whereIn('status', [
                DeliveryManPayout::STATUS_PENDING,
                DeliveryManPayout::STATUS_PROCESSING,
            ])->count(),
            'paid_payouts' => DeliveryManPayout::where('status', DeliveryManPayout::STATUS_PAID)->count(),
            'currency' => config('delivery_payout.currency', 'EUR'),
        ];

        return view('admin.delivery_payouts.index', compact(
            'deliveryMen',
            'earnings',
            'history',
            'pendingDeliveries',
            'pendingPayouts',
            'summary'
        ));
    }

    public function show(string $reference)
    {
        $this->authorizeAdmin();

        $payout = DeliveryManPayout::with([
            'deliveryMan.bankDetail',
            'processedBy',
            'items.order',
            'logs.performedBy',
        ])->where('reference', $reference)->firstOrFail();

        return view('admin.delivery_payouts.show', compact('payout'));
    }

    public function make(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $request->merge([
            'payout_method' => $request->input('payout_method', 'bank_transfer'),
        ]);

        try {
            return $this->redirectFromApiResponse($this->api->makePayout($request), 'Payout created successfully.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            return back()->withInput()->with(['message' => $e->getMessage(), 'message_type' => 'danger']);
        }
    }

    public function selectedDeliveriesAction(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $request->validate([
            'action' => ['required', 'in:bulk_payout,mark_paid'],
            'order_ids' => ['required', 'array', 'min:1'],
            'order_ids.*' => ['integer', 'distinct'],
            'status' => ['nullable', 'in:PENDING,PAID'],
            'payout_method' => ['nullable', 'string', 'max:80'],
            'payment_reference' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $request->merge([
            'payout_method' => $request->input('payout_method', 'bank_transfer'),
        ]);

        try {
            if ($request->input('action') === 'mark_paid') {
                return $this->redirectFromApiResponse($this->api->markDeliveriesPaid($request), 'Deliveries marked as paid successfully.');
            }

            $payouts = $this->bulkPayoutPayloadFromOrders($request->input('order_ids', []));
            $request->merge(['payouts' => $payouts]);

            return $this->redirectFromApiResponse($this->api->bulkPayout($request), 'Bulk payout created successfully.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            return back()->withInput()->with(['message' => $e->getMessage(), 'message_type' => 'danger']);
        }
    }

    public function markPaid(Request $request, string $reference): RedirectResponse
    {
        $this->authorizeAdmin();

        $request->merge([
            'payout_method' => $request->input('payout_method', 'bank_transfer'),
        ]);

        try {
            return $this->redirectFromApiResponse($this->api->markPaid($request, $reference), 'Payout marked as paid successfully.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            return back()->withInput()->with(['message' => $e->getMessage(), 'message_type' => 'danger']);
        }
    }

    public function markFailed(Request $request, string $reference): RedirectResponse
    {
        $this->authorizeAdmin();

        try {
            return $this->redirectFromApiResponse($this->api->markFailed($request, $reference), 'Payout marked as failed successfully.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            return back()->withInput()->with(['message' => $e->getMessage(), 'message_type' => 'danger']);
        }
    }

    public function cancel(Request $request, string $reference): RedirectResponse
    {
        $this->authorizeAdmin();

        try {
            return $this->redirectFromApiResponse($this->api->cancel($request, $reference), 'Payout cancelled successfully.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            return back()->withInput()->with(['message' => $e->getMessage(), 'message_type' => 'danger']);
        }
    }

    public function saveDeliveryManBankDetail(Request $request, User $deliveryMan): RedirectResponse
    {
        $this->authorizeAdmin();

        if (! in_array($deliveryMan->user_type, ['dispatcher', 'courier'], true)) {
            throw ValidationException::withMessages([
                'delivery_man_id' => ['Selected user is not a delivery man.'],
            ]);
        }

        $data = $request->validate([
            'bank_delivery_man_id' => ['nullable', 'integer'],
            'bank_name' => ['required', 'string', 'max:120'],
            'bank_code' => ['nullable', 'string', 'max:80'],
            'account_name' => ['nullable', 'string', 'max:120'],
            'account_number' => ['nullable', 'string', 'max:80'],
            'iban' => ['nullable', 'string', 'max:120'],
            'routing_number' => ['nullable', 'string', 'max:80'],
        ]);

        $accountNumber = trim((string) ($data['account_number'] ?? ''));
        $iban = trim((string) ($data['iban'] ?? ''));
        $bankCode = trim((string) ($data['bank_code'] ?? ''));

        if ($accountNumber === '' && $iban === '') {
            throw ValidationException::withMessages([
                'account_number' => ['Enter an account number or IBAN for this delivery man.'],
            ]);
        }

        if ($accountNumber !== '' && $bankCode === '') {
            throw ValidationException::withMessages([
                'bank_code' => ['Bank code is required when using an account number for Monnify payouts.'],
            ]);
        }

        DB::transaction(function () use ($deliveryMan, $data, $accountNumber, $iban, $bankCode) {
            $deliveryMan->loadMissing('bankDetail');
            $bankDetail = $deliveryMan->bankDetail;

            if ($bankDetail && User::where('bank_detail_id', $bankDetail->id)->where('id', '!=', $deliveryMan->id)->exists()) {
                $bankDetail = new BankDetail();
            }

            if (! $bankDetail) {
                $bankDetail = new BankDetail();
            }

            $bankDetail->fill([
                'bank_name' => $data['bank_name'],
                'bank_code' => $bankCode ?: null,
                'account_name' => ($data['account_name'] ?? null) ?: null,
                'acount_number' => $accountNumber ?: null,
                'iban' => $iban ?: null,
                'routing_number' => ($data['routing_number'] ?? null) ?: null,
                'country_id' => $bankDetail->country_id ?: ($deliveryMan->country ?: null),
                'city_id' => $bankDetail->city_id,
                'status' => 'active',
            ]);
            $bankDetail->save();

            if ((int) $deliveryMan->bank_detail_id !== (int) $bankDetail->id) {
                $deliveryMan->forceFill(['bank_detail_id' => $bankDetail->id])->save();
            }
        });

        return back()->with([
            'message' => 'Delivery man payout account saved successfully.',
            'message_type' => 'success',
        ]);
    }

    protected function redirectFromApiResponse($response, string $defaultSuccessMessage): RedirectResponse
    {
        $payload = $response->getData(true);

        if (($payload['status'] ?? false) !== true) {
            if (! empty($payload['errors'])) {
                return back()->withInput()->withErrors($payload['errors']);
            }

            return back()->withInput()->with([
                'message' => $payload['message'] ?? 'The payout action could not be completed.',
                'message_type' => 'danger',
            ]);
        }

        return back()->with([
            'message' => $payload['message'] ?? $defaultSuccessMessage,
            'message_type' => 'success',
        ]);
    }

    protected function bulkPayoutPayloadFromOrders(array $orderIds): array
    {
        $orders = Order::whereIn('id', $orderIds)
            ->whereNotNull('dispatcher_id')
            ->get(['id', 'dispatcher_id'])
            ->groupBy('dispatcher_id');

        return $orders->map(function (Collection $deliveryOrders, $deliveryManId) {
            return [
                'delivery_man_id' => (int) $deliveryManId,
                'order_ids' => $deliveryOrders->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
            ];
        })->values()->all();
    }

    protected function deliveryMenEarnings(Request $request): Collection
    {
        return $this->deliveredOrderQuery($request)
            ->with(['deliveryMan.bankDetail', 'deliveryPayoutItems'])
            ->get()
            ->filter(fn (Order $order) => $order->deliveryMan)
            ->groupBy('dispatcher_id')
            ->map(function (Collection $deliveryOrders) {
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
                    'delivery_man' => $deliveryMan,
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
            })
            ->values();
    }

    protected function payoutQuery(Request $request, ?array $statuses = null): Builder
    {
        $query = DeliveryManPayout::with(['deliveryMan.bankDetail', 'processedBy'])->latest();

        if ($statuses !== null) {
            $query->whereIn('status', $statuses);
        }

        if ($request->filled('delivery_man_id')) {
            $query->where('delivery_man_id', $request->delivery_man_id);
        }

        if ($request->filled('status')) {
            $query->where('status', strtoupper((string) $request->status));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function (Builder $query) use ($search) {
                $query->where('reference', 'like', "%{$search}%")
                    ->orWhere('payment_reference', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    protected function applyPendingDeliveryFilters(Builder $query, Request $request): void
    {
        if ($request->filled('delivery_man_id')) {
            $query->where('dispatcher_id', $request->delivery_man_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function (Builder $query) use ($search) {
                $query->where('tracking_id', 'like', "%{$search}%")
                    ->orWhere('pickup_name', 'like', "%{$search}%")
                    ->orWhere('delivery_name', 'like', "%{$search}%")
                    ->orWhere('delivery_phone', 'like', "%{$search}%");
            });
        }
    }

    protected function deliveredOrderQuery(Request $request): Builder
    {
        $query = Order::query()
            ->whereNotNull('dispatcher_id')
            ->where(function (Builder $query) {
                $query->where('delivery_man_status', config('delivery_payout.eligible_delivery_man_status', 'Delivered'))
                    ->orWhereIn('status', config('delivery_payout.eligible_order_statuses', [
                        'delivered',
                        'Customer Received',
                        'Accepted By Agent',
                    ]));
            });

        if ($request->filled('from')) {
            $query->whereDate('delivery_time', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('delivery_time', '<=', $request->to);
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

    protected function deliveryMen()
    {
        return User::whereIn('user_type', ['dispatcher', 'courier'])
            ->where('blocked', 0)
            ->orderBy('name')
            ->get();
    }

    protected function money($amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }

    protected function authorizeAdmin(): void
    {
        AdminController::canAdmin();
    }
}
