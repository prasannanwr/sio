<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ env('APP_NAME') }} | Account Removal</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="description" content="How to remove (delete) your {{ env('APP_NAME') }} account and what happens to your data.">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('landing2/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('landing2/css/style.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #222; line-height: 1.6; margin: 0; background: #f7f7f9; }
        .wrap { max-width: 760px; margin: 0 auto; padding: 32px 20px; }
        .card { background: #fff; border-radius: 10px; padding: 32px; }
        h1 { font-size: 24px; margin-top: 0; }
        h2 { font-size: 18px; margin-top: 28px; }
        ol, ul { padding-left: 20px; }
        .muted { color: #666; font-size: 14px; }
        code { background: #f0f1f5; padding: 2px 6px; border-radius: 4px; }
        hr { border: 0; border-top: 1px solid #eee; margin: 28px 0; }
    </style>
</head>
<body>
    @include('navbar')
<!-- Header Start -->
    <div class="jumbotron jumbotron-fluid mb-5">
        <div class="container text-center py-5">
            <h1 class="text-white display-3">{{__('nav.12')}}</h1>
            <div class="d-inline-flex align-items-center text-white">
                <p class="m-0"><a class="text-white" href="">Home</a></p>
                <i class="fa fa-circle px-3"></i>
                <p class="m-0">{{__('nav.12')}}</p>
            </div>
        </div>
    </div>
    <!-- Header End -->

    {{-- content --}}
    <section class="py-xxl-10 pb-0" id="home">
        <div class="bg-holder bg-size"
            style="background-image:url({{ asset('landing/assets/img/gallery/hero-header-bg.png') }});background-position:top center;background-size:cover;">
        </div>
        <!--/.bg-holder-->

        <div class="container">
            <div class="row align-items-left">
                <div class="col-11 text-md-start text-left py-8">
                    <h1 class="fw-normal fs-6 fs-xxl-7">{{__('nav.12')}} </h1>
                        <h1 class="fw-bolder fs-6 fs-xxl-7 mb-2"></h1>
                        <p class="fs-1 mb-5">
                             {!!trans('hompage.144')!!}
                        </p>
                </div>
            </div>
        </div>
    </section>
    {{-- /content end --}}
    <!-- Contact Start -->
    @include('findus')
    <!-- Contact End -->
    @include('footer')
</body>
</html>
