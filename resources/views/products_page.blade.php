<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ env('APP_NAME') }} | @yield('page_title')</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Shipping in italy" name="keywords">
    <meta content="Shipping in italy" name="description">

    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#218bff">
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('landing2/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('landing2/css/style.css') }}" rel="stylesheet">
    <style>
        @media only screen and (max-width: 767px) {

            .table>tbody>tr>td,
            .table>tbody>tr>th,
            .table>tfoot>tr>td,
            .table>tfoot>tr>th,
            .table>thead>tr>td,
            .table>thead>tr>th {
                display: block;
                min-width: 100% !important;
            }

            .table>thead>tr>th {
                display: none;
            }

            .table .filterBody span {
                display: block;
            }
        }
    </style>
</head>

<body>
    <!-- Topbar Start -->
    @include('navbar')
    <!-- Navbar End -->


    <!-- Header Start -->
    <div class="jumbotron jumbotron-fluid mb-5">
        <div class="container text-center py-5">
            <h1 class="text-white display-3">{{ __('hompage.showcase.title') }}</h1>
            <div class="d-inline-flex align-items-center text-white">
                <p class="m-0"><a class="text-white" href="">Home</a></p>
                <i class="fa fa-circle px-3"></i>
                <p class="m-0"> {{ __('hompage.showcase.title') }} </p>
            </div>
        </div>
    </div>
    <!-- Header End -->

    {{-- content --}}
    <section class="py-xxl-10 pb-0" id="home">
        <!--/.bg-holder-->

        <div class="container">
            <div class="row">
                @if (isset($products) && count($products) > 0)
                    @foreach ($products as $product)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <img src="{{ asset('images/' . $product->image) }}" class="card-img-top"
                                    alt="{{ $product->name }}">
                                <div class="card-body">
                                    @php
                                    if (session('locale')  == 'ar'){
                                        $name = $product->name_ar;
                                        $description = $product->description_ar;
                                    }
                                    else if (session('locale')  == 'it'){
                                        $name = $product->name_it;
                                        $description = $product->description_it;
                                    }
                                    else if (session('locale')  == 'ch'){
                                        $name = $product->name_ch;
                                        $description = $product->description_ch;
                                    }
                                    else if (session('locale')  == 'fr'){
                                        $name = $product->name_fr;
                                        $description = $product->description_fr;
                                    }
                                    else if (session('locale')  == 'dt'){
                                        $name = $product->name_dt;
                                        $description = $product->description_dt;
                                    }
                                    else if (session('locale')  == 'pt'){
                                        $name = $product->name_pt;
                                        $description = $product->description_pt;
                                    }
                                    else{
                                        $name = $product->name;
                                        $description = $product->description;
                                    }

                                    @endphp
                                    <h5 class="card-title">{{ $name }}</h5>
                                    <p class="card-text">{{ $description }}</p>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item"><strong>{{ __('hompage.showcase.title1') }}:</strong>
                                            &euro;{{ $product->price_per_tonne }}</li>
                                        <li class="list-group-item"><strong>{{ __('hompage.showcase.title2') }}:</strong>
                                            &euro;{{ $product->price_per_kg }}</li>
                                        <li class="list-group-item"><strong>{{ __('hompage.showcase.title3') }}:</strong>
                                            {{ $product->origin_port }}</li>
                                        <li class="list-group-item"><strong>{{ __('hompage.showcase.title4') }}:</strong>
                                            {{ $product->supported_ports }}</li>
                                        <li class="list-group-item"><strong>{{ __('hompage.showcase.title5') }}:</strong>
                                            {{ $product->shipping_terms }}
                                        </li>
                                    </ul>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#quoteModal-{{ $product->id }}">
                                            {{ __('hompage.showcase.quote_button') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{ $products->links() }}
                @else
                <p><i>{{ __('hompage.blogs.no_product') }}</i></p>
                @endif
            </div>

        </div>
    </section>
    <!-- Quote Modal -->
    @foreach ($products as $product)
        <div class="modal fade" id="quoteModal-{{ $product->id }}" tabindex="-1"
            aria-labelledby="quoteModalLabel-{{ $product->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="quoteModalLabel-{{ $product->id }}">Request Detailed Quote for
                            {{ $product->name }}</h5>
                        <button type="button" class="btn-close" style="margin-right: -100px" data-bs-dismiss="modal" aria-label="Close">X Close</button>
                        <br>
                    </div>
                    <div class="modal-body">
                        <small>Kindly fill the form and our team will get back to you.</small>
                        <br>
                        <small class="text-danger">All fields marked * are required</small>
                        <hr>
                        <form action="{{ route('inquiries.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="mb-3">
                                <label for="email" class="form-label">Your Email address <span class="text-danger">*</span> </label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="shipping_location" class="form-label">Your Desired Shipping Location/Port <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="shipping_location"
                                    name="shipping_location" required>
                            </div>
                            <div class="mb-3">
                                <label for="organization" class="form-label">Your Organization/Personal Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="organization" name="organization"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="organization" class="form-label">Your Note/ Message</label>
                                <textarea name="note" class="form-control"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Inquiry</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- /content end --}}
    <!-- Contact Start -->
    @include('findus')
    <!-- Contact End -->


    <!-- Footer Start -->
    @include('footer')
</body>

</html>
