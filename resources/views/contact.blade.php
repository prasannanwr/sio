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

        .headi {
            background-image: linear-gradient(to right, white 30%, transparent), url('landing/assets/img/gallery/contact.png');
            background-size: cover;
            background-repeat: no-repeat;
        }
    </style>
</head>

<body class="overflow-x-hidden">
    <!-- Topbar Start -->
    @include('navbar')
    <!-- Navbar End -->


    <!-- Header Start -->
    <div class="headi flex items-center h-auto md:h-screen p-5 bg-contain md:bg-cover">

        <div class="w-full lg:w-1/2 flex flex-col items-center justify-center">
            <div class="lg:w-[500px] space-y-3">
                <h1 class="md:text-4xl">{{ __('hompage.126.name') }}</h1>
                <div class="flex space-x-2 items-center">
                    <h1 class="text-gray-600">Home</h1>

                    <svg class="text-primary" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 256 256">
                        <path fill="currentColor"
                            d="m224.49 136.49l-72 72a12 12 0 0 1-17-17L187 140H40a12 12 0 0 1 0-24h147l-51.49-51.52a12 12 0 0 1 17-17l72 72a12 12 0 0 1-.02 17.01" />
                    </svg>

                    <h1 class="text-gray-600">{{ __('hompage.126.name') }}</h1>
                </div>

            </div>
        </div>

    </div>
    <!-- Header End -->

    {{-- content --}}
    <div class="h-screen">

        <div class="h-1/2 bg-white">
            <div class="flex justify-center mt-10 flex-col items-center">
                <h1 class="md:text-4xl">{{ __('hompage.126.form_title') }}</h1>
                <p class="font-bold">{{ __('hompage.126.form_description') }}</p>
            </div>
        </div>

        <div class="h-1/2 bg-primary relative flex justify-center items-center">

            <div class="bg-white rounded-lg absolute -top-24 w-auto shadow-xl p-3">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    <div class="space-y-2">
                        <h1>{{ __('hompage.126.field_name1') }}:</h1>
                        <input type="text" placeholder="{{ __('hompage.126.field_name1') }}"
                            class="bg-gray-200 text-black placeholder:text-black p-2 rounded-md outline-none">
                    </div>

                    <div class="space-y-2">
                        <h1>{{ __('hompage.126.field_name2') }}:</h1>
                        <input type="text" placeholder="{{ __('hompage.126.field_name2') }}"
                            class="bg-gray-200 text-black placeholder:text-black p-2 rounded-md outline-none">
                    </div>

                </div>

                <div class="grid grid-cols-1">

                    <div class="space-y-2 w-full">
                        <h1>{{ __('hompage.126.field_name3') }}:</h1>
                        <input type="text" placeholder="{{ __('hompage.126.field_name3') }}"
                            class="bg-gray-200 text-black placeholder:text-black p-2 flex-1 rounded-md outline-none">
                    </div>

                    <div class="space-y-2 w-full">
                        <h1>{{ __('hompage.126.field_name4') }}:</h1>
                        <textarea placeholder="{{ __('hompage.126.field_name4') }}"
                            class="bg-gray-200 text-black placeholder:text-black p-2 flex-1 rounded-md outline-none"></textarea>
                    </div>

                </div>

                <div class="flex justify-center">
                    <button class="bg-primary rounded-full text-white p-2">{{ __('hompage.126.button_name') }}</button>
                </div>
            </div>

        </div>

    </div>

    <div class="h-auto p-5 md:px-60 bg-white">

        <div class="flex justify-center mt-10 flex-col items-center">
            <h1 class="md:text-4xl">{{ __('hompage.126.form_title') }}</h1>
            <p class="font-bold">{{ __('hompage.126.form_description') }}</p>
        </div>

        <div class=" mt-5">

            <div class="grid grid-cols-1 md:grid-cols-2 justify-center gap-8 lg:grid-cols-3">

                <div class="border border-primary rounded-md space-y-5 p-5 flex flex-col items-center">

                    <div class="h-16 w-16 bg-gray-200 rounded-full items-center flex justify-center ">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            class="relative text-primary" viewBox="0 0 512 512">
                            <path fill="currentColor"
                                d="M32 376a56 56 0 0 0 56 56h336a56 56 0 0 0 56-56V222H32Zm66-76a30 30 0 0 1 30-30h48a30 30 0 0 1 30 30v20a30 30 0 0 1-30 30h-48a30 30 0 0 1-30-30ZM424 80H88a56 56 0 0 0-56 56v26h448v-26a56 56 0 0 0-56-56" />
                        </svg>
                    </div>

                    <h1 class="text-primary md:text-xl">{{ __('hompage.126.location') }}</h1>

                    <p class="text-center">Piazza Medaglia d'Oro Porcelli 88046 Lamezia Terme (CZ), Italy</p>

                </div>

                <div class="border border-primary rounded-md space-y-5 p-5 flex flex-col items-center">

                    <div class="h-16 w-16 bg-gray-200 rounded-full items-center flex justify-center ">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            class="relative text-primary" viewBox="0 0 512 512">
                            <path fill="currentColor"
                                d="M32 376a56 56 0 0 0 56 56h336a56 56 0 0 0 56-56V222H32Zm66-76a30 30 0 0 1 30-30h48a30 30 0 0 1 30 30v20a30 30 0 0 1-30 30h-48a30 30 0 0 1-30-30ZM424 80H88a56 56 0 0 0-56 56v26h448v-26a56 56 0 0 0-56-56" />
                        </svg>
                    </div>

                    <h1 class="text-primary md:text-xl">{{ __('hompage.126.phone') }}</h1>

                    <div>
                        <p class="text-center">+39 0968 191 6024 ({{ __('hompage.126.office') }})</p>
                        <p class="text-center">+393770993615 ({{ __('hompage.126.mobile') }})</p>
                        <p class="text-center">+39 0968 191 6024 ({{ __('hompage.126.fax') }})</p>
                    </div>

                </div>

                <div class="border border-primary rounded-md space-y-5 p-5 flex flex-col items-center">

                    <div class="h-16 w-16 bg-gray-200 rounded-full items-center flex justify-center ">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            class="relative text-primary" viewBox="0 0 512 512">
                            <path fill="currentColor"
                                d="M32 376a56 56 0 0 0 56 56h336a56 56 0 0 0 56-56V222H32Zm66-76a30 30 0 0 1 30-30h48a30 30 0 0 1 30 30v20a30 30 0 0 1-30 30h-48a30 30 0 0 1-30-30ZM424 80H88a56 56 0 0 0-56 56v26h448v-26a56 56 0 0 0-56-56" />
                        </svg>
                    </div>

                    <h1 class="text-primary md:text-xl">{{ __('hompage.126.email') }}</h1>

                    <div>
                        <p class="text-center">support@siopay.eu</p>
                        <p class="text-center">03940320793 (P. IVA)</p>
                    </div>
                </div>

            </div>

        </div>

    </div>


    <!-- Footer Start -->
    @include('footer')
</body>

</html>
