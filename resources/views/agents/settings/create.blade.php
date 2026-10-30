@extends('admin.app')
@section('page_title', 'Create Dispatcher')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Create Dispatcher</h5>
                {{-- @include('dispatcher.settings.nav') --}}
                @include('admin.partials.notification')
                <hr>
             
                <form action="{{ route('dispatchers.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="row ">
                        <div class="form-group">
                            <label for="name">Name/ Business Name</label>
                            <input type="text" name="name" class="form-control" id="name" value=""
                                required>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="email">{{ __('Email Address') }} <i class="text-danger">*</i></label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" autocomplete="email">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
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
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" class="form-control" id="phone" value=""
                                required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="phone_alt">Phone Alt</label>
                            <input type="text" name="phone_alt" class="form-control" id="phone_alt" value="">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="agency-type" class="col-md-4 col-form-label">Agency Type<i
                                    class="text-danger">*</i></label>
                            <select name="agency_type" id="agency_type" class="form-control">
                                <option value="person" selected>Personal</option>
                                <option value="agency">Agency</option>
                                <option value="public_admin">Public Adminiatration</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="business-name" class="col-md-4 col-form-label">Business Name</label>
                            <input id="business-name" type="text" class="form-control" value=""
                                name="business_name">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="tax-id-code" class="col-md-4 col-form-label">Tax ID Code <i
                                    class="text-danger">*</i></label>
                            <input id="tax-id-code" type="text" class="form-control" name="tax_id_code" value=""
                                required>

                        </div>
                        <div class="col-md-6">
                            <label for="vat-no" class="col-md-4 col-form-label">VAT Number</label>
                            <input id="vat-no" type="text" class="form-control" value="" name="vat_no"
                                required>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="pec" class="col-md-4 col-form-label">PEC</label>
                            <input id="pec" type="text" class="form-control" value="" name="pec"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="sdi" class="col-md-4 col-form-label">Unique SDI Code</label>
                            <input id="sdi" type="text" class="form-control" value="" name="sdi"
                                required>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group">
                            <label for="address1">Address 1</label>
                            <textarea name="address1" class="form-control" id="address1" required></textarea>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group">
                            <label for="address2">Address 2(additional decriptions)</label>
                            <textarea name="address2" class="form-control" id="address2"></textarea>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="zip">zip</label>
                            <input type="text" name="zip" class="form-control" id="zip" value=""
                                required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="residential_country">Country<i class="text-danger">*</i></label>
                            <select class="form-control" id="residential_country" name="residential_country" required>
                                <option value="" selected>Select Country</option>
                            </select>
                            <small class="text-muted">Select Country</small>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="residential_state">State<i class="text-danger">*</i></label>
                            <select class="select2 form-control" id="residential_state" name="residential_state"
                                required>
                            </select>
                            <small class="text-muted">Select State</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="residential_city">City<i class="text-danger">*</i></label>
                            <select class="form-control" id="residential_city" name="residential_city">
                            </select>
                            <small class="text-muted">Select City</small>
                        </div>
                    </div>
                    <br>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save</button>
                </form>
            </div>
        </div>
    </div>
@section('scripts')
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

                                residential_state.trigger('change');

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


            var residential_state = $("#residential_state");
            residential_state.wrap('<div class="position-relative"></div>');
            residential_state.on('change', function() {
                $("#residential_city").empty()
                $('#residential_city').html('<option value="">Select City</option>');

                var _token = '{{ csrf_token() }}';
                let url =
                    "{{ route('ajax-get-cities', ['stateId' => ':stateId']) }}"
                    .replace(':stateId', $(this).val());
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
                                residential_city.trigger('change');
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
@endsection
@endsection
