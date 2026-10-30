<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Siopay Logistics | @yield('page_title')</title>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

<body style="">
    <!-- Topbar Start -->
    @include('navbar')
    <!-- Navbar End -->

    {{-- <div class="headi flex items-center h-auto md:h-screen p-5 bg-contain md:bg-cover">

        <div class="w-full lg:w-1/2 flex flex-col items-center justify-center">
            <div class="lg:w-[500px] space-y-3">
                <h1 class="text-primary text-xl">Secure & Faster</h1>
                <h1 class="md:text-4xl">The Best VTU Payment <br> Service & VTU Platform</h1>

                <p class="font-bold text-black">Elevate your business and add value to your offerings by selecting
                    SIOPAY as your
                    payment services
                    solution.</p>

                <div class="flex space-x-2">
                    <button
                        class="bg-primary p-2 hover:bg-transparent hover:text-primary hover:border-2 hover:border-primary text-white rounded-full">Open
                        Account</button>
                    <button class="text-primary border-2 font-bold border-primary rounded-full p-2">Get in
                        touch</button>
                </div>
            </div>
        </div>

    </div> --}}

    <!-- Header Start -->
    <div class="jumbotron jumbotron-fluid mb-5">
        <div class="container text-center py-5">
            <h1 class="text-primary mb-4">{{ trans('hompage.welcome1') }}</h1>
            <h1 class="text-white display-3 typing-animation mb-5">{{ __('hompage.welcome2') }}</h1>
            <br>
            <a href="{{ route('showcase') }}" class="btn btn-primary">Visit Our Global Product Trade Showcase</a>
            <br>
            <br>
            <div class="mx-auto" style="width: 100%;">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ !empty(request()->get('origin_id')) || empty($_GET) ? 'active' : '' }}"
                            id="shipping-tab" data-toggle="tab" href="#shipping" role="tab" aria-controls="shipping"
                            aria-selected="true">{{ __('hompage.ship_quote_header') }}</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link {{ !empty(request()->get('s_country_eu')) ? 'active' : '' }}"
                            id="eu-funds-tab" data-toggle="tab" href="#eu-funds" role="tab" aria-controls="eu-funds"
                            aria-selected="false">{{__('hompage.eu_fund_quote_header')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ !empty(request()->get('s_country')) ? 'active' : '' }}"
                            id="intl-funds-tab" data-toggle="tab" href="#intl-funds" role="tab"
                            aria-controls="intl-funds" aria-selected="false">{{__('hompage.intl_fund_quote_header')}}</a>
                    </li> --}}
                </ul>
                <style>
                    .form-sm {
                        height: 27px;
                        border-radius: 3px;
                    }
                    label{
                        font-size: 12px;
                    }
                    ::placeholder {
                        font-size: 11px;
                    }

                    .form-card {
                        background-color: #ffffff4f;
                    }
                </style>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade {{ !empty(request()->get('origin_id')) || empty($_GET) ? 'show active' : '' }}"
                        id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                        <hr>
                        {{--  <form class="mt-4" action="" method="get" class="">  --}}
                        <div class="card form-card p-3">
                            <div class="row my-4">
                                <div class="col-md-4 col-lg-3 text-white text-start mb-2">
                                    <div class="ui-widget">
                                        <label for="origin">Shipping from Country<i class="text-danger">*</i> :
                                        </label> <br>
                                        <select name="ship_from_country" id="ship_from_country" class="form-control">

                                        </select>
                                        @error('ship_from_country')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-3 text-white text-start mb-2">
                                    <div class="ui-widget">
                                        <label for="origin">Shipping from City<i class="text-danger">*</i> : </label>
                                        <br>
                                        <select name="ship_from_city" id="ship_from_city" class="form-control">
                                        </select>
                                        @error('ship_from_city')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-3 text-white text-start mb-2">
                                    <div class="ui-widget">
                                        <label for="ship_from_state">Inter State Name<i class="text-danger">*</i> :
                                        </label> <br>
                                        <input type="text" placeholder="Inter State Name"
                                            class="form-control form-control-sm form-sm">
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-3 text-white text-start mb-2">
                                    <div class="ui-widget">
                                        <label for="weight_tot">Total Weight<i class="text-danger">*</i> : </label>
                                        <br>
                                        <input type="number" class="form-control form-sm" id="weight_tot">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-4 col-lg-3 text-white text-start mb-2">
                                    <div class="ui-widget">
                                        <label for="ship_to_country">Shipping to Country<i class="text-danger">*</i> :
                                        </label> <br>
                                        <select name="ship_to_country" id="ship_to_country" class="form-control">

                                        </select>
                                        @error('ship_to_country')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-3 text-white text-start mb-2">
                                    <div class="ui-widget">
                                        <label for="ship_to_city">Shipping to City<i class="text-danger">*</i> :
                                        </label> <br>
                                        <select name="ship_to_city" id="ship_to_city"
                                            class="form-select w-100 select2">
                                        </select>
                                        @error('ship_to_city')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-3 text-white text-start mb-2">
                                    <div class="ui-widget">
                                        <label for="ship_from_state">Inter State Name<i class="text-danger">*</i> :
                                        </label> <br>
                                        <input type="text" placeholder="Inter State Name"
                                            class="form-control form-control-sm form-sm">
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-3 mt-auto text-white text-start mb-2">
                                    <div class="bg-white pb-3 px-3 rounded d-flex gap-3 align-items-center pt-3">
                                        <h4 class="mb-0">Shipping Cost: </h4>
                                        <h5 class="mb-0" id="shipping_rate_list"></h5>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-4 ps-0 text-start" style="width: 100%">
                                <button class="btn btn-primary m-1"
                                    onclick="getRates()">{{ __('hompage.proceed') }}</button>
                                {{--  <a class="btn btn-danger m-1" href="/">{{ __('hompage.refresh') }}</a>  --}}
                            </div>
                        </div>
                        {{--  </form>  --}}
                        @if (!empty(request()->get('origin_id') && !empty(request()->get('dest_id'))))
                            <div class="bg-dark text-secondary p-2">
                                <div class="justify-items-left">
                                    <p>{{ __('hompage.1') }} <b>{{ request()->get('origin_') }}</b> to
                                        <b>{{ request()->get('dest_') }}</b>
                                    </p>
                                    <br>
                                    <p>
                                        {{ __('hompage.2') }}
                                    </p>
                                </div>
                                <div>
                                    <table class="table">
                                        <thead>
                                            <th>{{ __('hompage.3') }} <i class="text-danger">*</i></th>
                                            <th>{{ __('hompage.4') }} <i class="text-danger">*</i></th>
                                            <th>{{ __('hompage.5') }} <i class="text-danger">*</i></th>
                                            <th>{{ __('hompage.6') }} <i class="text-danger">*</i></th>
                                            <th>{{ __('hompage.7') }} <i class="text-danger">*</i></th>
                                            <th>{{ __('hompage.8') }} <i class="text-danger">*</i></th>
                                            <td><button class="btn btn-primary btn-sm" type="button"
                                                    onclick="addRow()"><i class="fa fa-plus"></i>
                                                    {{ __('hompage.9') }}</button></td>
                                        </thead>
                                        <tbody id="items_list">
                                            <tr>
                                                <td>
                                                    <select name="type[]" class="form-control type"
                                                        onchange="calculateTotItems('type')" required>
                                                        <option value="">--{{ __('hompage.10') }}--</option>
                                                        <option value="percel">{{ __('hompage.11') }}</option>
                                                        <option value="doc">{{ __('hompage.12') }}</option>
                                                        <option value="pallet">{{ __('hompage.13') }}</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="any" min="0"
                                                        value="0" class="form-control len"
                                                        onkeyup="calculateTot('len')" name="len[]" required>
                                                </td>
                                                <td>
                                                    <input type="number" step="any" min="0"
                                                        value="0" class="form-control width"
                                                        onkeyup="calculateTot('width')" name="width[]" required>
                                                </td>
                                                <td>
                                                    <input type="number" step="any" min="0"
                                                        value="0" class="form-control height"
                                                        onkeyup="calculateTot('height')" name="height[]" required>
                                                </td>
                                                <td>
                                                    <input type="number" step="any" min="0"
                                                        value="0" class="form-control weight"
                                                        onkeyup="calculateTot('weight')" name="weight[]" required>
                                                </td>
                                                <td>
                                                    <input type="number" step="1" min="1"
                                                        value="1" class="form-control count"
                                                        onkeyup="calculateTot('count')" name="count[]" required>
                                                </td>
                                                <td>
                                                    {{-- <button class="btn btn-danger" type="button" onclick="removeRow(this)"><i
                                                    class="fa fa-trash"></i></button> --}}
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <th>
                                                {{ __('hompage.14') }}: <span id="type-tot"></span>

                                            </th>
                                            <th>
                                                {{ __('hompage.15') }}: <span id="len-tot"></span>

                                                <input type="hidden" name="len_tot" id="len_tot">
                                            </th>
                                            <th>
                                                {{ __('hompage.16') }}: <span id="width-tot"></span>

                                                <input type="hidden" name="width_tot" id="width_tot">
                                            </th>
                                            <th>
                                                {{ __('hompage.17') }}: <span id="height-tot"></span>

                                                <input type="hidden" name="height_tot" id="height_tot">
                                            </th>
                                            <th>
                                                {{ __('hompage.18') }}: <span id="weight-tot"></span>

                                                <input type="hidden" name="weight_tot" id="weight_tot">
                                            </th>
                                            <th>
                                                {{ __('hompage.19') }}: <span id="count-tot"></span>
                                                <input type="hidden" name="count_tot" id="count_tot">
                                            </th>
                                        </tfoot>
                                    </table>
                                </div>
                                <button type="button" onclick="getRates()"
                                    class="btn btn-primary">{{ __('hompage.20') }}</button>

                                <hr>
                                <h5>{{ __('hompage.21') }}</h5>
                                <div class="table-responsive">
                                    <table id="locations_tbl"
                                        class="table table-sm  table-bordered table-striped display">
                                        <thead>
                                            <tr>
                                                {{-- <th>Select <i class="text-danger">*</i></th> --}}
                                                <th>{{ __('hompage.22') }}</th>
                                                <th>{{ __('hompage.23') }}</th>
                                                <th>{{ __('hompage.24') }}</th>
                                                <th>{{ __('hompage.25') }}(&euro;)</th>
                                                <th>{{ __('hompage.26') }}(cm)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="shipping_rate_list">

                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="tab-pane fade {{ !empty(request()->get('s_country_eu')) ? 'show active' : '' }}"
                        id="eu-funds" role="tabpanel" aria-labelledby="eu-funds-tab">
                        <hr>
                        <form action="" method="get" class="form-inline justify-content-left">
                            <div class="form-group col-md-3">
                                <input style="width: 100%" type="text" name="s_country_eu"
                                    value="{{ request()->get('s_country_eu') }}" class="form-control"
                                    id="s_country_eu" autocomplete="off" placeholder="{{ __('hompage.27') }}"
                                    required>
                            </div>


                            <div class="form-group col-md-3">
                                <input style="width: 100%" type="text" name="rx_country_eu"
                                    value="{{ request()->get('rx_country_eu') }}" class="form-control"
                                    id="rx_country_eu" placeholder="{{ __('hompage.28') }}" autocomplete="off"
                                    required>
                            </div>
                            <div class="form-group col-md-3">
                                <input style="width: 100%" type="number" step="any" name="rx_amount_eu"
                                    value="{{ request()->get('rx_amount_eu') }}" class="form-control"
                                    id="rx_amount_eu" placeholder="{{ __('hompage.29') }}(&euro;)"
                                    autocomplete="off" required>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary m-1"
                                    type="submit">{{ __('hompage.proceed') }}</button>
                                <a class="btn btn-danger m-1" href="/">{{ __('hompage.refresh') }}</a>
                            </div>
                        </form>
                        <br>
                        @if (isset($eu_rate) && !empty(request()->get('s_country_eu')))
                            <div>
                                <table class="table">
                                    <thead>
                                        <th>{{ __('hompage.30') }}</th>
                                        <th>{{ __('hompage.25') }}(&euro;)</th>
                                        <th>{{ __('hompage.31') }}(&euro;)</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                {{ request()->get('s_country_eu') }}
                                                <br>
                                                To
                                                <br>
                                                {{ request()->get('rx_country_eu') }}
                                            </td>
                                            <td>
                                                {{ __('hompage.25') }}(&euro;) :
                                                {{ number_format(request()->get('rx_amount_eu')) }}
                                            </td>
                                            <td>
                                                {{ __('hompage.31') }}(&euro;):
                                                @if ($eu_rate->calc == 'perc')
                                                    {{ request()->get('rx_amount_eu') * ($eu_rate->commision / 100) }}
                                                @else
                                                    {{ number_format(request()->get('rx_amount_eu')) }}
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @elseif(!isset($eu_rate) && !empty(request()->get('s_country_eu')))
                            <i>{{ __('hompage.32') }}</i>
                        @endif
                    </div>
                    <div class="tab-pane fade {{ !empty(request()->get('s_country')) ? 'show active' : '' }}"
                        id="intl-funds" role="tabpanel" aria-labelledby="intl-funds-tab">
                        <hr>
                        <form action="" method="get" class="justify-content-left">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <input style="width: 100%" type="text" name="s_country"
                                        value="{{ request()->get('s_country') }}" class="form-control"
                                        id="s_country" autocomplete="off" placeholder="{{ __('hompage.27') }}"
                                        required>
                                </div>
                                <div class="form-group col-md-6">
                                    <input style="width: 100%" type="text" name="rx_country"
                                        value="{{ request()->get('rx_country') }}" class="form-control"
                                        id="rx_country" placeholder="{{ __('hompage.28') }}" autocomplete="off"
                                        required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <input style="width: 100%" type="text" name="s_currency"
                                        value="{{ request()->get('s_currency') }}" class="form-control"
                                        id="s_currency" autocomplete="off" placeholder="{{ __('hompage.33') }}"
                                        required>
                                </div>
                                <div class="form-group col-md-6">
                                    <input style="width: 100%" type="text" name="rx_currency"
                                        value="{{ request()->get('rx_currency') }}" class="form-control"
                                        id="rx_currency" placeholder="{{ __('hompage.34') }}" autocomplete="off"
                                        required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <input style="width: 100%" type="number" step="any" name="rx_amount"
                                        value="{{ request()->get('rx_amount') }}" class="form-control"
                                        id="rx_amount" placeholder="{{ __('hompage.29') }}(&euro;) "
                                        autocomplete="off" required>
                                </div>
                                <col-md class="6">
                                    <button class="btn btn-primary m-1"
                                        type="submit">{{ __('hompage.proceed') }}</button>
                                    <a class="btn btn-danger m-1"
                                        href="/">{{ __('hompage.refresh') }}</a></col-md>
                            </div>

                        </form>
                        <br>
                        @if (isset($global_rate) && !empty(request()->get('s_country')))
                            <div>
                                <table class="table">
                                    <thead>
                                        <th>{{ __('hompage.30') }}</th>
                                        <th>{{ __('hompage.35') }}</th>
                                        <th>{{ __('hompage.31') }}(&euro;)</th>
                                        <th>{{ __('hompage.25') }}(&euro;)</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                {{ request()->get('s_country') }}
                                                <br>
                                                To
                                                <br>
                                                {{ request()->get('rx_country') }}
                                            </td>
                                            <td>
                                                {{ __('hompage.29') }} ({{ request()->get('s_currency') }}) :
                                                {{ number_format(request()->get('rx_amount')) }}
                                                <br>
                                                {{ __('hompage.36') }} ({{ request()->get('rx_currency') }}) :
                                                {{ number_format(request()->get('rx_amount') * $global_rate->ex_rate) }}
                                            </td>
                                            <td>
                                                {{ __('hompage.31') }}(&euro;)
                                                @if ($global_rate->calc == 'perc')
                                                    {{ request()->get('rx_amount') * ($global_rate->commision / 100) }}
                                                @else
                                                    {{ number_format(request()->get('rx_amount')) }}
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @elseif(!isset($global_rate) && !empty(request()->get('s_country')))
                            <i>{{ __('hompage.32') }}</i>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <div class="h-auto bg-primary p-5">

        <h1 class="text-white text-center text-3xl">Our Services</h1>
        <h1 class="text-center text-xl">Trusted Logistics, and Other Services Provider</h1>


        <div class="grid grid-cols-1 md:grid-cols-2 mt-10 lg:grid-cols-4 gap-8">

            <div class="bg-white p-5 rounded-lg relative">
                <div
                    class="h-16 w-16 bg-gray-200 rounded-full items-center flex absolute justify-center -top-5 left-1/2 transform -translate-x-1/2">
                    <svg class="text-blue-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24">
                        <g fill="none" fill-rule="evenodd">
                            <path
                                d="M24 0v24H0V0zM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z" />
                            <path fill="currentColor"
                                d="M14.211 17.776a4 4 0 0 1 3.364-.099l.214.1l2.658 1.328a1 1 0 0 1-.787 1.835l-.107-.046l-2.659-1.329a2 2 0 0 0-1.617-.076l-.172.076l-1.316.659a4 4 0 0 1-3.365.098l-.213-.098l-1.317-.659a2 2 0 0 0-1.617-.076l-.172.076l-2.658 1.33a1 1 0 0 1-.996-1.731l.102-.059l2.658-1.329a4 4 0 0 1 3.364-.099l.214.1l1.316.658a2 2 0 0 0 1.618.076l.171-.076zM13 2a1 1 0 0 1 1 1v1.32l3.329.554a2 2 0 0 1 1.67 1.973v3.432l2.06.686a1.25 1.25 0 0 1 .753 1.679l-2.169 5.06l-1.854-.928a4 4 0 0 0-3.578 0l-1.317.659a2 2 0 0 1-1.789 0l-1.316-.659a4 4 0 0 0-3.578 0l-1.27.636l-2.658-4.651a1.25 1.25 0 0 1 .69-1.806L5 10.279V6.847a2 2 0 0 1 1.67-1.973L10 4.32V3a1 1 0 0 1 1-1zm-1 4.014l-5 .833v2.766l4.367-1.456a2 2 0 0 1 1.265 0L17 9.613V6.847z" />
                        </g>
                    </svg>
                </div>

                <div class="flex flex-col items-center space-y-3">
                    <h1>Shipping/ Courier Services</h1>
                    <p>We offer fast, efficient, and secure shipping solutions tailored to meet your logistical needs.
                    </p>
                    <button class="bg-primary font-bold text-white w-full rounded-full p-2"><a
                            href="{{ route('shipping_page') }}">Get Started</a></button>
                </div>
            </div>

            <div class="bg-white p-5 rounded-lg relative">
                <div
                    class="h-16 w-16 bg-gray-200 rounded-full items-center flex absolute justify-center -top-5 left-1/2 transform -translate-x-1/2">
                    <svg class="text-blue-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 20 20">
                        <path fill="currentColor"
                            d="M1 10c.41.29.96.43 1.5.43c.55 0 1.09-.14 1.5-.43c.62-.46 1-1.17 1-2c0 .83.37 1.54 1 2c.41.29.96.43 1.5.43c.55 0 1.09-.14 1.5-.43c.62-.46 1-1.17 1-2c0 .83.37 1.54 1 2c.41.29.96.43 1.51.43c.54 0 1.08-.14 1.49-.43c.62-.46 1-1.17 1-2c0 .83.37 1.54 1 2c.41.29.96.43 1.5.43c.55 0 1.09-.14 1.5-.43c.63-.46 1-1.17 1-2V7l-3-7H4L0 7v1c0 .83.37 1.54 1 2m2 8.99h5v-5h4v5h5v-7c-.37-.05-.72-.22-1-.43c-.63-.45-1-.73-1-1.56c0 .83-.38 1.11-1 1.56c-.41.3-.95.43-1.49.44c-.55 0-1.1-.14-1.51-.44c-.63-.45-1-.73-1-1.56c0 .83-.38 1.11-1 1.56c-.41.3-.95.43-1.5.44c-.54 0-1.09-.14-1.5-.44c-.63-.45-1-.73-1-1.57c0 .84-.38 1.12-1 1.57c-.29.21-.63.38-1 .44z" />
                    </svg>
                </div>

                <div class="flex flex-col items-center space-y-3">
                    <h1>Pick-Up points</h1>
                    <p>Enhance Your Business's Visibility with SIOPAY’S Shipping and Collection Service</p>
                    <button class="bg-primary font-bold text-white w-full rounded-full p-2"><a
                            href="{{ route('pick_up_point_page') }}">Get Started</a></button>
                </div>
            </div>

            <div class="bg-white p-5 rounded-lg relative">
                <div
                    class="h-16 w-16 bg-gray-200 rounded-full items-center flex absolute justify-center -top-5 left-1/2 transform -translate-x-1/2">
                    <svg class="text-blue-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24">
                        <g fill="none" fill-rule="evenodd">
                            <path
                                d="M24 0v24H0V0zM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z" />
                            <path fill="currentColor"
                                d="M14.211 17.776a4 4 0 0 1 3.364-.099l.214.1l2.658 1.328a1 1 0 0 1-.787 1.835l-.107-.046l-2.659-1.329a2 2 0 0 0-1.617-.076l-.172.076l-1.316.659a4 4 0 0 1-3.365.098l-.213-.098l-1.317-.659a2 2 0 0 0-1.617-.076l-.172.076l-2.658 1.33a1 1 0 0 1-.996-1.731l.102-.059l2.658-1.329a4 4 0 0 1 3.364-.099l.214.1l1.316.658a2 2 0 0 0 1.618.076l.171-.076zM13 2a1 1 0 0 1 1 1v1.32l3.329.554a2 2 0 0 1 1.67 1.973v3.432l2.06.686a1.25 1.25 0 0 1 .753 1.679l-2.169 5.06l-1.854-.928a4 4 0 0 0-3.578 0l-1.317.659a2 2 0 0 1-1.789 0l-1.316-.659a4 4 0 0 0-3.578 0l-1.27.636l-2.658-4.651a1.25 1.25 0 0 1 .69-1.806L5 10.279V6.847a2 2 0 0 1 1.67-1.973L10 4.32V3a1 1 0 0 1 1-1zm-1 4.014l-5 .833v2.766l4.367-1.456a2 2 0 0 1 1.265 0L17 9.613V6.847z" />
                        </g>
                    </svg>
                </div>

                <div class="flex flex-col items-center space-y-3">
                    <h1 class="text-center">Agro Import and export</h1>
                    <p>We are big players in the international trade of agricultural produce.</p>
                    <button class="bg-primary font-bold text-white w-full rounded-full p-2"><a
                            href="{{ route('shipping_page') }}">Get Started</a></button>
                </div>
            </div>

            <div class="bg-white p-5 rounded-lg relative">
                <div
                    class="h-16 w-16 bg-gray-200 rounded-full items-center flex absolute justify-center -top-5 left-1/2 transform -translate-x-1/2">
                    <svg class="text-blue-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 20 20">
                        <path fill="currentColor"
                            d="M1 10c.41.29.96.43 1.5.43c.55 0 1.09-.14 1.5-.43c.62-.46 1-1.17 1-2c0 .83.37 1.54 1 2c.41.29.96.43 1.5.43c.55 0 1.09-.14 1.5-.43c.62-.46 1-1.17 1-2c0 .83.37 1.54 1 2c.41.29.96.43 1.51.43c.54 0 1.08-.14 1.49-.43c.62-.46 1-1.17 1-2c0 .83.37 1.54 1 2c.41.29.96.43 1.5.43c.55 0 1.09-.14 1.5-.43c.63-.46 1-1.17 1-2V7l-3-7H4L0 7v1c0 .83.37 1.54 1 2m2 8.99h5v-5h4v5h5v-7c-.37-.05-.72-.22-1-.43c-.63-.45-1-.73-1-1.56c0 .83-.38 1.11-1 1.56c-.41.3-.95.43-1.49.44c-.55 0-1.1-.14-1.51-.44c-.63-.45-1-.73-1-1.56c0 .83-.38 1.11-1 1.56c-.41.3-.95.43-1.5.44c-.54 0-1.09-.14-1.5-.44c-.63-.45-1-.73-1-1.57c0 .84-.38 1.12-1 1.57c-.29.21-.63.38-1 .44z" />
                    </svg>
                </div>

                <div class="flex flex-col items-center space-y-3">
                    <h1 class="text-center">Food Packaging and storage</h1>
                    <p>We have a robust framework and equipment for packaging food and it's effective storage.</p>
                    <button class="bg-primary font-bold text-white w-full rounded-full p-2"><a
                            href="{{ route('shipping_page') }}">Get Started</a></button>
                </div>
            </div>

        </div>

    </div>

    <!-- About Start -->
    <div class="container-fluid py-2">
        <!-- Video Modal -->
        <div class="modal fade" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <!-- 16:9 aspect ratio -->
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="" id="video"
                                allowscriptaccess="always" allow="autoplay"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->


    <!-- Pricing Plan Start -->
    <div class="container-fluid pt-2">
        <div class="container">
            <div class="text-center pb-2">
                <h6 class="text-primary text-uppercase font-weight-bold">{{ __('hompage.52') }}</h6>
                <h1 class="mb-4">{{ __('hompage.53') }}</h1>
            </div>
            <div class="row">

                <div class="col-md-4 mb-5">
                    <div class="card border-0 shadow">
                        <div class="card-body">
                            <h2 class="card-title text-center mb-0">
                                <small class="text-muted font-weight-medium"
                                    style="font-size: 22px; line-height: 45px;">&euro;</small>8<small
                                    class="text-muted font-weight-medium"
                                    style="font-size: 16px; line-height: 40px;">/Kg</small>
                            </h2>
                        </div>
                        <div class="card-header bg-primary text-center">
                            <h3 class="m-0 text-white">{{ __('hompage.54') }}</h3>
                        </div>
                        <div class="card-body text-center">
                            <ul class="list-unstyled">
                                {!! trans('hompage.57') !!}
                            </ul>
                            <a href="#" class="btn btn-primary btn-block py-2">Order Now</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-5">
                    <div class="card border-0 shadow">
                        <div class="card-body">
                            <h2 class="card-title text-center mb-0">
                                <small class="text-muted font-weight-medium"
                                    style="font-size: 22px; line-height: 45px;">&euro;</small>13<small
                                    class="text-muted font-weight-medium"
                                    style="font-size: 16px; line-height: 40px;">/Kg</small>
                            </h2>
                        </div>
                        <div class="card-header bg-primary text-center">
                            <h3 class="m-0 text-white">{{ __('hompage.58') }}</h3>
                        </div>
                        <div class="card-body text-center">
                            <ul class="list-unstyled">
                                {!! trans('hompage.56') !!}
                            </ul>
                            <a href="#" class="btn btn-primary btn-block py-2">Order Now</a>
                        </div>
                    </div>
                </div>


                <div class="col-md-4 mb-5">
                    <div class="card border-0 shadow">
                        <div class="card-body">
                            <h2 class="card-title text-center mb-0">
                                <small class="text-muted font-weight-medium"
                                    style="font-size: 22px; line-height: 45px;">&euro;</small>30<small
                                    class="text-muted font-weight-medium"
                                    style="font-size: 16px; line-height: 40px;">/Kg</small>
                            </h2>
                        </div>
                        <div class="card-header bg-primary text-center">
                            <h3 class="m-0 text-white">{{ __('hompage.59') }}</h3>
                        </div>
                        <div class="card-body text-center">
                            <ul class="list-unstyled">
                                {!! trans('hompage.57') !!}
                            </ul>
                            <a href="#" class="btn btn-primary btn-block py-2">Order Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pricing Plan End -->

    @include('findus')

    <!-- Quote Request Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="text-center pb-2">
                <h6 class="text-primary text-uppercase font-weight-bold">{{ __('hompage.62') }}</h6>
                <h1 class="mb-4">{{ __('hompage.63') }}</h1>
            </div>
            <div class="marquee">
                <div class="marquee">
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset('landing2/img/c0.jpeg') }}" alt="" style="width: 200px">
                    </div>
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset('landing2/img/c1.png') }}" alt="" style="width: 200px">
                    </div>
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset('landing2/img/c2.png') }}" alt="" style="width: 200px">
                    </div>
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset('landing2/img/c3.png') }}" alt="" style="width: 200px">
                    </div>
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset('landing2/img/c4.png') }}" alt="" style="width: 200px">
                    </div>
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset('landing2/img/c5.png') }}" alt="" style="width: 200px">
                    </div>
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset('landing2/img/c6.png') }}" alt="" style="width: 200px">
                    </div>
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset('landing2/img/c7.png') }}" alt="" style="width: 200px">
                    </div>
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset('landing2/img/c8.png') }}" alt="" style="width: 200px">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .product-item {
            text-align: center;
            padding: 15px;
            transform: scale(0.7);
            /* Scale down each item to 70% of its original size */
        }

        .product-img {
            border-radius: 50%;
            /* Make the image circular */
            width: 150px;
            height: 200px;
            object-fit: cover;
            /* Ensure the image covers the entire area */
            margin: 0 auto;
            /* Center the image horizontally */
        }

        .product-name {
            font-weight: bold;
            margin-top: 10px;
        }

        .product-description {
            font-size: 14px;
            color: #6c757d;
        }
    </style>
    <div class="container-fluid py-5">
        <div class="container">
            <div class="text-center pb-2">
                <h6 class="text-primary text-uppercase font-weight-bold">Our Products</h6>
                <h1 class="mb-4">Global Trade Product Showcase</h1>
            </div>
            <div class="owl-carousel owl-theme">
                @foreach ($products as $product)
                    <div class="product-item">
                        <img src="{{ asset('images/' . $product->image) }}" class="product-img"
                            alt="{{ $product->name }}">
                        <div class="product-name">{{ $product->name }}</div>
                        <div class="product-description">{{ Str::limit($product->description, 100) }}</div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('showcase') }}" class="btn btn-primary">See More Products/ Request Quote</a>
            </div>
        </div>
    </div>

    <!-- Footer Start -->
    @include('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"
        integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/blitzer/jquery-ui.min.css"
        integrity="sha512-ibBo2Ns078qm7xZNTPbIrg5XP4pZ+Aujfmz0QFsce2f4LYpCnF1Esy6FkIRFBgXC9cY30XiS7Ui9+RpN8K7ICg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>

    <script>
        $(document).ready(function() {

            $('#ship_from_country').select2();
            $('#ship_from_city').select2();
            $('#ship_to_country').select2();
            $('#ship_to_city').select2();

            countries();

            var ship_from_country = $("#ship_from_country");
            ship_from_country.wrap('<div class="position-relative"></div>');
            ship_from_country.on('change', function() {
                $("#ship_from_city").empty()
                $('#ship_from_city').html('<option value="">Select City</option>');

                var _token = '{{ csrf_token() }}';
                let url =
                    "{{ route('ajax-get-country-cities', ['countryId' => ':countryId']) }}"
                    .replace(':countryId', $(this).val());
                if ($(this).val() > 0) {
                    // showBlockUI();
                    $.ajax({
                        url: url,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'stateId': $(this).val(),
                            '_token': _token
                        },
                        success: function(response) {
                            if (response.success) {
                                $.each(response.cities, function(key, value) {
                                    $("#ship_from_city").append('<option value="' +
                                        value
                                        .id + '">' + value.name + '</option>');
                                });

                                $("#ship_from_city").trigger('change');
                                // hideBlockUI();

                            } else {
                                // hideBlockUI();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                    title: 'Are You Sure',
                                });
                            }
                        },
                        error: function(error) {
                            console.log(error);
                            // hideBlockUI();
                        }
                    });
                } else {
                    // hideBlockUI();
                }
                // hideBlockUI();

            });

            var ship_to_country = $("#ship_to_country");
            ship_to_country.wrap('<div class="position-relative"></div>');
            ship_to_country.on('change', function() {
                $("#ship_to_city").empty()
                $('#ship_to_city').html('<option value="">Select City</option>');

                var _token = '{{ csrf_token() }}';
                let url =
                    "{{ route('ajax-get-country-cities', ['countryId' => ':countryId']) }}"
                    .replace(':countryId', $(this).val());
                if ($(this).val() > 0) {

                    $.ajax({
                        url: url,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'stateId': $(this).val(),
                            '_token': _token
                        },
                        success: function(response) {
                            if (response.success) {
                                $.each(response.cities, function(key, value) {
                                    $("#ship_to_city").append('<option value="' +
                                        value
                                        .id + '">' + value.name + '</option>');
                                });
                                $("#ship_to_city").trigger('change');


                            } else {

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                    title: 'Are You Sure',
                                });
                            }
                        },
                        error: function(error) {
                            console.log(error);

                        }
                    });
                } else {

                }


            });
        })

        function countries() {

            $('#ship_from_country').html('<option value="">Select Country</option>');
            $('#ship_to_country').html('<option value="">Select Country</option>');
            $('#customer_country_id').html('<option value="">Select Country</option>');
            var _token = '{{ csrf_token() }}';
            let url = "{{ route('ajax-get-countries') }}";
            $.ajax({
                url: url,
                type: 'get',
                dataType: 'json',
                data: {
                    '_token': _token
                },
                success: function(response) {
                    if (response.success) {
                        $.each(response.countries, function(key, value) {
                            $("#ship_from_country").append('<option value="' + value.id +
                                '">' + value.name + '</option>');
                            $("#ship_to_country").append('<option value="' + value.id +
                                '">' + value.name + '</option>');
                            $("#customer_country_id").append('<option value="' + value.id + '">' + value
                                .name + '</option>')
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                        });
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function getRates() {

            let ship_from_country = $('#ship_from_country').val();
            let ship_from_city = $('#ship_from_city').val();
            let ship_to_country = $('#ship_to_country').val();
            let ship_to_city = $('#ship_to_city').val();
            let weightTotal = $('#weight_tot').val();



            $.ajax({
                url: "{{ route('rates.fetch') }}",
                type: "GET",
                data: {
                    ship_from_country: ship_from_country,
                    ship_from_city: ship_from_city,
                    ship_to_country: ship_to_country,
                    ship_to_city: ship_to_city,
                    weightTotal: weightTotal,
                    heightTotal: 0,
                    widthTotal: 0,
                    lengthTotal: 0,
                    valueTotal: 0,
                    countTotal: 0
                },
                success: function(data) {
                    if (data.success) {
                        console.log(data);
                        let result = data.data;
                        $('#shipping_rate_list').html(result.shipping_cost + " €");

                    } else {
                        toastr.error(data.message);
                    }
                },
                error: function(data) {
                    toastr.error('Something went wrong');
                }
            });



        }
    </script>
    @if (!empty($_GET))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Check if the element with id "myTabContent" exists
                var element = document.getElementById("myTabContent");
                if (element) {
                    // Scroll to the element
                    element.scrollIntoView({
                        behavior: "smooth"
                    });
                }
            });
        </script>
    @endif
    <script>
        $(document).ready(function() {
            $(".owl-carousel").owlCarousel({
                loop: true,
                margin: 10,
                nav: true,
                autoplay: true,
                autoplayTimeout: 3000,
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 3
                    },
                    1000: {
                        items: 4
                    }
                }
            });
        });
    </script>

    <script>
        $('#origin').autocomplete({
            source: "{{ route('locations.search') }}",
            minLength: 1,
            select: function(event, ui) {
                // Update the hidden input field with the selected value
                $('#origin_id').val(ui.item.value);

                // Update the visible input field with the label
                $('#origin').val(ui.item.label);

                // Prevent the default behavior of filling the input with the label
                event.preventDefault();
            }
        });

        $('#dest').autocomplete({
            source: "{{ route('locations.search') }}",
            minLength: 1,
            select: function(event, ui) {
                // Update the hidden input field with the selected value
                $('#dest_id').val(ui.item.value);

                // Update the visible input field with the label
                $('#dest').val(ui.item.label);

                // Prevent the default behavior of filling the input with the label
                event.preventDefault();
            }
        });

        function addRow() {
            let items_list = $('#items_list');
            let mar = `
            <tr>
                <td>
                    <select name="type[]" class="form-control type" onchange="calculateTotItems('type')" required>
                        <option value="">--{{ __('hompage.10') }}--</option>
                        <option value="percel">{{ __('hompage.11 ') }}</option>
                        <option value="doc">{{ __('hompage.12') }}</option>
                        <option value="pallet">{{ __('hompage.13') }}</option>
                        <option value="">--{{ __('hompage.10') }}--</option>
                        <option value="percel">{{ __('hompage.11 ') }}</option>
                        <option value="doc">{{ __('hompage.12') }}</option>
                        <option value="pallet">{{ __('hompage.13') }}</option>
                    </select>
                </td>
                <td>
                    <input type="number" step="any" min="0" value="0" class="form-control len" onkeyup="calculateTot()" name="len[]">
                </td>
                <td>
                    <input type="number" step="any" min="0" value="0" class="form-control width" onkeyup="calculateTot()" name="width[]">
                </td>
                <td>
                    <input type="number" step="any" min="0" value="0" class="form-control height" onkeyup="calculateTot()" name="height[]">
                </td>
                <td>
                    <input type="number" step="any" min="0" value="0" class="form-control weight" onkeyup="calculateTot()" name="weight[]">
                </td>
                <td>
                    <input type="number" step="1" min="1" value="1" class="form-control count" onkeyup="calculateTot()" name="count[]">
                </td>
                <td>
                    <button class="btn btn-danger" type="button" onclick="removeRow(this)"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
        `;

            items_list.append(mar);
        }

        function removeRow(obj) {
            $(obj).closest('tr').remove();
        }

        function calculateTot() {
            let the_classes = ['len', 'width', 'height', 'weight', 'count']
            for (let i = 0; i < the_classes.length; i++) {
                let total = 0;
                $('.' + the_classes[i]).each(function(o, e) {
                    if ($(this).val() != '') {
                        let c = (!$(this).hasClass('count')) ? $(this).parent().parent().find('.count').val() :
                            1; //find the count/qty on that row except for the count col
                        total = total + (parseFloat($(this).val()) * parseFloat(c));
                    }
                })
                $('#' + the_classes[i] + '-tot').text(total);
                $('#' + the_classes[i] + '_tot').val(total)
            }
        }

        function calculateTotItems(item_class) {
            let total = 0;
            $('.' + item_class).each(function(i, e) {
                if ($(this).val() != '') {
                    total = total + 1
                }
            })
            $('#' + item_class + '-tot').text(total);
        }

        function getRates() {
            let origin = $('#origin_id').val();
            let dest = $('#dest_id').val();
            let width = $('#width_tot').val();
            let height = $('#height_tot').val();
            let weight = $('#weight_tot').val();
            let length = $('#len_tot').val();
            let count = $('#count_tot').val();

            if (origin != '' && dest != '' && width != "" && height != "" && weight != "" && length != "" && count != "") {
                $.ajax({
                    url: "{{ route('rates.fetch') }}",
                    type: "GET",
                    data: {
                        origin: origin,
                        dest: dest,
                        width: width,
                        height: height,
                        weight: weight,
                        length: length,
                        count: count
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.length > 0) {
                            console.log(data);
                            let mar = '';
                            for (let j = 0; j < data.length; j++) {
                                mar += `
                                    <tr>

                                        <td>
                                            ${data[j].name}
                                        </td>
                                        <td>
                                            {{ __('hompage.66') }}: ${data[j].origin.name}
                                            {{ __('hompage.67') }}: ${data[j].destination.name}
                                            {{ __('hompage.66') }}: ${data[j].origin.name}
                                            {{ __('hompage.67') }}: ${data[j].destination.name}
                                        </td>
                                        <td>
                                            {{ __('hompage.68') }}: ${data[j].weight_start}
                                            {{ __('hompage.69') }}: ${data[j].weight_end}
                                            {{ __('hompage.68') }}: ${data[j].weight_start}
                                            {{ __('hompage.69') }}: ${data[j].weight_end}
                                        </td>
                                        <td>
                                            ${data[j].price}
                                        </td>
                                        <td>
                                            {{ __('hompage.5') }}: ${data[j].width}
                                            {{ __('hompage.6') }}: ${data[j].height}
                                            {{ __('hompage.4') }}: ${data[j].length}
                                            {{ __('hompage.5') }}: ${data[j].width}
                                            {{ __('hompage.6') }}: ${data[j].height}
                                            {{ __('hompage.4') }}: ${data[j].length}
                                        </td>
                                    </tr>

                                `;
                            }
                            $('#shipping_rate_list').html(mar);
                            //show second stage of form
                            $('.stage_2').show();
                        } else {
                            console.log('No results');
                            ($('#shipping_rate_list').html(
                                '<tr><td colspan="5">{{ __('hompage.70') }}</td></tr>'));
                            ($('#shipping_rate_list').html(
                                '<tr><td colspan="5">{{ __('hompage.70') }}</td></tr>'));
                            //hide second stage of form
                            $('.stage_2').hide();
                        }
                    }
                });
            } else {
                alert('Please fill out all fields accordingly in order to get rates ');
            }

        }
        const europeanTerritories = [
            // Recognized European Countries
            "Albania",
            "Andorra",
            "Austria",
            "Belarus",
            "Belgium",
            "Bosnia and Herzegovina",
            "Bulgaria",
            "Croatia",
            "Cyprus",
            "Czech Republic",
            "Denmark",
            "Estonia",
            "Faroe Islands",
            "Finland",
            "France",
            "Germany",
            "Gibraltar",
            "Greece",
            "Guernsey",
            "Hungary",
            "Iceland",
            "Ireland",
            "Isle of Man",
            "Italy",
            "Jersey",
            "Kosovo",
            "Latvia",
            "Liechtenstein",
            "Lithuania",
            "Luxembourg",
            "Malta",
            "Moldova",
            "Monaco",
            "Montenegro",
            "Netherlands",
            "North Macedonia",
            "Norway",
            "Poland",
            "Portugal",
            "Romania",
            "Russia",
            "San Marino",
            "Serbia",
            "Slovakia",
            "Slovenia",
            "Spain",
            "Svalbard and Jan Mayen",
            "Sweden",
            "Switzerland",
            "Ukraine",
            "United Kingdom",
            "Vatican City",

            // Dependent Territories and Special Regions
            "Aland Islands",
            "Akrotiri and Dhekelia",
            "Åland Islands",
            "Azores",
            "Balearic Islands",
            "Canary Islands",
            "Ceuta and Melilla",
            "Channel Islands",
            "Crimea",
            "Curaçao",
            "French Guiana",
            "Gagauzia",
            "Gottland",
            "Greenland",
            "Guadeloupe",
            "Madeira",
            "Man, Isle of",
            "Nagorno-Karabakh",
            "Northern Cyprus",
            "Republika Srpska",
            "Sark",
            "Sealand",
            "Transnistria",
        ];

        let countries = [
            "Afghanistan",
            "Albania",
            "Algeria",
            "Andorra",
            "Angola",
            "Antigua and Barbuda",
            "Argentina",
            "Armenia",
            "Australia",
            "Austria",
            "Azerbaijan",
            "Bahamas",
            "Bahrain",
            "Bangladesh",
            "Barbados",
            "Belarus",
            "Belgium",
            "Belize",
            "Benin",
            "Bhutan",
            "Bolivia",
            "Bosnia and Herzegovina",
            "Botswana",
            "Brazil",
            "Brunei",
            "Bulgaria",
            "Burkina Faso",
            "Burundi",
            "Cabo Verde",
            "Cambodia",
            "Cameroon",
            "Canada",
            "Central African Republic",
            "Chad",
            "Chile",
            "China",
            "Colombia",
            "Comoros",
            "Congo (Congo-Brazzaville)",
            "Costa Rica",
            "Croatia",
            "Cuba",
            "Cyprus",
            "Czechia (Czech Republic)",
            "Democratic Republic of the Congo (Congo-Kinshasa)",
            "Denmark",
            "Djibouti",
            "Dominica",
            "Dominican Republic",
            "Ecuador",
            "Egypt",
            "El Salvador",
            "Equatorial Guinea",
            "Eritrea",
            "Estonia",
            "Eswatini (fmr. Swaziland)",
            "Ethiopia",
            "Fiji",
            "Finland",
            "France",
            "Gabon",
            "Gambia",
            "Georgia",
            "Germany",
            "Ghana",
            "Greece",
            "Grenada",
            "Guatemala",
            "Guinea",
            "Guinea-Bissau",
            "Guyana",
            "Haiti",
            "Holy See",
            "Honduras",
            "Hungary",
            "Iceland",
            "India",
            "Indonesia",
            "Iran",
            "Iraq",
            "Ireland",
            "Israel",
            "Italy",
            "Ivory Coast",
            "Jamaica",
            "Japan",
            "Jordan",
            "Kazakhstan",
            "Kenya",
            "Kiribati",
            "Kuwait",
            "Kyrgyzstan",
            "Laos",
            "Latvia",
            "Lebanon",
            "Lesotho",
            "Liberia",
            "Libya",
            "Liechtenstein",
            "Lithuania",
            "Luxembourg",
            "Madagascar",
            "Malawi",
            "Malaysia",
            "Maldives",
            "Mali",
            "Malta",
            "Marshall Islands",
            "Mauritania",
            "Mauritius",
            "Mexico",
            "Micronesia",
            "Moldova",
            "Monaco",
            "Mongolia",
            "Montenegro",
            "Morocco",
            "Mozambique",
            "Myanmar (formerly Burma)",
            "Namibia",
            "Nauru",
            "Nepal",
            "Netherlands",
            "New Zealand",
            "Nicaragua",
            "Niger",
            "Nigeria",
            "North Korea",
            "North Macedonia (formerly Macedonia)",
            "Norway",
            "Oman",
            "Pakistan",
            "Palau",
            "Palestine State",
            "Panama",
            "Papua New Guinea",
            "Paraguay",
            "Peru",
            "Philippines",
            "Poland",
            "Portugal",
            "Qatar",
            "Romania",
            "Russia",
            "Rwanda",
            "Saint Kitts and Nevis",
            "Saint Lucia",
            "Saint Vincent and the Grenadines",
            "Samoa",
            "San Marino",
            "Sao Tome and Principe",
            "Saudi Arabia",
            "Senegal",
            "Serbia",
            "Seychelles",
            "Sierra Leone",
            "Singapore",
            "Slovakia",
            "Slovenia",
            "Solomon Islands",
            "Somalia",
            "South Africa",
            "South Korea",
            "South Sudan",
            "Spain",
            "Sri Lanka",
            "Sudan",
            "Suriname",
            "Sweden",
            "Switzerland",
            "Syria",
            "Tajikistan",
            "Tanzania",
            "Thailand",
            "Timor-Leste",
            "Togo",
            "Tonga",
            "Trinidad and Tobago",
            "Tunisia",
            "Turkey",
            "Turkmenistan",
            "Tuvalu",
            "Uganda",
            "Ukraine",
            "United Arab Emirates",
            "United Kingdom",
            "United States of America",
            "Uruguay",
            "Uzbekistan",
            "Vanuatu",
            "Venezuela",
            "Vietnam",
            "Yemen",
            "Zambia",
            "Zimbabwe",
        ];

        // Initialize autocomplete
        $("#s_country_eu").autocomplete({
            source: europeanTerritories,
            minLength: 1 // Minimum number of characters before triggering autocomplete
        });

        // Initialize autocomplete
        $("#rx_country_eu").autocomplete({
            source: europeanTerritories,
            minLength: 1 // Minimum number of characters before triggering autocomplete
        });

        // Initialize autocomplete
        $("#s_country").autocomplete({
            source: countries,
            minLength: 1 // Minimum number of characters before triggering autocomplete
        });

        // Initialize autocomplete
        $("#rx_country").autocomplete({
            source: countries,
            minLength: 1 // Minimum number of characters before triggering autocomplete
        });

        var currencies = [
            'AED - United Arab Emirates Dirham',
            'AFN - Afghan Afghani',
            'ALL - Albanian Lek',
            'AMD - Armenian Dram',
            'ANG - Netherlands Antillean Guilder',
            'AOA - Angolan Kwanza',
            'ARS - Argentine Peso',
            'AUD - Australian Dollar',
            'AWG - Aruban Florin',
            'AZN - Azerbaijani Manat',
            'BAM - Bosnia-Herzegovina Convertible Mark',
            'BBD - Barbadian Dollar',
            'BDT - Bangladeshi Taka',
            'BGN - Bulgarian Lev',
            'BHD - Bahraini Dinar',
            'BIF - Burundian Franc',
            'BMD - Bermudian Dollar',
            'BND - Brunei Dollar',
            'BOB - Bolivian Boliviano',
            'BRL - Brazilian Real',
            'BSD - Bahamian Dollar',
            'BTN - Bhutanese Ngultrum',
            'BWP - Botswanan Pula',
            'BYN - Belarusian Ruble',
            'BZD - Belize Dollar',
            'CAD - Canadian Dollar',
            'CDF - Congolese Franc',
            'CHF - Swiss Franc',
            'CLP - Chilean Peso',
            'CNY - Chinese Yuan',
            'COP - Colombian Peso',
            'CRC - Costa Rican Colón',
            'CUP - Cuban Peso',
            'CVE - Cape Verdean Escudo',
            'CZK - Czech Republic Koruna',
            'DJF - Djiboutian Franc',
            'DKK - Danish Krone',
            'DOP - Dominican Peso',
            'DZD - Algerian Dinar',
            'EGP - Egyptian Pound',
            'ERN - Eritrean Nakfa',
            'ETB - Ethiopian Birr',
            'EUR - Euro',
            'FJD - Fijian Dollar',
            'FKP - Falkland Islands Pound',
            'FKP - Falkland Islands Pound',
            'FJD - Fijian Dollar',
            'FKP - Falkland Islands Pound',
            'FJD - Fijian Dollar',
            'FKP - Falkland Islands Pound',
            'FJD - Fijian Dollar',
            'FKP - Falkland Islands Pound',
            'FJD - Fijian Dollar',
            'FKP - Falkland Islands Pound',
            'GEL - Georgian Lari',
            'GGP - Guernsey Pound',
            'GHS - Ghanaian Cedi',
            'GIP - Gibraltar Pound',
            'GMD - Gambian Dalasi',
            'GNF - Guinean Franc',
            'GTQ - Guatemalan Quetzal',
            'GYD - Guyanaese Dollar',
            'HKD - Hong Kong Dollar',
            'HNL - Honduran Lempira',
            'HRK - Croatian Kuna',
            'HTG - Haitian Gourde',
            'HUF - Hungarian Forint',
            'IDR - Indonesian Rupiah',
            'ILS - Israeli New Shekel',
            'IMP - Isle of Man Pound',
            'INR - Indian Rupee',
            'IQD - Iraqi Dinar',
            'IRR - Iranian Rial',
            'ISK - Icelandic Króna',
            'JEP - Jersey Pound',
            'JMD - Jamaican Dollar',
            'JOD - Jordanian Dinar',
            'JPY - Japanese Yen',
            'KES - Kenyan Shilling',
            'KGS - Kyrgystani Som',
            'KHR - Cambodian Riel',
            'KID - Kiribati Dollar',
            'KRW - South Korean Won',
            'KWD - Kuwaiti Dinar',
            'KYD - Cayman Islands Dollar',
            'KZT - Kazakhstani Tenge',
            'LAK - Laotian Kip',
            'LBP - Lebanese Pound',
            'LKR - Sri Lankan Rupee',
            'LRD - Liberian Dollar',
            'LSL - Lesotho Loti',
            'LYD - Libyan Dinar',
            'MAD - Moroccan Dirham',
            'MDL - Moldovan Leu',
            'MGA - Malagasy Ariary',
            'MKD - Macedonian Denar',
            'MMK - Myanma Kyat',
            'MNT - Mongolian Tugrik',
            'MOP - Macanese Pataca',
            'MRU - Mauritanian Ouguiya',
            'MUR - Mauritian Rupee',
            'MVR - Maldivian Rufiyaa',
            'MWK - Malawian Kwacha',
            'MXN - Mexican Peso',
            'MYR - Malaysian Ringgit',
            'MZN - Mozambican Metical',
            'NAD - Namibian Dollar',
            'NGN - Nigerian Naira',
            'NIO - Nicaraguan Córdoba',
            'NOK - Norwegian Krone',
            'NPR - Nepalese Rupee',
            'NZD - New Zealand Dollar',
            'OMR - Omani Rial',
            'PAB - Panamanian Balboa',
            'PEN - Peruvian Nuevo Sol',
            'PGK - Papua New Guinean Kina',
            'PHP - Philippine Peso',
            'PKR - Pakistani Rupee',
            'PLN - Polish Złoty',
            'PYG - Paraguayan Guarani',
            'QAR - Qatari Rial',
            'RON - Romanian Leu',
            'RSD - Serbian Dinar',
            'RUB - Russian Ruble',
            'RWF - Rwandan Franc',
            'SAR - Saudi Riyal',
            'SBD - Solomon Islands Dollar',
            'SCR - Seychellois Rupee',
            'SDG - Sudanese Pound',
            'SEK - Swedish Krona',
            'SGD - Singapore Dollar',
            'SHP - Saint Helena Pound',
            'SLL - Sierra Leonean Leone',
            'SOS - Somali Shilling',
            'SRD - Surinamese Dollar',
            'SSP - South Sudanese Pound',
            'STN - São Tomé and Príncipe Dobra',
            'SVC - Salvadoran Colón',
            'SYP - Syrian Pound',
            'SZL - Swazi Lilangeni',
            'THB - Thai Baht',
            'TJS - Tajikistani Somoni',
            'TMT - Turkmenistani Manat',
            'TND - Tunisian Dinar',
            'TOP - Tongan Pa\'anga',
            'TRY - Turkish Lira',
            'TTD - Trinidad and Tobago Dollar',
            'TWD - New Taiwan Dollar',
            'TZS - Tanzanian Shilling',
            'UAH - Ukrainian Hryvnia',
            'UGX - Ugandan Shilling',
            'USD - United States Dollar',
            'UYU - Uruguayan Peso',
            'UZS - Uzbekistan Som',
            'VES - Venezuelan Bolívar',
            'VND - Vietnamese Đồng',
            'VUV - Vanuatu Vatu',
            'WST - Samoan Tala',
            'XAF - Central African CFA Franc',
            'XCD - East Caribbean Dollar',
            'XOF - West African CFA Franc',
            'XPF - CFP Franc',
            'YER - Yemeni Rial',
            'ZAR - South African Rand',
            'ZMW - Zambian Kwacha',
            'ZWL - Zimbabwean Dollar',
        ];
        // Initialize autocomplete
        $("#rx_currency").autocomplete({
            source: currencies,
            minLength: 1 // Minimum number of characters before triggering autocomplete
        });
        // Initialize autocomplete
        $("#s_currency").autocomplete({
            source: currencies,
            minLength: 1 // Minimum number of characters before triggering autocomplete
        });
    </script>
    <style>
        .marquee-container {
            overflow: hidden;
        }

        .marquee {
            display: flex;
            animation: marquee-animation 20s linear infinite;
        }

        @keyframes marquee-animation {
            0% {
                transform: translateX(0%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        /* Keyframes for the typing animation */
        @keyframes typing {
            from {
                width: 0
            }

            to {
                width: 100%
            }
        }

        /* Apply animation to the text */
        .typing-animation {
            overflow: hidden;
            border-right: .15em solid orange;
            /* Change border properties according to your need */
            white-space: nowrap;
            animation: typing 3s steps(40, end), blinkCursor 0.5s step-end infinite alternate;
        }

        /* Define a separate keyframe animation to blink cursor */
        @keyframes blinkCursor {

            from,
            to {
                border-color: transparent
            }

            50% {
                border-color: orange;
            }
        }

        .inp-class {
            color: white;
        }

        .inp-class:focus {
            border: none;
        }
    </style>
    <style>
        .marquee-container {
            overflow: hidden;
        }

        .marquee {
            display: flex;
            animation: marquee-animation 20s linear infinite;
        }

        @keyframes marquee-animation {
            0% {
                transform: translateX(0%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        /* Keyframes for the typing animation */
        @keyframes typing {
            from {
                width: 0
            }

            to {
                width: 100%
            }
        }

        /* Apply animation to the text */
        .typing-animation {
            overflow: hidden;
            border-right: .15em solid orange;
            /* Change border properties according to your need */
            white-space: nowrap;
            animation: typing 3s steps(40, end), blinkCursor 0.5s step-end infinite alternate;
        }

        /* Define a separate keyframe animation to blink cursor */
        @keyframes blinkCursor {

            from,
            to {
                border-color: transparent
            }

            50% {
                border-color: orange;
            }
        }

        .inp-class {
            color: white;
        }

        .inp-class:focus {
            border: none;
        }
    </style>
</body>

</html>
