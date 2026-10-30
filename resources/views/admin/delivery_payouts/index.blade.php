@extends('admin.app')
@section('page_title', 'Delivery Man Payouts')
@section('content')
    @php
        $currency = $summary['currency'] ?? config('delivery_payout.currency', 'EUR');
        $statusClasses = [
            'PENDING' => 'warning',
            'PROCESSING' => 'info',
            'PAID' => 'success',
            'FAILED' => 'danger',
            'CANCELLED' => 'secondary',
        ];
    @endphp

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Delivery Man Payouts</h5>
                </div>

                @include('admin.partials.notification')

                <form action="{{ route('admin.delivery-payouts.web.index') }}" method="GET" class="row g-2 mb-4">
                    <div class="col-md-2">
                        <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <select name="delivery_man_id" class="form-control">
                            <option value="">All delivery men</option>
                            @foreach ($deliveryMen as $deliveryMan)
                                <option value="{{ $deliveryMan->id }}" @if ((string) request('delivery_man_id') === (string) $deliveryMan->id) selected @endif>
                                    {{ $deliveryMan->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search tracking, customer or payout reference">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-secondary w-100">Filter</button>
                        <a href="{{ route('admin.delivery-payouts.web.index') }}" class="btn btn-light">Reset</a>
                    </div>
                </form>

                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <div class="border p-3 h-100">
                            <small class="text-muted">Pending Deliveries</small>
                            <h4 class="mb-0">{{ $summary['pending_deliveries'] }}</h4>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border p-3 h-100">
                            <small class="text-muted">Pending Delivery Amount</small>
                            <h4 class="mb-0">{{ $currency }} {{ $summary['pending_delivery_amount'] }}</h4>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border p-3 h-100">
                            <small class="text-muted">Pending Payout Amount</small>
                            <h4 class="mb-0">{{ $currency }} {{ $summary['pending_payout_amount'] }}</h4>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border p-3 h-100">
                            <small class="text-muted">Paid Payout Amount</small>
                            <h4 class="mb-0">{{ $currency }} {{ $summary['paid_payout_amount'] }}</h4>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border p-3 h-100">
                            <small class="text-muted">Pending Payouts</small>
                            <h4 class="mb-0">{{ $summary['pending_payouts'] }}</h4>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border p-3 h-100">
                            <small class="text-muted">Paid Payouts</small>
                            <h4 class="mb-0">{{ $summary['paid_payouts'] }}</h4>
                        </div>
                    </div>
                </div>

                <h5 class="fw-semibold mb-3">Delivery Men Earnings</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Delivery Man</th>
                                <th>Commission</th>
                                <th>Delivered</th>
                                <th>Paid Orders</th>
                                <th>Pending Orders</th>
                                <th>Total Earned</th>
                                <th>Paid</th>
                                <th>Locked</th>
                                <th>Pending Amount</th>
                                <th style="width: 260px;">Make Payout</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($earnings as $earning)
                                @php
                                    $deliveryMan = $earning['delivery_man'];
                                    $bankDetail = $deliveryMan->bankDetail;
                                    $accountNumber = $bankDetail ? ($bankDetail->acount_number ?: $bankDetail->iban) : null;
                                    $bankCode = $bankDetail ? ($bankDetail->bank_code ?: $bankDetail->routing_number) : null;
                                    $hasAnyPayoutAccount = $bankDetail && $accountNumber;
                                    $hasMonnifyAccount = $bankDetail && $bankDetail->acount_number && $bankCode;
                                    $bankFormOpen = (string) old('bank_delivery_man_id') === (string) $deliveryMan->id;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $deliveryMan->name }}</strong><br>
                                        <small>{{ $deliveryMan->phone ?? $deliveryMan->email }}</small><br>
                                        @if ($hasMonnifyAccount)
                                            <span class="badge bg-success mt-1">Monnify account ready</span>
                                        @elseif ($hasAnyPayoutAccount)
                                            <span class="badge bg-info mt-1">Manual account saved</span>
                                        @else
                                            <span class="badge bg-warning mt-1">No payout account</span>
                                        @endif
                                        @if ($bankDetail)
                                            <div class="small text-muted mt-1">
                                                {{ $bankDetail->bank_name }}
                                                @if ($accountNumber)
                                                    - {{ $accountNumber }}
                                                @endif
                                            </div>
                                        @endif
                                        <button class="btn btn-sm btn-outline-secondary mt-2" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#deliveryManBank{{ $deliveryMan->id }}"
                                            aria-expanded="{{ $bankFormOpen ? 'true' : 'false' }}"
                                            aria-controls="deliveryManBank{{ $deliveryMan->id }}">
                                            {{ $bankDetail ? 'Edit Account' : 'Add Account' }}
                                        </button>
                                    </td>
                                    <td>{{ $earning['commission_percentage'] }}%</td>
                                    <td>{{ $earning['delivered_orders'] }}</td>
                                    <td>{{ $earning['paid_orders'] }}</td>
                                    <td>{{ $earning['pending_orders'] }}</td>
                                    <td>{{ $currency }} {{ $earning['total_earned'] }}</td>
                                    <td>{{ $currency }} {{ $earning['total_paid'] }}</td>
                                    <td>{{ $currency }} {{ $earning['locked_in_pending_payouts'] }}</td>
                                    <td>{{ $currency }} {{ $earning['pending_amount'] }}</td>
                                    <td>
                                        @if ((float) $earning['pending_amount'] > 0)
                                            <form action="{{ route('admin.delivery-payouts.web.make') }}" method="POST" class="row g-1">
                                                @csrf
                                                <input type="hidden" name="delivery_man_id" value="{{ $deliveryMan->id }}">
                                                <div class="col-6">
                                                    <select name="status" class="form-control form-control-sm">
                                                        <option value="PAID">Paid</option>
                                                        <option value="PENDING">Pending</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <select name="payout_method" class="form-control form-control-sm">
                                                        <option value="bank_transfer">Bank transfer</option>
                                                        <option value="cash">Cash</option>
                                                        <option value="monnify" @unless ($hasMonnifyAccount) disabled @endunless>
                                                            Monnify{{ $hasMonnifyAccount ? '' : ' - add account first' }}
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <input type="text" name="payment_reference" class="form-control form-control-sm"
                                                        placeholder="Payment reference">
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-sm btn-primary w-100">Create Payout</button>
                                                </div>
                                            </form>
                                        @else
                                            <span class="text-muted">No eligible balance</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="10" class="p-0 border-0">
                                        <div class="collapse {{ $bankFormOpen ? 'show' : '' }}"
                                            id="deliveryManBank{{ $deliveryMan->id }}">
                                            <div class="border p-3 mb-2 bg-light">
                                                <form action="{{ route('admin.delivery-payouts.web.delivery-men.bank-detail', $deliveryMan->id) }}"
                                                    method="POST" class="row g-2">
                                                    @csrf
                                                    <input type="hidden" name="bank_delivery_man_id" value="{{ $deliveryMan->id }}">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Bank Name</label>
                                                        <input type="text" name="bank_name"
                                                            value="{{ $bankFormOpen ? old('bank_name', optional($bankDetail)->bank_name) : optional($bankDetail)->bank_name }}"
                                                            class="form-control form-control-sm @if ($bankFormOpen) @error('bank_name') is-invalid @enderror @endif"
                                                            required>
                                                        @if ($bankFormOpen)
                                                            @error('bank_name')
                                                                <span class="invalid-feedback">{{ $message }}</span>
                                                            @enderror
                                                        @endif
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Bank Code</label>
                                                        <input type="text" name="bank_code"
                                                            value="{{ $bankFormOpen ? old('bank_code', $bankCode) : $bankCode }}"
                                                            class="form-control form-control-sm @if ($bankFormOpen) @error('bank_code') is-invalid @enderror @endif"
                                                            placeholder="Monnify code">
                                                        @if ($bankFormOpen)
                                                            @error('bank_code')
                                                                <span class="invalid-feedback">{{ $message }}</span>
                                                            @enderror
                                                        @endif
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Account Name</label>
                                                        <input type="text" name="account_name"
                                                            value="{{ $bankFormOpen ? old('account_name', optional($bankDetail)->account_name) : optional($bankDetail)->account_name }}"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Account Number</label>
                                                        <input type="text" name="account_number"
                                                            value="{{ $bankFormOpen ? old('account_number', optional($bankDetail)->acount_number) : optional($bankDetail)->acount_number }}"
                                                            class="form-control form-control-sm @if ($bankFormOpen) @error('account_number') is-invalid @enderror @endif">
                                                        @if ($bankFormOpen)
                                                            @error('account_number')
                                                                <span class="invalid-feedback">{{ $message }}</span>
                                                            @enderror
                                                        @endif
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">IBAN</label>
                                                        <input type="text" name="iban"
                                                            value="{{ $bankFormOpen ? old('iban', optional($bankDetail)->iban) : optional($bankDetail)->iban }}"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Routing Number</label>
                                                        <input type="text" name="routing_number"
                                                            value="{{ $bankFormOpen ? old('routing_number', optional($bankDetail)->routing_number) : optional($bankDetail)->routing_number }}"
                                                            class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-md-3 d-flex align-items-end">
                                                        <button type="submit" class="btn btn-sm btn-primary w-100">Save Payout Account</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No delivery earnings found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h5 class="fw-semibold mb-3">Pending Deliveries</h5>
                <form action="{{ route('admin.delivery-payouts.web.selected-deliveries') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 40px;"><input type="checkbox" id="selectAllPendingDeliveries"></th>
                                    <th>Tracking ID</th>
                                    <th>Delivery Man</th>
                                    <th>Customer</th>
                                    <th>Delivered At</th>
                                    <th>Shipping Cost</th>
                                    <th>Commission</th>
                                    <th>Payout Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendingDeliveries as $order)
                                    <tr>
                                        <td><input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="pending-delivery-checkbox"></td>
                                        <td>{{ $order->tracking_id }}</td>
                                        <td>
                                            @if ($order->deliveryMan)
                                                <strong>{{ $order->deliveryMan->name }}</strong><br>
                                                <small>{{ $order->deliveryMan->phone ?? $order->deliveryMan->email }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $order->delivery_name }}<br>
                                            <small>{{ $order->delivery_phone }}</small>
                                        </td>
                                        <td>{{ $order->delivery_time ? \Carbon\Carbon::parse($order->delivery_time)->format('d M Y, h:i A') : '-' }}</td>
                                        <td>{{ $currency }} {{ number_format((float) $order->shipping_cost, 2) }}</td>
                                        <td>{{ $order->commission_percentage }}%</td>
                                        <td>{{ $currency }} {{ $order->payout_amount }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No pending deliveries found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">Payout Status</label>
                            <select name="status" class="form-control">
                                <option value="PAID">Paid</option>
                                <option value="PENDING">Pending</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Method</label>
                            <select name="payout_method" class="form-control">
                                <option value="bank_transfer">Bank transfer</option>
                                <option value="cash">Cash</option>
                                <option value="monnify">Monnify</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Payment Reference</label>
                            <input type="text" name="payment_reference" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Notes</label>
                            <input type="text" name="notes" class="form-control">
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" name="action" value="bulk_payout" class="btn btn-primary w-100">Bulk Payout</button>
                            <button type="submit" name="action" value="mark_paid" class="btn btn-success w-100">Mark Paid</button>
                        </div>
                    </div>
                    <div class="mt-3">
                        {{ $pendingDeliveries->appends(request()->query())->links() }}
                    </div>
                </form>

                <h5 class="fw-semibold mb-3">Pending Payouts</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Delivery Man</th>
                                <th>Amount</th>
                                <th>Orders</th>
                                <th>Status</th>
                                <th>Requested</th>
                                <th style="width: 310px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendingPayouts as $payout)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.delivery-payouts.web.show', $payout->reference) }}">
                                            {{ $payout->reference }}
                                        </a>
                                    </td>
                                    <td>{{ optional($payout->deliveryMan)->name }}</td>
                                    <td>{{ $payout->currency }} {{ number_format((float) $payout->total_amount, 2) }}</td>
                                    <td>{{ $payout->orders_count }}</td>
                                    <td><span class="badge bg-{{ $statusClasses[$payout->status] ?? 'secondary' }}">{{ $payout->status }}</span></td>
                                    <td>{{ optional($payout->requested_at ?: $payout->created_at)->format('d M Y, h:i A') }}</td>
                                    <td>
                                        <form action="{{ route('admin.delivery-payouts.web.mark-paid', $payout->reference) }}" method="POST" class="d-inline-block mb-1">
                                            @csrf
                                            <input type="hidden" name="payout_method" value="{{ $payout->payout_method ?: 'bank_transfer' }}">
                                            <button type="submit" class="btn btn-sm btn-success">Mark Paid</button>
                                        </form>
                                        <form action="{{ route('admin.delivery-payouts.web.mark-failed', $payout->reference) }}" method="POST" class="d-inline-block mb-1">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">Failed</button>
                                        </form>
                                        <form action="{{ route('admin.delivery-payouts.web.cancel', $payout->reference) }}" method="POST" class="d-inline-block mb-1">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Cancel this payout?')">Cancel</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No pending payouts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $pendingPayouts->appends(request()->query())->links() }}
                </div>

                <h5 class="fw-semibold mb-3">Payout History</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Delivery Man</th>
                                <th>Amount</th>
                                <th>Orders</th>
                                <th>Status</th>
                                <th>Method</th>
                                <th>Payment Reference</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($history as $payout)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.delivery-payouts.web.show', $payout->reference) }}">
                                            {{ $payout->reference }}
                                        </a>
                                    </td>
                                    <td>{{ optional($payout->deliveryMan)->name }}</td>
                                    <td>{{ $payout->currency }} {{ number_format((float) $payout->total_amount, 2) }}</td>
                                    <td>{{ $payout->orders_count }}</td>
                                    <td><span class="badge bg-{{ $statusClasses[$payout->status] ?? 'secondary' }}">{{ $payout->status }}</span></td>
                                    <td>{{ $payout->payout_method ?: '-' }}</td>
                                    <td>{{ $payout->payment_reference ?: '-' }}</td>
                                    <td>{{ optional($payout->created_at)->format('d M Y, h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No payout history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $history->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).on('change', '#selectAllPendingDeliveries', function() {
            $('.pending-delivery-checkbox').prop('checked', $(this).prop('checked'));
        });
    </script>
@endsection
