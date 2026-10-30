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
            <h1 class="text-white display-3">{{ __('hompage.blogs.name') }}</h1>
            <div class="d-inline-flex align-items-center text-white">
                <p class="m-0"><a class="text-white" href="{{ url('/') }}">Home</a></p>
                <i class="fa fa-circle px-3"></i>
                <p class="m-0">{{ __('hompage.blogs.name') }}</p>
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
                        <div class="col-md-3 mb-3">
                            <div class="card h-100">
                                <img src="{{ asset('images/' . $product->image) }}" class="card-img-top"
                                    alt="{{ $product->title }}">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $product->title }}</h5>
                                    <p class="card-text">{{ strlen(strip_tags(html_entity_decode($product->description))) > 100 
                                        ? substr(strip_tags(html_entity_decode($product->description)), 0, 100) . '...' 
                                        : strip_tags(html_entity_decode($product->description)) }}
                                    </p>
                                    
                                    <div class="mt-3 text-center">
                                        <a href="{{ route('blog-details',$product->slug) }}" class="btn btn-primary" >
                                            {{ __('hompage.blogs.button') }}
                                        </a>
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


    {{-- /content end --}}
    <!-- Contact Start -->
    @include('findus')
    <!-- Contact End -->


    <!-- Footer Start -->
    @include('footer')
</body>

</html>