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
    <script src="https://cdn.tailwindcss.com"></script>
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

        .headi {
            background-image: linear-gradient(to right, white 30%, transparent), url('landing/assets/img/gallery/about.png');
            background-repeat: no-repeat;
        }
    </style>
</head>

<body>
    <!-- Topbar Start -->
    @include('navbar')
    <!-- Navbar End -->


    <!-- Header Start -->
    <div class="headi flex items-center w-screen lg:h-screen p-5 bg-contain md:bg-cover">

        <div class="w-full lg:w-1/2 flex flex-col md:items-center justify-center">
            <div class="lg:w-[500px] space-y-3">
                <h1 class="md:text-4xl">{{ __('hompage.38') }}</h1>
                <div class="flex space-x-2 items-center">
                    <h1 class="text-gray-600">Home</h1>

                    <svg class="text-primary" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 256 256">
                        <path fill="currentColor"
                            d="m224.49 136.49l-72 72a12 12 0 0 1-17-17L187 140H40a12 12 0 0 1 0-24h147l-51.49-51.52a12 12 0 0 1 17-17l72 72a12 12 0 0 1-.02 17.01" />
                    </svg>

                    <h1 class="text-gray-600">{{ __('hompage.38') }}</h1>
                </div>

            </div>
        </div>

    </div>
    <!-- Header End -->

    <div class="h-auto p-5 flex items-center justify-center">

        <div class="w-full px:[30px] md:px-[100px] flex-col-reverse md:flex-row flex items-center justify-between">

            <div class="flex w-full md:px-0 lg:w-1/2 items-center">
                <div class="w-full space-y-3">
                    <h1 class="text-primary">{{ __('hompage.38') }}</h1>
                    {!! __('hompage.108') !!}
                    <div>
                        <a href="/register" class="bg-primary hover:no-underline text-white rounded-full p-2">{{ __('hompage.74') }}</a>
                    </div>
                </div>
            </div>

            <div>
                <img src="/landing/assets/img/gallery/payment.png" alt="" srcset="">
            </div>

        </div>

    </div>

    {{-- /content end --}}
    <!-- Contact Start -->
    @include('findus')
    <!-- Contact End -->




    <!-- Footer Start -->
    @include('footer')
</body>

</html>
