<?php
use Illuminate\Support\Facades\DB;
?>

<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ route('home') }}" class="text-nowrap logo-img">
                <img src="{{ asset('landing/assets/img/gallery/sioshipping_logo.png') }}" style="width:100%;" alt="logo" />
            </a>
            <hr>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
            </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">

                <li class="sidebar-item">
                    <a class="sidebar-link @if (Route::is('home*')) active @endif" href="{{ route('home') }}"
                        aria-expanded="false">
                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>
                        <span class="hide-menu">{{ __('admin.menus.1') }}</span>
                    </a>
                </li>

                @if (Auth::user()->user_type == 'dispatcher')

                <li class="sidebar-item">
                    <a class="sidebar-link @if (Route::is('allOrdersDeliveryMan')) active @endif" href="{{ route('allOrdersDeliveryMan') }}"
                        aria-expanded="false">
                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>
                        <span class="hide-menu"> {{ __('admin.menus.newOrders') }} </span>
                    </a>
                </li>                
                <li class="sidebar-item">
                    <a class="sidebar-link @if (Route::is('allOrdersDelivered')) active @endif" href="{{ route('allOrdersDelivered') }}"
                        aria-expanded="false">
                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>
                        <span class="hide-menu"> Delivered Orders </span>
                    </a>
                </li>                
                <li class="sidebar-item">
                    <a class="sidebar-link @if (Route::is('allOrdersCancel')) active @endif" href="{{ route('allOrdersCancel') }}"
                        aria-expanded="false">
                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>
                        <span class="hide-menu"> Canceled Orders </span>
                    </a>
                </li>                
                @endif

                {{-- @if (Auth::user()->user_type == 'agent')
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <h2 class="hide-menu">POS: {{ str_pad(Auth::id(), 4, '0', STR_PAD_LEFT) }}</h2>
                    </li>
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <h6 class="hide-menu">Today's Commision (&euro;) :
                            {{ getAccountbalances(Auth::id())['earningsToday'] }}</h6>
                        <h6 class="hide-menu">Today's Spendings (&euro;) :
                            {{ getAccountbalances(Auth::id())['spentToday'] }}</h6>
                        <h6 class="hide-menu">Account Balance (&euro;) : {{ getAccountbalances(Auth::id())['balance'] }}
                        </h6>
                    </li>
                @endif --}}


                @if (Auth::user()->user_type == 'agent')
                    <li class="sidebar-item">
                        <a class="sidebar-link  @if (Route::is('my-wallet.*')) active @endif"
                            href="{{ route('my-wallet.index') }}" aria-expanded="false">
                            <span>
                                <i class="fa-solid fa-gears"></i>
                            </span>
                            <span class="hide-menu">{{ __('admin.menus.17') }}</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link @if (Route::is('agentsOrders') || Route::is('walkInOrderAgents.*') || Route::is('orders.*')) active @endif"
                            href="{{ route('agentsOrders') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-article"></i>
                            </span>
                            <span class="hide-menu">{{ __('admin.menus.newOrders') }}</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link @if (Route::is('agentDeliveredOrder')) active @endif"
                            href="{{ route('agentDeliveredOrder') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-article"></i>
                            </span>
                            <span class="hide-menu">{{ __('admin.menus.agentDeliveredOrder') }}</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link @if (Route::is('agentDeliveryManOrder')) active @endif"
                            href="{{ route('agentDeliveryManOrder') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-article"></i>
                            </span>
                            <span class="hide-menu">{{ __('admin.menus.agentDeliveryManOrder') }}</span>
                        </a>
                    </li>
                    {{-- <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route(') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-article"></i>
                            </span>
                            <span class="hide-menu">New Shipping Order</span>
                        </a>
                    </li> --}}
                    <li class="sidebar-item">
                        <a class="sidebar-link @if (Route::is('agent.accept') || Route::is('agent.accept.search')) active @endif"
                            href="{{ route('agent.accept') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-article"></i>
                            </span>
                            <span class="hide-menu">{{ __('admin.menus.18') }}</span>
                        </a>
                    </li>
                @endif


                @if (Auth::user()->user_type == 'admin')
                    {{-- @if (Auth::user()->user_type == 'admin' && (optional(auth()->user()->admin)->can == 'all' || optional(auth()->user()->admin)->can == 'kyc')) --}}
                    <li class="sidebar-item">
                        <a class="sidebar-link @if (Route::is('allUsers') ||
                                Route::is('create.dispatcher') ||
                                Route::is('dispatchers.*') ||
                                Route::is('admin-paymentRequestget') ||
                                Route::is('agent.editSitting.*')) active @endif"
                            href="{{ route('allUsers') }}" aria-expanded="false">
                            <span>
                                <i class="fa-solid fa-users"></i>
                            </span>
                            <span class="hide-menu">{{ __('admin.menus.2') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link  @if (Route::is('subAdmin.*')) active @endif"
                            href="{{ route('subAdmin.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-article"></i>
                            </span>
                            <span class="hide-menu">{{ __('admin.menus.3') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link @if (Route::is('cities.shipping.rates') || Route::is('shipping_rates.index') || Route::is('weight.shipping.rates')) active @endif"
                            href="{{ route('shipping_rates.index') }}" aria-expanded="false">
                            <span>
                                <i class="fa-solid fa-ferry"></i>
                            </span>
                            <span class="hide-menu">{{ __('admin.menus.4') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link @if (Route::is('currency-view')) active @endif"
                            href="{{ route('currency-view') }}" aria-expanded="false">
                            <span>
                                <i class="fa-solid fa-coins"></i>
                            </span>
                            <span class="hide-menu">{{ __('admin.menus.5') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link @if (Route::is('bank_details.*')) active @endif"
                            href="{{ route('bank_details.index') }}" aria-expanded="false">
                            <span>
                                <i class="fa-solid fa-coins"></i>
                            </span>
                            <span class="hide-menu">{{ __('admin.menus.6') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link @if (Route::is('admin.delivery-payouts.web.*')) active @endif"
                            href="{{ route('admin.delivery-payouts.web.index') }}" aria-expanded="false">
                            <span>
                                <i class="fa-solid fa-money-bill-transfer"></i>
                            </span>
                            <span class="hide-menu">Delivery Payouts</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link  @if (Route::is('blogEntry.*')) active @endif"
                            href="{{ route('blogEntry.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-article"></i>
                            </span>
                            <span class="hide-menu">{{ __('admin.menus.8') }}</span>
                        </a>
                    </li>
                    {{-- <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('walk_in_customers.index') }}"
                                aria-expanded="false">
                                <span>
                                    <i class="fa-solid fa-users"></i>
                                </span>
                                <span class="hide-menu">Manage KYC</span>
                            </a>
                        </li> --}}
                    {{-- @endif --}}
                    {{-- @if (Auth::user()->user_type == 'admin' && (optional(auth()->user()->admin)->can == 'all' || optional(auth()->user()->admin)->can == 'accounts')) --}}
                    <li class="sidebar-item">
                        <a class="sidebar-link @if (Route::is('allOrders') || Route::is('orders.*')) active @endif"
                            href="{{ route('allOrders') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-article"></i>
                            </span>
                            <span class="hide-menu">{{ __('admin.menus.9') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link @if (Route::is('admin.deliveries.web.*')) active @endif"
                            href="{{ route('admin.deliveries.web.index') }}" aria-expanded="false">
                            <span>
                                <i class="fa-solid fa-route"></i>
                            </span>
                            <span class="hide-menu">Delivery Tracking</span>
                        </a>
                    </li>
                    {{-- @endif --}}

                    {{-- @if (Auth::user()->user_type == 'admin' && optional(auth()->user()->admin)->can == 'all') --}}
                    <li class="sidebar-item">
                        <a class="sidebar-link  @if (Route::is('products.*')) active @endif"
                            href="{{ route('products.index') }}" aria-expanded="false">
                            <span>
                                <i class="fa-solid fa-check"></i>
                            </span>
                            <span class="hide-menu">{{ __('admin.menus.10') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link  @if (Route::is('vehicles.*')) active @endif"
                            href="{{ route('vehicles.index') }}" aria-expanded="false">
                            <span>
                                <i class="fa-solid fa-truck"></i>
                            </span>
                            <span class="hide-menu">Vehicles</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link  @if (Route::is('coupons.*')) active @endif"
                            href="{{ route('coupons.index') }}" aria-expanded="false">
                            <span>
                                <i class="fa-solid fa-ticket"></i>
                            </span>
                            <span class="hide-menu">Coupons</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link  @if (Route::is('faqs.*') || Route::is('faq-categories.*')) active @endif"
                            href="{{ route('faqs.index') }}" aria-expanded="false">
                            <span>
                                <i class="fa-solid fa-circle-question"></i>
                            </span>
                            <span class="hide-menu">Help Center</span>
                        </a>
                    </li>
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <h2 class="hide-menu">Settings</h2>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link @if (Route::is('smtp-configurations.*')) active @endif"
                            href="{{ route('smtp-configurations.index') }}" aria-expanded="false">
                            <span>
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <span class="hide-menu">SMTP Configurations</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link @if (Route::is('monnify-configurations.*')) active @endif"
                            href="{{ route('monnify-configurations.index') }}" aria-expanded="false">
                            <span>
                                <i class="fa-solid fa-credit-card"></i>
                            </span>
                            <span class="hide-menu">Monnify Configurations</span>
                        </a>
                    </li>
                    {{-- <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('admin.settings') }}" aria-expanded="false">
                                <span>
                                    <i class="fa-solid fa-gears"></i>
                                </span>
                                <span class="hide-menu">Settings</span>
                            </a>
                        </li> --}}
                    {{-- @endif --}}
                @endif


                @if (Auth::user()->user_type == 'subAdmin')
                    {{-- @if (Auth::user()->user_type == 'admin' && (optional(auth()->user()->admin)->can == 'all' || optional(auth()->user()->admin)->can == 'kyc')) --}}
                    
                    @php


    $userId = Auth()->user()->id; // Replace with the actual user ID

    $menus = DB::table('menu_lists')
        ->leftJoin('menu_permission', function ($join) use ($userId) {
            $join->on('menu_lists.id', '=', 'menu_permission.menu_id')
                ->where('menu_permission.user_id', '=', $userId);
        })
        ->select(
            'menu_lists.id',
            'menu_lists.name',
            'menu_lists.url',
            'menu_lists.icon',
            DB::raw('IF(menu_permission.menu_id IS NULL, 0, 1) as status')
        )
        ->get();
        // dd($menus);
        
@endphp

                @foreach ($menus as $menu) 
                    @if($menu->status==1)
                        <li class="sidebar-item">
                            <a class="sidebar-link @if (Request::is($menu->url)) active @endif"
                                href="{{ url($menu->url) }}" aria-expanded="false">
                                <span> 
                                    <i class="fa-solid fa-{{ $menu->icon }}"></i>
                                </span>
                                <span class="hide-menu"> {{ __('admin.menus.'.$menu->name) }}</span> 
                            </a>
                        </li>
                    @endif
                @endforeach

                @endif
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
