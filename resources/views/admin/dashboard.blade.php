@php
    $users = $dashboard['users'];
    $vehicles = $dashboard['vehicles'];
    $vtu = $dashboard['vtu'];
    $deliveries = $dashboard['deliveries'];
    $payouts = $dashboard['payouts'];
    $reports = $dashboard['reports'];
    $logs = $dashboard['logs'];
    $payoutCurrency = config('delivery_payout.currency', 'EUR');
@endphp

<style>
    .admin-dashboard-band {
        border: 1px solid #e5e7eb;
        padding: 16px;
        margin-bottom: 18px;
    }

    .admin-dashboard-stat {
        border: 1px solid #edf2f7;
        padding: 14px;
        height: 100%;
        background: #fff;
    }

    .admin-dashboard-stat small {
        color: #667085;
        display: block;
        margin-bottom: 6px;
    }

    .admin-dashboard-stat h4 {
        font-size: 22px;
        margin: 0;
    }

    .admin-dashboard-table td,
    .admin-dashboard-table th {
        font-size: 12px;
        vertical-align: middle;
        white-space: nowrap;
    }
</style>

<div class="admin-dashboard">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Admin Dashboard</h4>
            <p class="text-muted mb-0">Operations overview for users, delivery men, vehicles, VTU, deliveries, payouts, reports, and logs.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.deliveries.web.index') }}" class="btn btn-sm btn-primary">Monitor Deliveries</a>
            <a href="{{ route('admin.delivery-payouts.web.index') }}" class="btn btn-sm btn-secondary">Payouts</a>
        </div>
    </div>

    <div class="admin-dashboard-band">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Users, Delivery Men, Vehicles</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('allUsers') }}" class="btn btn-sm btn-light">Users</a>
                <a href="{{ route('vehicles.index') }}" class="btn btn-sm btn-light">Vehicles</a>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-2">
                <div class="admin-dashboard-stat">
                    <small>Total Users</small>
                    <h4>{{ number_format($users['total']) }}</h4>
                </div>
            </div>
            <div class="col-md-2">
                <div class="admin-dashboard-stat">
                    <small>Active Users</small>
                    <h4>{{ number_format($users['active']) }}</h4>
                </div>
            </div>
            <div class="col-md-2">
                <div class="admin-dashboard-stat">
                    <small>Delivery Men</small>
                    <h4>{{ number_format($users['delivery_men']) }}</h4>
                </div>
            </div>
            <div class="col-md-2">
                <div class="admin-dashboard-stat">
                    <small>Total Vehicles</small>
                    <h4>{{ number_format($vehicles['total']) }}</h4>
                </div>
            </div>
            <div class="col-md-2">
                <div class="admin-dashboard-stat">
                    <small>Assigned Vehicles</small>
                    <h4>{{ number_format($vehicles['assigned']) }}</h4>
                </div>
            </div>
            <div class="col-md-2">
                <div class="admin-dashboard-stat">
                    <small>Unassigned Vehicles</small>
                    <h4>{{ number_format($vehicles['unassigned']) }}</h4>
                </div>
            </div>
        </div>
        <div class="row g-3 mt-1">
            <div class="col-md-3">
                <div class="admin-dashboard-stat">
                    <small>Bikes</small>
                    <h4>{{ number_format($vehicles['bike']) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="admin-dashboard-stat">
                    <small>Cars</small>
                    <h4>{{ number_format($vehicles['car']) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="admin-dashboard-stat">
                    <small>Vans</small>
                    <h4>{{ number_format($vehicles['van']) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="admin-dashboard-stat">
                    <small>Trucks</small>
                    <h4>{{ number_format($vehicles['truck']) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-dashboard-band">
        <h5 class="mb-3">Deliveries</h5>
        <div class="row g-3">
            <div class="col-md-2">
                <div class="admin-dashboard-stat">
                    <small>Total Deliveries</small>
                    <h4>{{ number_format($deliveries['total']) }}</h4>
                </div>
            </div>
            <div class="col-md-2">
                <div class="admin-dashboard-stat">
                    <small>Pending</small>
                    <h4>{{ number_format($deliveries['pending']) }}</h4>
                </div>
            </div>
            <div class="col-md-2">
                <div class="admin-dashboard-stat">
                    <small>Accepted</small>
                    <h4>{{ number_format($deliveries['accepted']) }}</h4>
                </div>
            </div>
            <div class="col-md-2">
                <div class="admin-dashboard-stat">
                    <small>On The Way</small>
                    <h4>{{ number_format($deliveries['on_the_way']) }}</h4>
                </div>
            </div>
            <div class="col-md-2">
                <div class="admin-dashboard-stat">
                    <small>Delivered</small>
                    <h4>{{ number_format($deliveries['delivered']) }}</h4>
                </div>
            </div>
            <div class="col-md-2">
                <div class="admin-dashboard-stat">
                    <small>Today</small>
                    <h4>{{ number_format($deliveries['today']) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="admin-dashboard-band h-100">
                <h5 class="mb-3">VTU Transactions</h5>
                <div class="row g-3 mb-3">
                    <div class="col-md-4"><div class="admin-dashboard-stat"><small>Total</small><h4>{{ number_format($vtu['total']) }}</h4></div></div>
                    <div class="col-md-4"><div class="admin-dashboard-stat"><small>Successful</small><h4>{{ number_format($vtu['successful']) }}</h4></div></div>
                    <div class="col-md-4"><div class="admin-dashboard-stat"><small>Pending</small><h4>{{ number_format($vtu['pending']) }}</h4></div></div>
                    <div class="col-md-4"><div class="admin-dashboard-stat"><small>Failed</small><h4>{{ number_format($vtu['failed']) }}</h4></div></div>
                    <div class="col-md-4"><div class="admin-dashboard-stat"><small>Today</small><h4>{{ number_format($vtu['today']) }}</h4></div></div>
                    <div class="col-md-4"><div class="admin-dashboard-stat"><small>Value</small><h4>{{ number_format((float) $vtu['amount'], 2) }}</h4></div></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered admin-dashboard-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vtu['recent'] as $transaction)
                                <tr>
                                    <td>{{ $transaction->reference }}</td>
                                    <td>{{ $transaction->service_type }}</td>
                                    <td>{{ $transaction->currency }} {{ number_format((float) $transaction->amount, 2) }}</td>
                                    <td>{{ $transaction->status }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">No VTU transactions yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="admin-dashboard-band h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Payouts</h5>
                    <a href="{{ route('admin.delivery-payouts.web.index') }}" class="btn btn-sm btn-light">Open Payouts</a>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4"><div class="admin-dashboard-stat"><small>Total</small><h4>{{ number_format($payouts['total']) }}</h4></div></div>
                    <div class="col-md-4"><div class="admin-dashboard-stat"><small>Pending</small><h4>{{ number_format($payouts['pending']) }}</h4></div></div>
                    <div class="col-md-4"><div class="admin-dashboard-stat"><small>Processing</small><h4>{{ number_format($payouts['processing']) }}</h4></div></div>
                    <div class="col-md-4"><div class="admin-dashboard-stat"><small>Paid</small><h4>{{ number_format($payouts['paid']) }}</h4></div></div>
                    <div class="col-md-4"><div class="admin-dashboard-stat"><small>Pending Amount</small><h4>{{ $payoutCurrency }} {{ number_format((float) $payouts['pending_amount'], 2) }}</h4></div></div>
                    <div class="col-md-4"><div class="admin-dashboard-stat"><small>Paid Amount</small><h4>{{ $payoutCurrency }} {{ number_format((float) $payouts['paid_amount'], 2) }}</h4></div></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered admin-dashboard-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Delivery Man</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payouts['recent'] as $payout)
                                <tr>
                                    <td><a href="{{ route('admin.delivery-payouts.web.show', $payout->reference) }}">{{ $payout->reference }}</a></td>
                                    <td>{{ optional($payout->deliveryMan)->name ?: '-' }}</td>
                                    <td>{{ $payout->currency }} {{ number_format((float) $payout->total_amount, 2) }}</td>
                                    <td>{{ $payout->status }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">No payouts yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-dashboard-band mt-3">
        <h5 class="mb-3">Reports & Logs</h5>
        <div class="row g-3 mb-3">
            <div class="col-md-2"><div class="admin-dashboard-stat"><small>Inquiries</small><h4>{{ number_format($reports['contacts']) }}</h4></div></div>
            <div class="col-md-2"><div class="admin-dashboard-stat"><small>Order Value</small><h4>{{ number_format((float) $reports['orders_value'], 2) }}</h4></div></div>
            <div class="col-md-2"><div class="admin-dashboard-stat"><small>Shipping Revenue</small><h4>{{ number_format((float) $reports['shipping_revenue'], 2) }}</h4></div></div>
            <div class="col-md-2"><div class="admin-dashboard-stat"><small>Paid Payments</small><h4>{{ number_format($reports['payments_done']) }}</h4></div></div>
            <div class="col-md-2"><div class="admin-dashboard-stat"><small>Tracking Logs</small><h4>{{ number_format($logs['tracking_events']) }}</h4></div></div>
            <div class="col-md-2"><div class="admin-dashboard-stat"><small>Payout Logs</small><h4>{{ number_format($logs['payout_logs']) }}</h4></div></div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <h6 class="fw-semibold">Latest Tracking Logs</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered admin-dashboard-table">
                        <thead>
                            <tr>
                                <th>Tracking ID</th>
                                <th>Status</th>
                                <th>Delivery Man</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs['latest_tracking_events'] as $event)
                                <tr>
                                    <td>{{ $event->tracking_id }}</td>
                                    <td>{{ $event->new_delivery_man_status ?: $event->new_status }}</td>
                                    <td>{{ optional($event->deliveryMan)->name ?: '-' }}</td>
                                    <td>{{ optional($event->created_at)->format('d M Y, h:i A') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">No tracking logs yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-6">
                <h6 class="fw-semibold">Latest Payout Logs</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered admin-dashboard-table">
                        <thead>
                            <tr>
                                <th>Payout</th>
                                <th>Action</th>
                                <th>By</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs['latest_payout_logs'] as $log)
                                <tr>
                                    <td>{{ $log->payout_reference ?: '-' }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ optional($log->performedBy)->name ?: '-' }}</td>
                                    <td>{{ optional($log->created_at)->format('d M Y, h:i A') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">No payout logs yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
