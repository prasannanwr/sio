<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Siopay Logistics | Login</title>
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
            background-image: linear-gradient(to right, white 30%, transparent), url('landing/assets/img/gallery/hero.png');
            background-size: cover;
            background-repeat: no-repeat;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="">

        <div class="headi flex items-center h-screen md:h-screen p-3 bg-contain md:bg-cover">

            <div class="w-full lg:w-1/2 flex flex-col items-center justify-center">
                <div class="lg:w-[500px] space-y-5 bg-white flex flex-col items-center rounded-xl p-3 shadow-xl">

                    <h1>Welcome!</h1>

                    <div class="flex flex-col items-center">
                        <h1 class="text-primary">Login</h1>
                        <p class="text-center">If you do not have an account, please register</p>
                    </div>
                    <form action="{{ route('login') }}" method="post">
                        @csrf
                        <div class="space-y-2 w-full">
                            <h1>Email</h1>
                            <input type="text" placeholder="Email" name="email"
                                class="bg-gray-200 text-black placeholder:text-black w-full p-2 flex-1 rounded-md outline-none">
                            @error('email')
                                <strong class="text-danger">{{ $message }}</strong>
                            @enderror
                        </div>

                        <div class="space-y-2 w-full">
                            <h1>Password</h1>
                            <input type="password" placeholder="Password" name="password"
                                class="bg-gray-200 text-black placeholder:text-black w-full p-2 flex-1 rounded-md outline-none">

                            @error('password')
                                
                                    <strong class="text-danger">{{ $message }}</strong>
                                
                            @enderror
                        </div>

                        <div class="w-full items-center flex flex-col mt-2">
                            <button class="w-full bg-primary p-2 text-white rounded-lg">Login</button>
                            <p>Don't have an account? <a class="btn btn-link" href="/register">Sign
                                    up</a></p>
                            <div>
                                <a class="btn btn-link" href="/password/reset">Forgot password?</a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>

    <style>
        .headi {
            background-image: linear-gradient(to right, white 30%, transparent), url('landing/assets/img/gallery/login.png');
            background-size: cover;
            background-repeat: no-repeat;
        }
    </style>
</body>

</html>
{{--
<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="email"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Login') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
--}}
