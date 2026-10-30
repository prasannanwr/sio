<!DOCTYPE html>
<html lang="en-US" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>{{ env('APP_NAME') }} | @yield('page_title')</title>


    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#218bff">


    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link href="{{ asset('landing/assets/css/theme.css') }}" rel="stylesheet" />

</head>


<body>

    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
        <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3 d-block">
            <div class="container"><a class="navbar-brand" href="/"><img
                        src="{{ asset('landing/assets/img/gallery/siopay_logo.png') }}" height="45"
                        alt="logo" /></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon">
                    </span></button>
                <div class="collapse navbar-collapse border-top border-lg-0 mt-4 mt-lg-0" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto pt-2 pt-lg-0 font-base">
                        <li class="nav-item px-2"><a class="nav-link" aria-current="page" href="#">Home</a>
                        </li>
                        <li class="nav-item px-2"><a class="nav-link" aria-current="page" href="{{route('who_we_are')}}">About Us</a>
                        </li>
                        
                        {{-- <li class="nav-item px-2"><a class="nav-link" href="#services">Our Services</a></li> --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Our Services
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                                <li><a class="dropdown-item" href="{{ route('shipping_page') }}">Shipping Services</a></li>
                                <li><a class="dropdown-item" href="{{ route('payment_page') }}">Payment services</a></li>
                                <li><a class="dropdown-item" href="{{ route('tup_up_page') }}">Pre-paid Card top-Up</a></li>
                                <li><a class="dropdown-item" href="{{ route('pick_up_point_page') }}">Pick-Up points</a></li>
                                <li><a class="dropdown-item" href="{{ route('spdi_page') }}">SPDI and Digital Signatures</a></li>
                                <li><a class="dropdown-item" href="{{ route('pec_page') }}">Certified Electronic Mail
                                        service(PEC)</a></li>
                                <li><a class="dropdown-item" href="{{ route('roadside_page') }}">Roadside Assistance Card</a>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('ticket_resale_page') }}">Ticket Resale</a></li>
                                <li><a class="dropdown-item" href="{{ route('gas_page') }}">Electricity and Gas</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="howtoDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                How To ship
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="howtoDropdown">
                                <li><a class="dropdown-item" href="{{ route('order_guide') }}">How To order</a></li>
                                <li><a class="dropdown-item" href="{{ route('how_to_pack') }}">How To Pack</a></li>
                                <li><a class="dropdown-item" href="{{ route('how_to_measure') }}">How to measeure</a></li>
                                <li><a class="dropdown-item" href="{{ route('prohibited_goods') }}">Prohibited Goods</a></li>
                            </ul>
                        </li>
                         
                        <li class="nav-item px-2"><a class="nav-link" href="#findUs">Find Us</a></li>
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item px-2"><a class="nav-link"
                                        href="{{ route('login') }}">{{ __('Login') }}</a></li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn btn-primary" href="{{ route('home') }}">Dashboard</a>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <section class="py-xxl-10 pb-0" id="home">
            <div class="bg-holder bg-size"
                style="background-image:url({{ asset('landing/assets/img/gallery/hero-header-bg.png') }});background-position:top center;background-size:cover;">
            </div>
            <!--/.bg-holder-->

            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-5 col-xl-6 col-xxl-7 order-0 order-md-1 text-end"><img class="pt-7 pt-md-0 w-100"
                            src="{{ asset('landing/assets/img/illustrations/hero.pn') }}g" alt="hero-header" /></div>
                    <div class="col-md-75 col-xl-6 col-xxl-5 text-md-start text-center py-8">
                        <h1 class="fw-normal fs-6 fs-xxl-7">A trusted provider of </h1>
                        <h1 class="fw-bolder fs-6 fs-xxl-7 mb-2">courier services.</h1>
                        <p class="fs-1 mb-5">We deliver your products safely to <br />your desired destination in a reasonable time.
                        </p><a class="btn btn-primary me-2" href="{{ route('home') }}" role="button">{{__('hompage.74')}}<i
                                class="fas fa-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
        </section>


        <!-- ============================================-->
        <!-- <section> begin ============================-->
        <section class="py-7" id="services" container-xl="container-xl">

            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-5 text-center mb-3">
                        <h5 class="text-danger">SERVICES</h5>
                        <h2>Our services for you</h2>
                    </div>
                </div>
                <div class="row h-100 justify-content-center">
                    <div class="col-md-4 pt-4 px-md-2 px-lg-3">
                        <div class="card h-100 px-lg-5 card-span">
                            <div class="card-body d-flex flex-column justify-content-around">
                                <div class="text-center pt-5"><img class="img-fluid"
                                        src="{{ asset('landing/assets/img/icons/services-1.svg') }}"
                                        alt="..." />
                                    <h5 class="my-4">Shipping</h5>
                                </div>
                                <p>We Offer the most reliable shipping at the very best pices.</p>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Corporate goods
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Shipment
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Accesories
                                    </li>
                                </ul>
                                <div class="text-center my-5">
                                    <div class="d-grid">
                                        <a class="btn btn-outline-danger" href="{{route('shipping_page')}}">Learn more </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 pt-4 px-md-2 px-lg-3">
                        <div class="card h-100 px-lg-5 card-span">
                            <div class="card-body d-flex flex-column justify-content-around">
                                <div class="text-center pt-5"><img class="img-fluid"
                                        src="{{ asset('landing/assets/img/icons/services-2.svg') }}"
                                        alt="..." />
                                    <h5 class="my-4">Payment Services</h5>
                                </div>
                                <p>Our State of the art payment server can cater for all you payment needs.</p>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Bill Paymnets
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Funds transfer
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Secure Process
                                    </li>
                                </ul>
                                <div class="text-center my-5">
                                    <div class="d-grid">
                                        <a class="btn btn-danger hover-top btn-glow border-0"
                                            href="{{route('payment_page')}}">Learn more</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 pt-4 px-md-2 px-lg-3">
                        <div class="card h-100 px-lg-5 card-span">
                            <div class="card-body d-flex flex-column justify-content-around">
                                <div class="text-center pt-5"><img class="img-fluid"
                                        src="{{ asset('landing/assets/img/icons/services-3.svg') }}"
                                        alt="..." />
                                    <h5 class="my-4">Pre-paid Card top-Up</h5>
                                </div>
                                <p>You can trust us to safely fund your Prepaid cards.</p>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Easy Process
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Quick delivery
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Secure process
                                    </li>
                                </ul>
                                <div class="text-center my-5">
                                    <div class="d-grid">
                                        <a class="btn btn-outline-danger" href="{{route('tup_up_page')}}">Learn more </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row h-100 justify-content-center">
                    <div class="col-md-4 pt-4 px-md-2 px-lg-3">
                        <div class="card h-100 px-lg-5 card-span">
                            <div class="card-body d-flex flex-column justify-content-around">
                                <div class="text-center pt-5"><img class="img-fluid"
                                        src="{{ asset('landing/assets/img/icons/services-1.svg') }}"
                                        alt="..." />
                                    <h5 class="my-4">Pick-Up points</h5>
                                </div>
                                <p>We Offer Businerss owners the chance of becoming agents.</p>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Earn Commisions
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Gain more publicity
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Convienient registeration Process
                                    </li>
                                </ul>
                                <div class="text-center my-5">
                                    <div class="d-grid">
                                        <a class="btn btn-outline-danger" href="{{route('pick_up_point_page')}}">Learn more </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 pt-4 px-md-2 px-lg-3">
                        <div class="card h-100 px-lg-5 card-span">
                            <div class="card-body d-flex flex-column justify-content-around">
                                <div class="text-center pt-5"><img class="img-fluid"
                                        src="{{ asset('landing/assets/img/icons/services-2.svg') }}"
                                        alt="..." />
                                    <h5 class="my-4">SPDI and Digital Signatures</h5>
                                </div>
                                <p>We offer a system for generating and managing digital signatures.</p>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Quick
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Efficient
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Secure Process
                                    </li>
                                </ul>
                                <div class="text-center my-5">
                                    <div class="d-grid">
                                        <a class="btn btn-danger hover-top btn-glow border-0"
                                            href="{{route('spdi_page')}}">Learn more</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 pt-4 px-md-2 px-lg-3">
                        <div class="card h-100 px-lg-5 card-span">
                            <div class="card-body d-flex flex-column justify-content-around">
                                <div class="text-center pt-5"><img class="img-fluid"
                                        src="{{ asset('landing/assets/img/icons/services-3.svg') }}"
                                        alt="..." />
                                    <h5 class="my-4">Certified Electronic Mail service(PEC)</h5>
                                </div>
                                <p>We offer Certified Electronic Mail service As a service.</p>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Easy Process
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Secure delivery of your correspondece
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Secure process
                                    </li>
                                </ul>
                                <div class="text-center my-5">
                                    <div class="d-grid">
                                        <a class="btn btn-outline-danger" href="{{route('pec_page')}}">Learn more </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row h-100 justify-content-center">
                    <div class="col-md-4 pt-4 px-md-2 px-lg-3">
                        <div class="card h-100 px-lg-5 card-span">
                            <div class="card-body d-flex flex-column justify-content-around">
                                <div class="text-center pt-5"><img class="img-fluid"
                                        src="{{ asset('landing/assets/img/icons/services-1.svg') }}"
                                        alt="..." />
                                    <h5 class="my-4">Roadside Assistance Card</h5>
                                </div>
                                <p>We Offer Professional and timely roadside assistance.</p>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Timely delivery
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Available 24 hours a day, 7 days a week
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Quick expert help
                                    </li>
                                </ul>
                                <div class="text-center my-5">
                                    <div class="d-grid">
                                        <a class="btn btn-outline-danger" href="{{route('roadside_page')}}">Learn more </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 pt-4 px-md-2 px-lg-3">
                        <div class="card h-100 px-lg-5 card-span">
                            <div class="card-body d-flex flex-column justify-content-around">
                                <div class="text-center pt-5"><img class="img-fluid"
                                        src="{{ asset('landing/assets/img/icons/services-2.svg') }}"
                                        alt="..." />
                                    <h5 class="my-4">Ticket Resale</h5>
                                </div>
                                <p>Our mission is to provide an efficient and reliable service, allowing you to purchase tickets for your train journey quickly and easily.</p>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Best prices
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Timely response
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Secure Process
                                    </li>
                                </ul>
                                <div class="text-center my-5">
                                    <div class="d-grid">
                                        <a class="btn btn-danger hover-top btn-glow border-0"
                                            href="{{route('ticket_resale_page')}}">Learn more</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 pt-4 px-md-2 px-lg-3">
                        <div class="card h-100 px-lg-5 card-span">
                            <div class="card-body d-flex flex-column justify-content-around">
                                <div class="text-center pt-5"><img class="img-fluid"
                                        src="{{ asset('landing/assets/img/icons/services-3.svg') }}"
                                        alt="..." />
                                    <h5 class="my-4">Electricity and Gas</h5>
                                </div>
                                <p>We are here to offer you a flawless and hassle-free energy experience.</p>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Easy Process
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Quick delivery
                                    </li>
                                    <li class="mb-2"><span class="me-2"><i class="fas fa-circle text-primary"
                                                style="font-size:.5rem"></i></span>Unmatched reliability
                                    </li>
                                </ul>
                                <div class="text-center my-5">
                                    <div class="d-grid">
                                        <a class="btn btn-outline-danger" href="{{route('gas_page')}}">Learn more </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end of .container-->

        </section>
        <!-- <section> close ============================-->
        <!-- ============================================-->




        <!-- ============================================-->
        <!-- <section> begin ============================-->
        <section class="pt-7 pb-0">

            <div class="container">
                <div class="row">
                    <div class="col-6 col-lg mb-5">
                        <div class="text-center"><img src="{{ asset('landing/assets/img/icons/awards.png') }}"
                                alt="..." />
                            <h1 class="text-primary mt-4">26+</h1>
                            <h5 class="text-800">Awards won</h5>
                        </div>
                    </div>
                    <div class="col-6 col-lg mb-5">
                        <div class="text-center"><img src="{{ asset('landing/assets/img/icons/states.png') }}"
                                alt="..." />
                            <h1 class="text-primary mt-4">65+</h1>
                            <h5 class="text-800">States covered</h5>
                        </div>
                    </div>
                    <div class="col-6 col-lg mb-5">
                        <div class="text-center"><img src="{{ asset('landing/assets/img/icons/clients.png') }}"
                                alt="..." />
                            <h1 class="text-primary mt-4">689K+</h1>
                            <h5 class="text-800">Happy clients</h5>
                        </div>
                    </div>
                    <div class="col-6 col-lg mb-5">
                        <div class="text-center"><img src="{{ asset('landing/assets/img/icons/goods.png') }}"
                                alt="..." />
                            <h1 class="text-primary mt-4">130M+</h1>
                            <h5 class="text-800">Goods delivered</h5>
                        </div>
                    </div>
                    <div class="col-6 col-lg mb-5">
                        <div class="text-center"><img src="{{ asset('landing/assets/img/icons/business.png') }}"
                                alt="..." />
                            <h1 class="text-primary mt-4">130M+</h1>
                            <h5 class="text-800">Business hours</h5>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end of .container-->

        </section>
        <!-- <section> close ============================-->
        <!-- ============================================-->




        <!-- ============================================-->
        <!-- <section> begin ============================-->
        <section>

            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-dark text-white py-4 py-sm-0"><img class="w-100"
                                src="{{ asset('landing/assets/img/gallery/video.png') }}" alt="video" />
                            <div class="card-img-overlay bg-dark-gradient d-flex flex-column flex-center"><img
                                    src="{{ asset('landing/assets/img/icons/play.png') }}" width="80"
                                    alt="play" />
                                <h5 class="text-primary">FASTEST DELIVERY</h5>
                                <p class="text-center">You can get your valuable item in the fastest period of<br
                                        class="d-none d-sm-block" />time with safety. Because your emergency<br
                                        class="d-none d-sm-block" />is our first priority.</p><a
                                    class="stretched-link" href="#" data-bs-toggle="modal"
                                    data-bs-target="#exampleModal"></a>
                                <div class="modal fade" id="exampleModal" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content overflow-hidden">
                                            <div class="modal-header p-0">
                                                <div class="ratio ratio-16x9" id="exampleModalLabel">
                                                    <iframe src="https://www.youtube.com/embed/TlcP2aTOp-Q"
                                                        title="YouTube video"
                                                        allowfullscreen="allowfullscreen"></iframe>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-primary" type="button"
                                                    data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end of .container-->

        </section>
        <!-- <section> close ============================-->
        <!-- ============================================-->




        <!-- ============================================-->
        <!-- <section> begin ============================-->
        <section class="py-7">

            <div class="container-fluid">
                <div class="row flex-center">
                    <div class="bg-holder bg-size"
                        style="background-image:url({{ asset('landing/assets/img/gallery/quote.png') }});background-position:top;background-size:auto;margin-left:-270px;margin-top:-45px;">
                    </div>
                    <!--/.bg-holder-->

                    <div class="col-md-8 col-lg-5 text-center">
                        <h5 class="text-danger">TESTIMONIAL</h5>
                        <h2>Our Awesome Clients</h2>
                    </div>
                </div>
                <div class="carousel slide pt-6" id="carouselExampleDark" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active" data-bs-interval="10000">
                            <div class="row h-100">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="card h-100 card-span p-3">
                                        <div class="card-body">
                                            <h5 class="mb-0 text-primary">Fantastic service!</h5>
                                            <p class="card-text pt-3">I purchased a phone from an e-commerce site, and
                                                this courier service provider assisted me in getting it delivered to my
                                                home. I received my phone within one day, and I was really satisfied
                                                with their service when I received it. </p>
                                            <div class="d-xl-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center mb-3"><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i></div>
                                                <div class="d-flex align-items-center"><img class="img-fluid"
                                                        src="{{ asset('landing/assets/img/icons/avatar.png') }}"
                                                        alt="" />
                                                    <div class="flex-1 ms-3">
                                                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Yves Tanguy</h6>
                                                        <p class="fs--2 fw-normal mb-0">Chief Executive, DLF</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="card h-100 card-span p-3">
                                        <div class="card-body">
                                            <h5 class="mb-0 text-primary">Fantastic service!</h5>
                                            <p class="card-text pt-3">I purchased a phone from an e-commerce site, and
                                                this courier service provider assisted me in getting it delivered to my
                                                home. I received my phone within one day, and I was really satisfied
                                                with their service when I received it.</p>
                                            <div class="d-xl-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center mb-3"><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i></div>
                                                <div class="d-flex align-items-center"><img class="img-fluid"
                                                        src="{{ asset('landing/assets/img/icons/avatar.png') }}"
                                                        alt="" />
                                                    <div class="flex-1 ms-3">
                                                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Kim Young Jou</h6>
                                                        <p class="fs--2 fw-normal mb-0">Chief Executive, DLF</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="card h-100 card-span p-3">
                                        <div class="card-body">
                                            <h5 class="mb-0 text-primary">Fantastic service!</h5>
                                            <p class="card-text pt-3">I purchased a phone from an e-commerce site, and
                                                this courier service provider assisted me in getting it delivered to my
                                                home. I received my phone within one day, and I was really satisfied
                                                with their service when I received it. .</p>
                                            <div class="d-xl-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center mb-3"><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i></div>
                                                <div class="d-flex align-items-center"><img class="img-fluid"
                                                        src="{{ asset('landing/assets/img/icons/avatar.png') }}"
                                                        alt="" />
                                                    <div class="flex-1 ms-3">
                                                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Yves Tanguy</h6>
                                                        <p class="fs--2 fw-normal mb-0">Chief Executive, DLF</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item" data-bs-interval="2000">
                            <div class="row h-100">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="card h-100 card-span p-3">
                                        <div class="card-body">
                                            <h5 class="mb-0 text-primary">Fantastic service!</h5>
                                            <p class="card-text pt-3">I purchased a phone from an e-commerce site, and
                                                this courier service provider assisted me in getting it delivered to my
                                                home. I received my phone within one day, and I was really satisfied
                                                with their service when I received it. </p>
                                            <div class="d-xl-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center mb-3"><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i></div>
                                                <div class="d-flex align-items-center"><img class="img-fluid"
                                                        src="{{ asset('landing/assets/img/icons/avatar.png') }}"
                                                        alt="" />
                                                    <div class="flex-1 ms-3">
                                                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Yves Tanguy</h6>
                                                        <p class="fs--2 fw-normal mb-0">Chief Executive, DLF</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="card h-100 card-span p-3">
                                        <div class="card-body">
                                            <h5 class="mb-0 text-primary">Fantastic service!</h5>
                                            <p class="card-text pt-3">I purchased a phone from an e-commerce site, and
                                                this courier service provider assisted me in getting it delivered to my
                                                home. I received my phone within one day, and I was really satisfied
                                                with their service when I received it. </p>
                                            <div class="d-xl-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center mb-3"><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i></div>
                                                <div class="d-flex align-items-center"><img class="img-fluid"
                                                        src="{{ asset('landing/assets/img/icons/avatar.png') }}"
                                                        alt="" />
                                                    <div class="flex-1 ms-3">
                                                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Kim Young Jou</h6>
                                                        <p class="fs--2 fw-normal mb-0">Chief Executive, DLF</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="card h-100 card-span p-3">
                                        <div class="card-body">
                                            <h5 class="mb-0 text-primary">Fantastic service!</h5>
                                            <p class="card-text pt-3">I purchased a phone from an e-commerce site, and
                                                this courier service provider assisted me in getting it delivered to my
                                                home. I received my phone within one day, and I was really satisfied
                                                with their service when I received it. .</p>
                                            <div class="d-xl-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center mb-3"><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i></div>
                                                <div class="d-flex align-items-center"><img class="img-fluid"
                                                        src="{{ asset('landing/assets/img/icons/avatar.png') }}"
                                                        alt="" />
                                                    <div class="flex-1 ms-3">
                                                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Yves Tanguy</h6>
                                                        <p class="fs--2 fw-normal mb-0">Chief Executive, DLF</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="row h-100">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="card h-100 card-span p-3">
                                        <div class="card-body">
                                            <h5 class="mb-0 text-primary">Fantastic service!</h5>
                                            <p class="card-text pt-3">I purchased a phone from an e-commerce site, and
                                                this courier service provider assisted me in getting it delivered to my
                                                home. I received my phone within one day, and I was really satisfied
                                                with their service when I received it. </p>
                                            <div class="d-xl-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center mb-3"><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i></div>
                                                <div class="d-flex align-items-center"><img class="img-fluid"
                                                        src="{{ asset('landing/assets/img/icons/avatar.png') }}"
                                                        alt="" />
                                                    <div class="flex-1 ms-3">
                                                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Yves Tanguy</h6>
                                                        <p class="fs--2 fw-normal mb-0">Chief Executive, DLF</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="card h-100 card-span p-3">
                                        <div class="card-body">
                                            <h5 class="mb-0 text-primary">Fantastic service!</h5>
                                            <p class="card-text pt-3">“I purchased a phone from an e-commerce site, and
                                                this courier service provider assisted me in getting it delivered to my
                                                home. I received my phone within one day, and I was really satisfied
                                                with their service when I received it. </p>
                                            <div class="d-xl-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center mb-3"><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i></div>
                                                <div class="d-flex align-items-center"><img class="img-fluid"
                                                        src="{{ asset('landing/assets/img/icons/avatar.png') }}"
                                                        alt="" />
                                                    <div class="flex-1 ms-3">
                                                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Kim Young Jou</h6>
                                                        <p class="fs--2 fw-normal mb-0">Chief Executive, DLF</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="card h-100 card-span p-3">
                                        <div class="card-body">
                                            <h5 class="mb-0 text-primary">Fantastic service!</h5>
                                            <p class="card-text pt-3">I purchased a phone from an e-commerce site, and
                                                this courier service provider assisted me in getting it delivered to my
                                                home. I received my phone within one day, and I was really satisfied
                                                with their service when I received it. .</p>
                                            <div class="d-xl-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center mb-3"><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i><i
                                                        class="fas fa-star text-primary me-1"></i></div>
                                                <div class="d-flex align-items-center"><img class="img-fluid"
                                                        src="{{ asset('landing/assets/img/icons/avatar.png') }}"
                                                        alt="" />
                                                    <div class="flex-1 ms-3">
                                                        <h6 class="mb-0 fs--1 text-1000 fw-medium">Yves Tanguy</h6>
                                                        <p class="fs--2 fw-normal mb-0">Chief Executive, DLF</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row px-3 px-md-0 mt-6">
                        <div class="col-12 position-relative">
                            <ol class="carousel-indicators">
                                <li class="active" data-bs-target="#carouselExampleDark" data-bs-slide-to="0"></li>
                                <li data-bs-target="#carouselExampleDark" data-bs-slide-to="1"></li>
                                <li data-bs-target="#carouselExampleDark" data-bs-slide-to="2"></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end of .container-->

        </section>
        <!-- <section> close ============================-->
        <!-- ============================================-->




        <!-- ============================================-->
        <!-- <section> begin ============================-->
        {{-- <section>

            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-5 col-xl-4"><img
                            src="{{ asset('landing/assets/img/illustrations/callback.png') }}" alt="..." />
                        <h5 class="text-danger">REQUEST A CALLBACK</h5>
                        <h2>We will contact in the shortest time.</h2>
                        <p class="text-muted">Monday to Friday, 9am-5pm.</p>
                    </div>
                    <div class="col-md-6 col-lg-5 col-xl-4">
                        <form class="row">
                            <div class="mb-3">
                                <label class="form-label visually-hidden" for="inputName">Name</label>
                                <input class="form-control form-quriar-control" id="inputName" type="text"
                                    placeholder="Name" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label visually-hidden" for="inputEmail">Another label</label>
                                <input class="form-control form-quriar-control" id="inputEmail" type="email"
                                    placeholder="Email" />
                            </div>
                            <div class="mb-5">
                                <label class="form-label visually-hidden" for="validationTextarea">Message</label>
                                <textarea class="form-control form-quriar-control is-invalid border-400" id="validationTextarea"
                                    placeholder="Message" style="height: 150px" required="required"></textarea>
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-primary" type="submit">Send Message<i
                                        class="fas fa-paper-plane ms-2"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- end of .container-->

        </section> --}}
        <!-- <section> close ============================-->
        <!-- ============================================-->




        <!-- ============================================-->
        <!-- <section> begin ============================-->
        @include('findus');
        <!-- <section> close ============================-->
        <!-- ============================================-->




        <!-- ============================================-->
        <!-- <section> begin ============================-->
        @include('newsletter')
        <!-- <section> close ============================-->
        <!-- ============================================-->




        <!-- ============================================-->
        <!-- <section> begin ============================-->
        @include('footernav')
        <!-- <section> close ============================-->
        <!-- ============================================-->




        <!-- ============================================-->
        <!-- <section> begin ============================-->
        @include('footer')
        <!-- <section> close ============================-->
        <!-- ============================================-->


    </main>
    <!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->




    <!-- ===============================================-->
    <!--    JavaScripts-->
    <!-- ===============================================-->
    <script src="{{ asset('landing/vendors/@popperjs/popper.min.js') }}"></script>
    <script src="{{ asset('landing/vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('landing/vendors/is/is.min.js') }}"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src="{{ asset('landing/vendors/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/theme.js') }}"></script>

    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@200;300;400;500;600;700;800&amp;display=swap"
        rel="stylesheet">
</body>

</html>
