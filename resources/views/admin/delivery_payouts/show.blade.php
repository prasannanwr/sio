@extends('admin.app')
@section('page_title', 'Delivery Payout Details')
@section('content')
    @php
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
                    <h5 class="card-title fw-semibold mb-0">Payout {{ $payout->reference }}</h5>
                    <a href="{{ route('admin.delivery-payouts.web.index') }}" class="btn btn-primary">All Payouts</a>
                </div>

                @include('admin.partials.notification')

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="border p-3 h-100">
                            <small class="text-muted">Delivery Man</small>
                            <h6 class="mb-1">{{ optional($payout->deliveryMan)->name ?: '-' }}</h6>
                            <small>{{ optional($payout->deliveryMan)->phone ?? optional($payout->deliveryMan)->email }}</small>
                            @if ($payout->deliveryMan && $payout->deliveryMan->bankDetail)
                                <div class="small text-muted mt-2">
                                    {{ $payout->deliveryMan->bankDetail->bank_name }}
                                    @if ($payout->deliveryMan->bankDetail->acount_number || $payout->deliveryMan->bankDetail->iban)
                                        - {{ $payout->deliveryMan->bankDetail->acount_number ?: $payout->deliveryMan->bankDetail->iban }}
                                    @endif
                                </div>
                            @else
                                <div class="small text-warning mt-2">No payout account saved</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border p-3 h-100">
                            <small class="text-muted">Amount</small>
                            <h5 class="mb-0">{{ $payout->currency }} {{ number_format((float) $payout->total_amount, 2) }}</h5>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border p-3 h-100">
                            <small class="text-muted">Orders</small>
                            <h5 class="mb-0">{{ $payout->orders_count }}</h5>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border p-3 h-100">
                            <small class="text-muted">Status</small><br>
                            <span class="badge bg-{{ $statusClasses[$payout->status] ?? 'secondary' }}">{{ $payout->status }}</span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border p-3 h-100">
                            <small class="text-muted">Method</small>
                            <h6 class="mb-0">{{ $payout->payout_method ?: '-' }}</h6>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 220px;">Payment Reference</th>
                                <td>{{ $payout->payment_reference ?: '-' }}</td>
                                <th style="width: 160px;">Processed By</th>
                                <td>{{ optional($payout->processedBy)->name ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Requested At</th>
                                <td>{{ optional($payout->requested_at ?: $payout->created_at)->format('d M Y, h:i A') }}</td>
                                <th>Paid At</th>
                                <td>{{ optional($payout->paid_at)->format('d M Y, h:i A') ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Notes</th>
                                <td colspan="3">{{ $payout->notes ?: '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if ($payout->status !== \App\Models\DeliveryManPayout::STATUS_PAID)
                    <div class="border p-3 mb-4">
                        <form action="{{ route('admin.delivery-payouts.web.mark-paid', $payout->reference) }}" method="POST" class="row g-2 align-items-end">
                            @csrf
                            <div class="col-md-3">
                                <label class="form-label">Method</label>
                                <select name="payout_method" class="form-control">
                                    <option value="bank_transfer" @if (($payout->payout_method ?: 'bank_transfer') === 'bank_transfer') selected @endif>Bank transfer</option>
                                    <option value="cash" @if ($payout->payout_method === 'cash') selected @endif>Cash</option>
                                    <option value="monnify" @if ($payout->payout_method === 'monnify') selected @endif>Monnify</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Payment Reference</label>
                                <input type="text" name="payment_reference" value="{{ $payout->payment_reference }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Notes</label>
                                <input type="text" name="notes" value="{{ $payout->notes }}" class="form-control">
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-success w-100">Mark Paid</button>
                            </div>
                        </form>

                        <div class="mt-2 d-flex gap-2">
                            <form action="{{ route('admin.delivery-payouts.web.mark-failed', $payout->reference) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">Mark Failed</button>
                            </form>
                            <form action="{{ route('admin.delivery-payouts.web.cancel', $payout->reference) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Cancel this payout?')">Cancel Payout</button>
                            </form>
                        </div>
                    </div>
                @endif

                <h5 class="fw-semibold mb-3">Payout Deliveries</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Tracking ID</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Delivery</th>
                                <th>Paid At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payout->items as $item)
                                <tr>
                                    <td>
                                        @if ($item->order)
                                            <a href="{{ route('orders.show', $item->order->id) }}">{{ $item->tracking_id }}</a>
                                        @else
                                            {{ $item->tracking_id }}
                                        @endif
                                    </td>
                                    <td>{{ $item->currency }} {{ number_format((float) $item->amount, 2) }}</td>
                                    <td><span class="badge bg-{{ $statusClasses[$item->status] ?? 'secondary' }}">{{ $item->status }}</span></td>
                                    <td>
                                        @if ($item->order)
                                            {{ $item->order->delivery_name }}<br>
                                            <small>{{ $item->order->delivery_phone }}</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ optional($item->paid_at)->format('d M Y, h:i A') ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No deliveries found for this payout.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h5 class="fw-semibold mb-3">Payout Logs</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Action</th>
                                <th>Status Change</th>
                                <th>Performed By</th>
                                <th>Message</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payout->logs->sortByDesc('created_at') as $log)
                                <tr>
                                    <td>{{ optional($log->created_at)->format('d M Y, h:i A') }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td>
                                        {{ $log->old_status ?: '-' }}
                                        <span class="mx-1">to</span>
                                        {{ $log->new_status ?: '-' }}
                                    </td>
                                    <td>{{ optional($log->performedBy)->name ?: '-' }}</td>
                                    <td>{{ $log->message }}</td>
                                    <td>{{ $log->ip_address }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No logs found for this payout.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
