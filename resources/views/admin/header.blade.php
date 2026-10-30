<header class="app-header d-flex justify-content-end">
    <nav class="navbar navbar-expand w-20  navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
                <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop3" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="ti ti-bell-ringing"></i>
                    <div class="notification bg-primary rounded-circle d-flex justify-content-center p-1 text-white align-items-center"
                       > <span style="font-size: 10px" class="position-relative"  id="notificationCounter">1</span></div>
                </a>
                <style>
                    .message-body {
                        font-size: 10px;
                        min-width: 300px;
                    }

                    .notification {
                        content: "";
                        position: absolute;
                        top: 11px;
                        right: 0px;
                        width: 20px;
                        height: 20px;
                    }
                </style>
                <div class="dropdown-menu p-3 dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop3"
                    id="notificationDropDown">
                    <div class="message-body d-flex align-items-center gap-3 mb-2">
                        <div>
                            <img style="width:30px; height:30px; border-radius:50%"
                                src="{{ asset('admin_assets/assets/images/profile/user-1.jpg') }}" alt="">
                        </div>
                        <div>
                            <b class="fw-bold">Alex Carry</b>
                            <p class="fw-bold mb-0">Welcome To Sioshipping</p>
                            <a class="" href="">Url</a>
                        </div>
                    </div>
                    <hr class="m-1">
                    <div class="text-center">
                        <a class="btn-sm" href="">{{ __('admin.menus.16') }}</a>
                    </div>
                </div>
            </li>
        </ul>
        <div class="navbar-collapse px-0" id="navbarNavLang">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                <li class="nav-item dropdown">
                    <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop3"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        @if ((session('locale') && session('locale') == 'en') || app()->getLocale() == 'en')
                        English
                    @elseif ((session('locale') && session('locale') == 'ar') || app()->getLocale() == 'ar')
                        arabic
                    @elseif ((session('locale') && session('locale') == 'it') || app()->getLocale() == 'it')
                        Italiano
                    @elseif ((session('locale') && session('locale') == 'ch') || app()->getLocale() == 'ch')
                    Cinese
                    @elseif ((session('locale') && session('locale') == 'fr') || app()->getLocale() == 'fr')
                    France
                    @elseif ((session('locale') && session('locale') == 'dt') || app()->getLocale() == 'dt')
                    Dauch
                    @elseif ((session('locale') && session('locale') == 'pt') || app()->getLocale() == 'pt')
                    Portugal
                    @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop3">
                        <div class="message-body">
                                <a href="{{ route('set-lang', 'en') }}"
                                    class="d-flex align-items-center gap-2 dropdown-item">
                                    @if ((session('locale') && session('locale') == 'en') || app()->getLocale() == 'en')
                                    <i class="fa fa-check text-success"></i>
                                    @endif
                                    <p class="mb-0 fs-3">English</p>
                                </a>
                                <a href="{{ route('set-lang', 'it') }}"
                                    class="d-flex align-items-center gap-2 dropdown-item">
                                    @if ((session('locale') && session('locale') == 'it') || app()->getLocale() == 'it')
                                    <i class="fa fa-check text-success"></i>
                                    @endif
                                    <p class="mb-0 fs-3">Italiano</p>
                                </a>
                                <a href="{{ route('set-lang', 'ar') }}"
                                    class="d-flex align-items-center gap-2 dropdown-item">
                                    @if ((session('locale') && session('locale') == 'ar') || app()->getLocale() == 'ar')
                                    <i class="fa fa-check text-success"></i>
                                    @endif
                                    <p class="mb-0 fs-3">Arabic</p>
                                </a>
                                <a href="{{ route('set-lang', 'ch') }}"
                                    class="d-flex align-items-center gap-2 dropdown-item">
                                    @if ((session('locale') && session('locale') == 'ch') || app()->getLocale() == 'ch')
                                    <i class="fa fa-check text-success"></i>
                                    @endif
                                    <p class="mb-0 fs-3">Cinese</p>
                                </a>
                                <a href="{{ route('set-lang', 'fr') }}"
                                    class="d-flex align-items-center gap-2 dropdown-item">
                                    @if ((session('locale') && session('locale') == 'fr') || app()->getLocale() == 'fr')
                                    <i class="fa fa-check text-success"></i>
                                    @endif
                                    <p class="mb-0 fs-3">France</p>
                                </a>
                                <a href="{{ route('set-lang', 'dt') }}"
                                    class="d-flex align-items-center gap-2 dropdown-item">
                                    @if ((session('locale') && session('locale') == 'dt') || app()->getLocale() == 'dt')
                                    <i class="fa fa-check text-success"></i>
                                    @endif
                                    <p class="mb-0 fs-3">Dauch</p>
                                </a>
                                <a href="{{ route('set-lang', 'pt') }}"
                                    class="d-flex align-items-center gap-2 dropdown-item">
                                    @if ((session('locale') && session('locale') == 'pt') || app()->getLocale() == 'pt')
                                    <i class="fa fa-check text-success"></i>
                                    @endif
                                    <p class="mb-0 fs-3">Portugal</p>
                                </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                <li class="nav-item dropdown">
                    <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset('admin_assets/assets/images/profile/user-1.jpg') }}" alt=""
                            width="35" height="35" class="rounded-circle">
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                        <div class="message-body">
                            @if (Auth::user()->user_type == 'admin')
                                <a href="{{ route('admin.settings') }}"
                                    class="d-flex align-items-center gap-2 dropdown-item">
                                    <i class="ti ti-user fs-6"></i>
                                    <p class="mb-0 fs-3">{{ __('admin.menus.14') }}</p>
                                </a>
                            @endif
                            @if (Auth::user()->user_type == 'agent')
                                <a href="{{ route('agent.profile') }}"
                                    class="d-flex align-items-center gap-2 dropdown-item">
                                    <i class="ti ti-user fs-6"></i>
                                    <p class="mb-0 fs-3">{{ __('admin.menus.14') }}</p>
                                </a>
                            @endif
                            {{-- <a href="{{ route('my.payments') }}" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-mail fs-6"></i>
                                <p class="mb-0 fs-3">My Payments</p>
                            </a> --}}
                            {{-- <a href="{{ route('orders.index') }}"
                                class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-list-check fs-6"></i>
                                <p class="mb-0 fs-3">My Orders</p>
                            </a> --}}
                            <a class="btn btn-outline-primary mx-3 mt-2 d-block" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                                {{ __('admin.menus.15') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                            {{-- <a href="./authentication-login.html"
                                class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a> --}}
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>
