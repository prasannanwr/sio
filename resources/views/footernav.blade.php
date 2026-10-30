<section class="bg-900 pb-0 pt-5">

    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-6 mb-4 order-0 order-sm-0"><a class="text-decoration-none"
                    href="#"><img src="{{ asset('landing/assets/img/gallery/siopay_logo.png') }}" height="51"
                        alt="" /></a>
                <p class="text-500 my-4">The most trusted Courier<br />company in the EU.</p>
            </div>
            <div class="col-6 col-sm-4 col-lg-2 mb-3 order-2 order-sm-1">
                <h5 class="lh-lg fw-bold mb-4 text-light font-sans-serif">Other links </h5>
                <ul class="list-unstyled mb-md-4 mb-lg-0">
                    <li class="lh-lg"><a class="text-500" href="#!">Blogs</a></li>
                    {{-- <li class="lh-lg"><a class="text-500" href="#!">Movers website</a></li>
                    <li class="lh-lg"><a class="text-500" href="#!">Traffic Update</a></li> --}}
                </ul>
            </div>
            <div class="col-6 col-sm-4 col-lg-2 mb-3 order-3 order-sm-2">
                <h5 class="lh-lg fw-bold text-light mb-4 font-sans-serif">Services</h5>
                <ul class="list-unstyled mb-md-4 mb-lg-0">

                    <li class="lh-lg"><a class="text-500" href="{{ route('shipping_page') }}">Shipping Services</a>
                    </li>
                    <li class="lh-lg"><a class="text-500" href="{{ route('payment_page') }}">Payment services</a></li>
                    {{-- <li class="lh-lg"><a class="text-500" href="{{ route('tup_up_page') }}">Pre-paid Card top-Up</a>
                    </li> --}}
                    <li class="lh-lg"><a class="text-500" href="{{ route('pick_up_point_page') }}">Pick-Up points</a>
                    </li>
                    {{-- <li class="lh-lg"><a class="text-500" href="{{ route('spdi_page') }}">SPDI and Digital
                            Signatures</a></li> --}}
                    <li class="lh-lg"><a class="text-500" href="{{ route('pec_page') }}">Certified Electronic Mail
                            service(PEC)</a></li>
                    <li class="lh-lg"><a class="text-500" href="{{ route('roadside_page') }}">Roadside Assistance
                            Card</a>
                    </li>
                    <li class="lh-lg"><a class="text-500" href="{{ route('ticket_resale_page') }}">Ticket Resale</a>
                    </li>
                    {{-- <li class="lh-lg"><a class="text-500" href="{{ route('gas_page') }}">Electricity and Gas</a></li> --}}
                </ul>
            </div>
            <div class="col-6 col-sm-4 col-lg-2 mb-3 order-3 order-sm-2">
                <h5 class="lh-lg fw-bold text-light mb-4 font-sans-serif"> Customer Care</h5>
                <ul class="list-unstyled mb-md-4 mb-lg-0">
                    <li class="lh-lg"><a class="text-500" href="{{route('who_we_are')}}">About Us</a></li>
                    <li class="lh-lg"><a class="text-500" href="#findUs">Contact US</a></li>
                    <li class="lh-lg"><a class="text-500" href="{{route('home')}}">Dashboard</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- end of .container-->

</section>
