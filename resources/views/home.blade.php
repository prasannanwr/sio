@extends('admin.app')
@section('page_title', 'Home')
@section('content')
    <style>
        .card {
            height: 100%;
        }

        /* Style for the card container */
        .trans {
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease-in-out;
        }

        /* Hover effect */
        .trans:hover::before {
            opacity: 1;
        }

        .trans:hover.trans {
            transform: scale(1.08);
            /* Adjust the scale value for the zoom effect */
        }

        .dashboard_ul li {
            width: 20%;
        }

        .card_h2 {
            font-size: 18px;
            white-space: nowrap;
        }
    </style>

    {{-- @dd($dispatcher_rep);  --}}
    <div class="container-fluid">
        <div class="card h-auto">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">{{ __('admin.menus.welcome') }}</h5>

                @if (Auth::user()->user_type == 'admin' && !empty($admin_dashboard))
                    @include('admin.dashboard', ['dashboard' => $admin_dashboard])
                @endif

                {{-- @if (Auth::user()->user_type == 'user' || optional(auth()->user()->admin)->can == 'all')
                    <div class="row">
                        <h5>User Reports</h5>
                        <div class="col-sm-2">
                            <div class="card trans">
                                <div class="card-body">
                                    <h6>All Orders</h6>
                                    <h4>{{ $user_rep['total_orders'] }}</h4>
                                    <a href="{{ route('orders.index') }}">See All</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card trans">
                                <div class="card-body">
                                    <h6>Unpaid Orders</h6>
                                    <h4>{{ $user_rep['total_unpaid_orders'] }}</h4>
                                    <a href="{{ route('orders.index') }}">See All</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card trans">
                                <div class="card-body">
                                    <h6>Paid Orders</h6>
                                    <h4>{{ $user_rep['totsl_paid_orders'] }}</h4>
                                    <a href="{{ route('orders.index') }}">See All</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card trans">
                                <div class="card-body">
                                    <h6>Orders in transit</h6>
                                    <h4>{{ $user_rep['total_in_transit_orders'] }}</h4>
                                    <a href="{{ route('orders.index') }}">See All</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card trans">
                                <div class="card-body">
                                    <h6>Orders delivered</h6>
                                    <h4>{{ $user_rep['total_delivered_orders'] }}</h4>
                                    <a href="{{ route('orders.index') }}">See All</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card trans">
                                <div class="card-body">
                                    <h6>Orders canceled</h6>
                                    <h4>{{ $user_rep['total_cancelled_orders'] }}</h4>
                                    <a href="{{ route('orders.index') }}">See All</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card trans">
                                <div class="card-body">
                                    <h6>All Payments</h6>
                                    <h4>{{ $user_rep['total_payments'] }}</h4>
                                    <a href="{{ route('my.payments') }}">See All</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card trans">
                                <div class="card-body">
                                    <h6>All Pending Payments</h6>
                                    <h4>{{ $user_rep['total_payments_pending'] }}</h4>
                                    <a href="{{ route('my.payments') }}">See All</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card trans">
                                <div class="card-body">
                                    <h6>All Failed Payments</h6>
                                    <h4>{{ $user_rep['total_payments_failed'] }}</h4>
                                    <a href="{{ route('my.payments') }}">See All</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card trans">
                                <div class="card-body">
                                    <h6>Total Paid(&euro;)</h6>
                                    <h4>{{ number_format($user_rep['total_payments_value'], 2) }}</h4>
                                    <a href="{{ route('my.payments') }}">See All</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                @endif --}}
                {{-- @if (Auth::user()->user_type == 'dispatcher' || optional(auth()->user()->admin)->can == 'all')
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="dispatch-actions-tab" data-bs-toggle="tab"
                                data-bs-target="#dispatch-actions-tab-pane" type="button" role="tab"
                                aria-controls="dispatch-actions-tab-pane" aria-selected="true">Agent Actions</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="dispatch-reports-tab" data-bs-toggle="tab"
                                data-bs-target="#dispatch-reports-tab-pane" type="button" role="tab"
                                aria-controls="dispatch-reports-tab-pane" aria-selected="false">Agent Reports</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="dispatch-actions-tab-pane" role="tabpanel"
                            aria-labelledby="home-tab" tabindex="0">
                            <div class="row mt-2">
                                <div class="col-sm-2">
                                    <div class="card trans" href="{{ route('walk_in_customer_order.create') }}">
                                        <img src="{{ asset('admin_assets/assets/images/ship-boat.jpg') }}"
                                            class="card-img-top" alt="Shipping">
                                        <div class="card-body">
                                            <h5 class="card-title">Shipping</h5>
                                            <a href="{{ route('walk_in_customer_order.create') }}">Create Order</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="card trans">
                                        <img src="{{ asset('admin_assets/assets/images/transfer.png') }}"
                                            class="card-img-top" alt="...">
                                        <div class="card-body">
                                            <h5 class="card-title">EU Funds</h5>
                                            <a href="{{ route('eu_fund_trasfer_order.index') }}">Create Order</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="card trans">
                                        <img src="{{ asset('admin_assets/assets/images/international.jpg') }}"
                                            class="card-img-top" alt="...">
                                        <div class="card-body">
                                            <h5 class="card-title">Intl. Funds</h5>
                                            <a href="{{ route('intl_fund_trasfer_order.index') }}">Create Order</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="dispatch-reports-tab-pane" role="tabpanel"
                            aria-labelledby="profile-tab" tabindex="0">
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="card trans">
                                        <div class="card-body">
                                            <h6>Total Orders Accepted</h6>
                                            <h4>{{ $dispatcher_rep['total_orders'] }}</h4>
                                            <a href="{{ route('dispatchOrders') }}">See All</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="card trans">
                                        <div class="card-body">
                                            <h6>Total Orders Accepted [In Transit]</h6>
                                            <h4>{{ $dispatcher_rep['total_in_transit_orders'] }}</h4>
                                            <a href="{{ route('dispatchOrders') }}">See All</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="card trans">
                                        <div class="card-body">
                                            <h6>Total Orders Accepted [Delivered]</h6>
                                            <h4>{{ $dispatcher_rep['total_delivered_orders'] }}</h4>
                                            <a href="{{ route('dispatchOrders') }}">See All</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="card trans">
                                        <div class="card-body">
                                            <h6>Total Orders Accepted [Picked-Up]</h6>
                                            <h4>{{ $dispatcher_rep['total_picked_up_orders'] }}</h4>
                                            <a href="{{ route('dispatchOrders') }}">See All</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="card trans">
                                        <div class="card-body">
                                            <h6>Total Orders Accepted [Canceled]</h6>
                                            <h4>{{ $dispatcher_rep['total_cancelled_orders'] }}</h4>
                                            <a href="{{ route('dispatchOrders') }}">See All</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                @endif --}}

                @if (Auth::user()->user_type == 'admin')
                    {{-- <div class="row">
                        <div class="col-sm-2">
                            <div class="card " href="">
                                <img src="{{ asset('admin_assets/assets/images/user_icon.jpg') }}" class="card-img-top"
                                    alt="Shipping">
                                <div class="card-body">
                                    <h5 class="card-title text-nowrap">Total Users : {{ $totalUsers }}</h5>
                                    <a href="{{ route('allUsers') }}">Manage</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card" href="">
                                <img src="{{ asset('admin_assets/assets/images/kyc.jpg') }}" class="card-img-top"
                                    alt="Shipping">
                                <div class="card-body">
                                    <h5 class="card-title">KYC</h5>
                                    <a href="{{ route('walk_in_customers.index') }}">Manage</a>
                                </div>
                            </div>
                        </div>
                       
                        <div class="col-sm-2">
                            <div class="card trans" href="">
                                <img src="{{ asset('admin_assets/assets/images/ship-boat.jpg') }}" class="card-img-top"
                                    alt="Shipping">
                                <div class="card-body">
                                    <h5 class="card-title">Shipping</h5>
                                    <a href="{{ route('allOrders') }}">Manage</a>
                                </div>
                            </div>
                        </div>

                    </div> --}}


                    {{-- <ul class="nav nav-tabs" id="adminTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="admin-actions-tab" data-bs-toggle="tab"
                                data-bs-target="#admin-actions-tab-pane" type="button" role="tab"
                                aria-controls="admin-actions-tab-pane" aria-selected="true">Admin Actions</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="admin-reports-tab" data-bs-toggle="tab"
                                data-bs-target="#admin-reports-tab-pane" type="button" role="tab"
                                aria-controls="admin-reports-tab-pane" aria-selected="false">Admin Reports</button>
                        </li>
                    </ul> --}}
                    <div class="tab-content" id="adminTabContent">
                        <div class="tab-pane fade show active" id="admin-actions-tab-pane" role="tabpanel"
                            aria-labelledby="home-tab" tabindex="0">

                        </div>
                        {{-- <div class="tab-pane fade" id="admin-reports-tab-pane" role="tabpanel"
                            aria-labelledby="profile-tab" tabindex="0">
                            <div class="row">
                                @if (optional(auth()->user()->admin)->can == 'all' || optional(auth()->user()->admin)->can == 'kyc')
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>Total Users</h6>
                                                <h4>{{ $admin_rep['total_users'] }}</h4>
                                                <a href="{{ route('allUsers') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>Total Users [Blocked]</h6>
                                                <h4>{{ $admin_rep['total_blocked_users'] }}</h4>
                                                <a href="{{ route('allUsers') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>Total Walk In Customers</h6>
                                                <h4>{{ $admin_rep['total_customers'] }}</h4>
                                                <a href="{{ route('walk_in_customers.index') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>Total Walk In Customers [KYC Approved]</h6>
                                                <h4>{{ $admin_rep['total_approved_users'] }}</h4>
                                                <a href="{{ route('walk_in_customers.index') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (optional(auth()->user()->admin)->can == 'all' || optional(auth()->user()->admin)->can == 'accounts')
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>All Orders</h6>
                                                <h4>{{ $admin_rep['total_orders'] }}</h4>
                                                <a href="{{ route('allOrders') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>Unpaid Orders</h6>
                                                <h4>{{ $admin_rep['total_unpaid_orders'] }}</h4>
                                                <a href="{{ route('allOrders') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>Paid Orders</h6>
                                                <h4>{{ $admin_rep['totsl_paid_orders'] }}</h4>
                                                <a href="{{ route('allOrders') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>Orders in transit</h6>
                                                <h4>{{ $admin_rep['total_in_transit_orders'] }}</h4>
                                                <a href="{{ route('allOrders') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>Orders delivered</h6>
                                                <h4>{{ $admin_rep['total_delivered_orders'] }}</h4>
                                                <a href="{{ route('allOrders') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>Orders delivered</h6>
                                                <h4>{{ $admin_rep['total_picked_up_orders'] }}</h4>
                                                <a href="{{ route('allOrders') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>Orders canceled</h6>
                                                <h4>{{ $admin_rep['total_cancelled_orders'] }}</h4>
                                                <a href="{{ route('allOrders') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>All Payments</h6>
                                                <h4>{{ $admin_rep['total_payments'] }}</h4>
                                                <a href="{{ route('all.payments') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>All Pending Payments</h6>
                                                <h4>{{ $admin_rep['total_payments_pending'] }}</h4>
                                                <a href="{{ route('all.payments') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>All Failed Payments</h6>
                                                <h4>{{ $admin_rep['total_payments_failed'] }}</h4>
                                                <a href="{{ route('all.payments') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>Total Paid For shipping(&euro;)</h6>
                                                <h4>{{ number_format($admin_rep['total_payments_value'], 2) }}</h4>
                                                <a href="{{ route('all.payments') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>Total Paid EU Fund Transfers(&euro;)</h6>
                                                <h4>{{ number_format($admin_rep['total_eu_funds_value'], 2) }}</h4>
                                                <a href="{{ route('adminEUFundOrders') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>All EU Fund Transfers</h6>
                                                <h4>{{ $admin_rep['total_eu_funds'] }}</h4>
                                                <a href="{{ route('adminEUFundOrders') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>All Completed EU Fund Transfers</h6>
                                                <h4>{{ $admin_rep['total_eu_funds_done'] }}</h4>
                                                <a href="{{ route('adminEUFundOrders') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>All Rejected EU Fund Transfers</h6>
                                                <h4>{{ $admin_rep['total_eu_funds_rejected'] }}</h4>
                                                <a href="{{ route('adminEUFundOrders') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>Total Paid Intl. Fund Transfers(&euro;)</h6>
                                                <h4>{{ number_format($admin_rep['total_intl_funds_value'], 2) }}</h4>
                                                <a href="{{ route('adminIntlFundOrders') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>All Intl. Fund Transfers</h6>
                                                <h4>{{ $admin_rep['total_eu_funds'] }}</h4>
                                                <a href="{{ route('adminIntlFundOrders') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>All Completed Intl. Fund Transfers</h6>
                                                <h4>{{ $admin_rep['total_intl_funds_done'] }}</h4>
                                                <a href="{{ route('adminIntlFundOrders') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="card trans">
                                            <div class="card-body">
                                                <h6>All Rejected Intl. Fund Transfers</h6>
                                                <h4>{{ $admin_rep['total_intl_funds_rejected'] }}</h4>
                                                <a href="{{ route('adminIntlFundOrders') }}">See All</a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div> --}}
                    </div>
                @elseif (Auth::user()->user_type == 'subAdmin')
                <div class="row">
                    <div class="col-12">
                        <img src="{{ asset('landing2/img/timelab.jpg'); }}" alt="" style="width:100%;">
                    </div>
                </div>
                    @endif
            </div>
        </div>
        @if ($dispatcher_rep && Auth::user()->user_type !== 'admin')
            <ul class="d-flex dashboard_ul mb-5 gap-3">
                @if (Auth::user()->user_type === 'admin')
                    <li class="p-3 shadow rounded">
                        <p class="fw-bold text-nowrap">{{ __('admin.dashboard.total_user') }}</p>
                        <h2 class="text-center card_h2 text-nowrap">
                            <b>{{ $dispatcher_rep['total_users'] }}</b>
                        </h2>
                    </li>
                @endif
                <li class="p-3 shadow rounded">
                    <p class="fw-bold text-nowrap">{{ __('admin.dashboard.total_customer') }}</p>
                    <h2 class="text-center card_h2 text-nowrap">
                        <b>{{ $dispatcher_rep['customer'] }}</b>
                    </h2>
                </li>

                {{--  @dd(fromEuroView())  --}}
                <li class="p-3 shadow rounded">
                    <p class="fw-bold text-nowrap">{{ __('admin.dashboard.total_sales') }}</p>
                    <h2 class="text-center card_h2 text-nowrap">
                        <b>{{ fromEuroView(auth()->user()->currency_id ?? 0, $dispatcher_rep['total_sales']) }}</b>
                    </h2>
                </li>

                <li class="p-3 shadow rounded">
                    <p class="fw-bold text-nowrap">{{ __('admin.dashboard.total_account_balance') }}</p>
                    <h2 class="text-center card_h2 text-nowrap">
                        <b> {{ fromEuroView(auth()->user()->currency_id ?? 0, $dispatcher_rep['totalWalletAmout']) }} </b>
                    </h2>
                </li>
                <li class="p-3 shadow rounded">
                    <p class="fw-bold text-nowrap">{{ __('admin.dashboard.total_today_spending') }}</p>
                    <h2 class="text-center card_h2 text-nowrap">
                        <b>{{ fromEuroView(auth()->user()->currency_id ?? 0, $dispatcher_rep['total_today_spent']) }}</b>
                    </h2>
                </li>
                <li class="p-3 shadow rounded">
                    <p class="fw-bold text-nowrap">{{ __('admin.dashboard.total_spent_amount') }}</p>
                    <h2 class="text-center card_h2 text-nowrap">
                        <b> {{ fromEuroView(auth()->user()->currency_id ?? 0, $dispatcher_rep['toatalSpendAmount']) }} </b>
                    </h2>
                </li>
            </ul>
            <ul class="d-flex dashboard_ul mb-5 gap-3">
                <li class="p-3 shadow rounded">
                    <p class="fw-bold text-nowrap">{{ __('admin.dashboard.total_orders') }}</p>
                    <h2 class="text-center card_h2 text-nowrap">
                        <b>{{ $dispatcher_rep['total_orders'] }}</b>
                    </h2>
                </li>
                <li class="p-3 shadow rounded">
                    <p class="fw-bold text-nowrap">{{ __('admin.dashboard.total_placed_orders') }}</p>
                    <h2 class="text-center card_h2 text-nowrap">
                        <b>{{ $dispatcher_rep['total_placed_orders'] }}</b>
                    </h2>
                </li>
                <li class="p-3 shadow rounded">
                    <p class="fw-bold text-nowrap">{{ __('admin.dashboard.total_in_transit_orders') }}</p>
                    <h2 class="text-center card_h2 text-nowrap">
                        <b>{{ $dispatcher_rep['total_in_transit_orders'] }}</b>
                    </h2>
                </li>
                <li class="p-3 shadow rounded">
                    <p class="fw-bold text-nowrap">{{ __('admin.dashboard.total_unpaid_orders') }}</p>
                    <h2 class="text-center card_h2 text-nowrap">
                        <b>{{ $dispatcher_rep['total_unpaid_orders'] }}</b>
                    </h2>
                </li>
                <li class="p-3 shadow rounded">
                    <p class="fw-bold text-nowrap">{{ __('admin.dashboard.total_delivered_orders') }}</p>
                    <h2 class="text-center card_h2 text-nowrap">
                        <b>{{ $dispatcher_rep['total_delivered_orders'] }}</b>
                    </h2>
                </li>
                <li class="p-3 shadow rounded">
                    <p class="fw-bold text-nowrap">{{ __('admin.dashboard.total_canceled_orders') }}</p>
                    <h2 class="text-center card_h2 text-nowrap">
                        <b>{{ $dispatcher_rep['total_cancelled_orders'] }}</b>
                    </h2>
                </li>
            </ul>
            <div class="container-fluid">
                <div class="row mb-5 justify-content-between">
                    <div class="col-md-12  h-auto d-flex gap-3">
                        <div class="card  h-auto w-100 p-3 mb-4 shadow">
                            {{-- <h3>{{ __('admin.dashboard.customer.name') }}</h3>
                            <table class="table customer_tbl mb-5 table-bordered table-striped"
                                aria-describedby="orders_tbl_info">
                                <thead>
                                    <tr>
                                        <td>{{ __('admin.dashboard.customer.sl') }}</td>
                                        <td>{{ __('admin.dashboard.customer.fullName') }}</td>
                                        <td>{{ __('admin.dashboard.customer.email') }}</td>
                                        <td>{{ __('admin.dashboard.customer.tax_code') }}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @foreach ($dispatcher_rep['customers_list'] as $key => $customer)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $customer->surname }} {{ $customer->name }}</td>
                                            <td>{{ $customer->email }}</td>
                                            <td>{{ $customer->tax_code }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {!! $dispatcher_rep['customers_list']->links() !!} --}}

                            <style>
                                .contact_tbl td,
                                .order_tbl td,
                                .customer_tbl td {
                                    font-size: 11px;
                                    white-space: nowrap;    
                                }
                                .card{
                                    height: fit-content !important;
                                }
                                .contact_tbl td:first-child {
                                    /* width: 20px; */
                                }
                            </style>
                            @if (auth()->user()->user_type == 'admin')
                                <h3>{{ __('admin.dashboard.guest.name') }}</h3>
                                <div class="overflow-auto">
                                <table class="table contact_tbl table-responsive table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <td class="s_no">{{ __('admin.dashboard.guest.sl') }}</td>
                                            <td>{{ __('admin.dashboard.guest.fullName') }}</td>
                                            <td>{{ __('admin.dashboard.guest.email') }}</td>
                                            <td>{{ __('admin.dashboard.guest.phone') }}</td>
                                            {{-- <td>{{ __('admin.dashboard.guest.description') }}</td> --}}
                                            {{-- <td>{{ __('admin.dashboard.guest.location') }}</td> --}}
                                            <td>{{ __('admin.dashboard.guest.total_weight') }} </td>
                                            <td>{{ __('admin.dashboard.guest.shipping_cost') }} </td>
                                            {{-- <td>{{ __('hompage.shipping_day') }} </td> --}}
                                            <td>{{ __('admin.dashboard.guest.show') }}</td>
                                        </tr>
                                    </thead>
                                    <tbody id="contact_tbl">
                                        @foreach ($dispatcher_rep['contacts'] as $key => $contact)
                                            <tr>
                                                <td class="s_no"> {{ $key + 1 }} </td>
                                                <td> {{ $contact->first_name }} {{ $contact->last_name }}</td>
                                                <td> {{ $contact->email }} </td>
                                                <td> {{ $contact->phone }} </td>
                                                {{-- <td> {{ substr($contact->description, 0, 5) }} </td> --}}
                                                {{-- <td style="white-space: nowrap"> 
                                                    Ship from Country:
                                                    {{ $contact->ship_from_country?->name }} <br>
                                                    Ship from City: {{ $contact->ship_from_city?->name }} <br>
                                                    Ship from Zip Code: {{ $contact->zip_code }} <br>
                                                    Ship from Address: {{ $contact->address }} 
                                                    <hr>
                                                    Ship to Country: {{ $contact->ship_to_country?->name }}
                                                    <br>
                                                    Ship to City: {{ $contact->ship_to_city?->name }}
                                                    <br>
                                                    Ship to Zip Code: {{ $contact->zip_code1 }}
                                                    <br>
                                                    Ship to Address: {{ $contact->address1 }}
                                                </td> --}}
                                                <td> {{ $contact->total_weight }} </td>
                                                <td> {{ $contact->shipping_cost }} </td>
                                                {{-- <td> {{ $contact->day }} </td> --}}
                                                <td><a onclick="getContact(id)" data-bs-toggle="modal"
                                                        data-bs-target="#contactModal"
                                                        class="btn btn-primary show_btn btn-sm"
                                                        id="{{ $contact->id }}">{{ __('admin.dashboard.guest.show') }}</a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                                {!! $dispatcher_rep['contacts']->links() !!}
                            @endif

                        </div>

                        <div class="card w-50 h-auto shadow p-3 d-none">
                            <h3>{{ __('admin.dashboard.latestOrder.name') }}</h3>
                            <table class="table table-bordered table-responsive w-100 order_tbl table-striped"
                                aria-describedby="orders_tbl_info">
                                <thead>
                                    <tr>
                                        <td>{{ __('admin.dashboard.latestOrder.sl') }}</td>
                                        <td>{{ __('admin.dashboard.latestOrder.tracking_d') }}</td>
                                        <td>{{ __('admin.dashboard.latestOrder.status') }}</td>
                                        <td>{{ __('admin.dashboard.latestOrder.value_of_goods') }}</td>
                                        <td>{{ __('admin.dashboard.latestOrder.shipping_cost') }}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dispatcher_rep['latest_orders'] as $key => $order)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $order->tracking_id }}</td>
                                            <td>{{ $order->status }}</td>
                                            <td>{{ fromEuroView(auth()->user()->currency_id ?? 0, $order->val_of_goods, 2) }}
                                            </td>
                                            <td>{{ fromEuroView(auth()->user()->currency_id ?? 0, $order->shipping_cost, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {!! $dispatcher_rep['latest_orders']->links() !!}
                        </div>
                    </div>
                </div>

        @endif
    </div>
    </div>
    {{-- modal --}}
    <div class="modal modal-lg fade" id="contactModal">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-dialog-lg">
            <div class="modal-content rounded">
                <div class="modal-header">
                    <h4 class="modal-title">Contact Us</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
        {{-- modal --}}
    @endsection
    @section('scripts')
        <script src="{{ asset('admin_assets/assets/libs/jquery/dist/jquery.min.js') }}"></script>
        <script>
            // getContact();

            function getContact(id) {
                var id = id;
                console.log($('.show_btn').attr('id'));
                let url = "{{ route('contact.show', ['id' => ':id']) }}"
                    .replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'get',
                    dataType: 'json',
                    data: id,
                    success: function(response) {
                        console.log(response);
                        var tr = '';
                        tr += '<div class="mb-3 w-100 d-flex justify-content-between">';
                        tr += '<div class="mb-3 w-50">';
                        tr += '<p class="mb-2">First Name: <span class="ps-2 f_name">' + response.contact
                            .first_name + '</span></p>';
                        tr += '<p class="mb-2">Last Name: <span class="ps-2 l_name">' + response.contact
                            .last_name + '</span></p>';
                        tr += '<p class="mb-2">Email: <span class="ps-2 email">' + response.contact.email +
                            '</span></p>';
                        tr += '<p class="mb-2">Phone: <span class="ps-2 phone">' + response.contact.phone +
                            '</span></p>';
                        tr += '<p>Description: <span class="ps-2 desc">' + response.contact.description +
                            '</span></p>';
                        tr += '</div>';
                        tr += '<div class="mb-3 w-50">';
                        tr += '<p>Total Weight: <span class="ps-2 desc">' + response.contact.total_weight +
                            '</span></p>';
                        tr += '<p>Shipping Cost: <span class="ps-2 desc">' + response.contact.shipping_cost +
                            '</span></p>';
                        tr += '<p>Approximate Delivery Day: <span class="ps-2 desc">' + response.contact.day +
                            '</span></p> <hr>';

                        tr += '<p>Ship from Country: <span class="ps-2 desc">' + response.ship_from_country +
                            '</span></p>';
                        tr += '<p>Ship from City: <span class="ps-2 desc">' + response.ship_from_city +
                            '</span></p>';
                        tr += '<p>Ship from State Name: <span class="ps-2 desc">' + response.contact.ship_from_State_name +
                            '</span></p> ';
                        tr += '<p>Ship from Zip Code: <span class="ps-2 desc">' + response.contact.zip_code +
                            '</span></p> ';
                        tr += '<p>Ship from Address: <span class="ps-2 desc">' + response.contact.address +
                            '</span></p> <hr>';
                        tr += '<p>Ship to Country: <span class="ps-2 desc">' + response.ship_to_country +
                            '</span></p>';
                        tr += '<p>Ship to City: <span class="ps-2 desc">' + response.ship_to_city +
                            '</span></p>';
                            tr += '<p>Ship to State Name: <span class="ps-2 desc">' + response.contact.ship_to_State_name +
                                '</span></p>';
                            tr += '<p>Ship to Zip Code: <span class="ps-2 desc">' + response.contact.zip_code1 +
                                '</span></p>';
                            tr += '<p>Ship to Address: <span class="ps-2 desc">' + response.contact.address1 +
                                '</span></p>';
                        tr += '</div>';
                        tr += '</div>';
                        tr += '<hr>';
                        $(".modal-body").empty();
                        $(".modal-body").append(tr);
                    }

                });

            }
        </script>
