<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    {{-- <title>{{ env('APP_NAME') }} | @yield('page_title')</title> --}}
    <title>Siopay Logistics | Sign Up</title>

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
        .headi {
            background-image: linear-gradient(to right, white 30%, transparent), url('landing/assets/img/gallery/login.png');
            background-size: cover;
            background-repeat: no-repeat;
        }

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
    <script src="https://cdn.tailwindcss.com"></script>
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

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

</head>

<body>
    <div class="">
        <div class="headi flex items-center h-auto md:h-screen p-3 bg-contain md:bg-cover">

            <div class="w-full lg:w-1/2 flex flex-col items-center justify-center">
                <form action="{{ route('register') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="lg:w-[500px] space-y-5 bg-white items-left rounded-xl p-3 shadow-xl">

                        <div class="flex flex-col items-center">
                            <h1 class="text-primary">Sign up</h1>
                            <p class="text-center">If you do not have an account, please register</p>
                        </div>

                        <div class="form-group">
                            <label for="email">{{ __('Username') }} <i class="text-danger">*</i></label>
                            <input id="name" type="text"
                                class="form-control @error('name') is-invalid @enderror" name="name"
                                value="{{ old('name') }}" autocomplete="name">

                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">{{ __('Email Address') }} <i class="text-danger">*</i></label>
                            <input id="email" type="email"
                                class="form-control @error('email') is-invalid @enderror" name="email"
                                value="{{ old('email') }}" autocomplete="email">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">{{ __('Password') }} <i class="text-danger">*</i></label>
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" name="password"
                                autocomplete="new-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                        <div class="form-group">
                            <label for="password-confirm">{{ __('Confirm Password') }}
                                <i class="text-danger">*</i></label>
                            <input id="password-confirm" type="password" class="form-control"
                                name="password_confirmation" autocomplete="new-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="agency-type">{{ __('Agency Type') }} <i class="text-danger">*</i></label>
                            <select name="agency_type" id="agency_type" class="form-control"
                                onchange="toggleBizName(this.value)">
                                <option value="person">Personal</option>
                                <option value="agency">Agency</option>
                                <option value="public_admin">Public Adminiatration</option>
                            </select>
                            @error('agency_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="business_name">{{ __('Business Name') }} <i class="text-danger">*</i></label>
                            <input id="business_name" type="text"
                                class="form-control @error('business_name') is-invalid @enderror" name="business_name"
                                value="{{ old('business_name') }}" autocomplete="name">

                            @error('business_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                       
                        <div class="form-group">
                            <label for="tax_id_code">{{ __('Tax ID Code') }} <i class="text-danger">*</i></label>
                            <input id="tax_id_code" type="text"
                                class="form-control @error('tax_id_code') is-invalid @enderror" name="tax_id_code"
                                value="{{ old('tax_id_code') }}" autocomplete="name">

                            @error('tax_id_code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tax_id_code">{{ __('VAT Number') }} <i class="text-danger">*</i></label>
                            <input id="vat_no" type="text"
                                class="form-control @error('vat_no') is-invalid @enderror" name="vat_no"
                                value="{{ old('vat_no') }}" autocomplete="name">

                            @error('vat_no')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tax_id_code">{{ __('PEC') }} <i class="text-danger">*</i></label>
                            <input id="pec" type="text"
                                class="form-control @error('pec') is-invalid @enderror" name="pec"
                                value="{{ old('pec') }}" autocomplete="name">

                            @error('pec')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>



                        <div class="form-group">
                            <label for="tax_id_code">{{ __('Unique SDI Code') }} <i class="text-danger">*</i></label>
                            <input id="sdi" type="text"
                                class="form-control @error('sdi') is-invalid @enderror" name="sdi"
                                value="{{ old('sdi') }}" autocomplete="name">

                            @error('sdi')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    

                      
                        <div class="form-group">
                            <label for="tax_id_code">{{ __('Phone') }} <i class="text-danger">*</i></label>
                            <input id="phone" type="text"
                                class="form-control @error('phone') is-invalid @enderror" name="phone"
                                value="{{ old('phone') }}" autocomplete="name">

                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                                           
                        <div class="form-group">
                            <label for="phone_alt">Phone Alt</label>
                            <input type="text" name="phone_alt" class="form-control" id="phone_alt"
                                value="">
                        </div>

                        <div class="form-group">
                            <label for="address1">{{ __('Address 1') }} <i class="text-danger">*</i></label>
                            <textarea name="address1" class="form-control  @error('phone') is-invalid @enderror" id="address1"></textarea>
                            @error('address1')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address2">Address 2(additional decriptions)</label>
                            <textarea name="address2" class="form-control" id="address2"></textarea>
                        </div>

                       

                        <div class="form-group">
                            <label for="zip">{{ __('Zip') }} <i class="text-danger">*</i></label>
                            <input type="text" name="zip" class="form-control @error('phone') is-invalid @enderror" id="zip"
                                value="">
                            @error('zip')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>



                        <div class="form-group ">
                            <label for="residential_country">{{ __('Country') }} <i class="text-danger">*</i></label>

                            <select class="form-control" id="residential_country" name="residential_country">
                                <option value="" selected>Select Country</option>

                            </select>
                            <small class="text-muted">Select Country</small>
                            @error('residential_country')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="">
                            <label for="residential_state">{{ __('State') }} <i class="text-danger">*</i></label>

                            <select class="select2 form-control" id="residential_state" name="residential_state">

                                @if (isset($states))

                                    @foreach ($states as $state)
                                        <option @if (isset($hiring_job) && $hiring_job->state_id == $state->id) selected @endif
                                            value="{{ $state->id }}">
                                            {{ $state->name }}</option>
                                    @endforeach
                                @endif

                            </select>
                            <small class="text-muted">Select State</small>
                            @error('residential_state')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="">
                            <label for="residential_city">{{ __('City') }} <i class="text-danger">*</i></label>

                            <select class="form-control" id="residential_city" name="residential_city">

                                @if (isset($cities))
                                    @foreach ($cities as $city)
                                        <option @if (isset($hiring_job) && $hiring_job->city_id == $city->id) selected @endif
                                            value="{{ $city->id }}">
                                            {{ $city->name }}</option>
                                    @endforeach
                                @endif

                            </select>
                            <small class="text-muted">Select City</small>
                            @error('residential_city')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="attachment">{{ __('Registration Document') }} <i class="text-danger">*</i></label>
                            <input type="file" class="form-control" name="front_attachment" accept="application/pdf">
                            @error('attachment')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="attachment">{{ __('Full Document') }} <i class="text-danger">*</i></label>
                            <input type="file" class="form-control" name="attachment" accept="application/pdf">
                            @error('attachment')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <hr>
                        <div class="w-full items-center flex flex-col">
                            <button type="submit" class="btn btn-primary btn-lg" id="registerButton">
                                {{ __('Register') }}
                            </button>
                            <p>Already have an account? <a class="btn btn-link" href="/login">Login</a></p>
                        </div>
                </form>
            </div>
        </div>

    </div>

    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#residential_country').select2();
            $('#residential_city').select2();
            $('#residential_state').select2();


            countries();

            var residential_country = $("#residential_country");
            residential_country.wrap('<div class="position-relative"></div>');
            residential_country.on('change', function() {
                $("#residential_city").empty();
                $('#residential_state').empty();
                $('#residential_state').html('<option value="">Select State</option>');
                $('#residential_city').html('<option value="">Select City</option>');
                var _token = '{{ csrf_token() }}';
                let url = "{{ route('ajax-get-states', ['countryId' => ':countryId']) }}".replace(
                    ':countryId', $(this).val());
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
                                $.each(response.states, function(key, value) {
                                    $("#residential_state").append(
                                        '<option value="' + value.id + '">' + value
                                        .name + '</option>'
                                    );
                                });
                                // hideBlockUI();

                                $('#residential_state').trigger('change');

                                @if (!is_null(old('residential.state')))
                                    $('#residential_state').val({{ old('residential.state') }})
                                        .trigger('change');
                                    $('#residential_state').trigger('change');
                                @endif
                            } else {
                                // hideBlockUI();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(error) {
                            console.log(error);
                            // hideBlockUI();
                        }
                    });
                }
            });


            var residential_country = $("#residential_country");
            residential_country.wrap('<div class="position-relative"></div>');
            residential_country.on('change', function() {
                $("#residential_city").empty()
                $('#residential_city').html('<option value="">Select City</option>');

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
                                    $("#residential_city").append('<option value="' +
                                        value
                                        .id + '">' + value.name + '</option>');
                                });
                                $('#residential_city').trigger('change');
                                // hideBlockUI();
                                @if (!is_null(old('residential.city')))
                                    $('#residential_city').val({{ old('residential.city') }});
                                    $('#residential_city').trigger('change')
                                @endif
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
        })


        function countries() {

            $('#residential_country').html('<option value="">Select Country</option>');
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
                            $("#residential_country").append('<option value="' + value.id +
                                '">' + value.name + '</option>');
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
    </script>

</body>


</html>
