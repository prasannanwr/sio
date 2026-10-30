@extends('admin.app')
@section('page_title', 'Edit Dispatcher')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Edit Dispatcher</h5>
                @include('admin.partials.notification')
                <hr>
                <form action="{{ route('dispatchers.update', $dispatcher) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="form-group">
                            <label for="name">Name/ Business Name</label>
                            <input type="text" name="name" class="form-control" id="name"
                                value="{{ $dispatcher->name }}" required>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" class="form-control" id="phone"
                                value="{{ $dispatcher->phone }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="phone_alt">Phone Alt</label>
                            <input type="text" name="phone_alt" class="form-control" id="phone_alt"
                                value="{{ $dispatcher->phone_alt }}">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="agency-type" class="col-md-4 col-form-label">{{ __('Agency Type') }} <i
                                    class="text-danger">*</i></label>
                            <select name="agency_type" id="agency_type" class="form-control">
                                <option value="person" {{ $dispatcher->agency_type == 'person' ? 'selected' : '' }}>
                                    Personal</option>
                                <option value="agency" {{ $dispatcher->agency_type == 'agency' ? 'selected' : '' }}>
                                    Agency</option>
                                <option value="public_admin"
                                    {{ $dispatcher->agency_type == 'public_admin' ? 'selected' : '' }}>Public
                                    Adminiatration</option>
                            </select>
                            @error('agency_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                        <div class="col-md-6">
                            <label for="business-name" class="col-md-4 col-form-label">Business Name</label>
                            <input id="business-name" type="text" class="form-control"
                                value="{{ $dispatcher->business_name }}" name="business_name">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="tax-id-code" class="col-md-4 col-form-label">Tax ID Code <i
                                    class="text-danger">*</i></label>
                            <input id="tax-id-code" type="text" class="form-control" name="tax_id_code"
                                value="{{ $dispatcher->tax_id_code }}" required>
                            @error('tax_id_code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="vat-no" class="col-md-4 col-form-label">VAT Number</label>
                            <input id="vat-no" type="text" class="form-control" value="{{ $dispatcher->vat_no }}"
                                name="vat_no">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="pec" class="col-md-4 col-form-label">PEC</label>
                            <input id="pec" type="text" class="form-control" value="{{ $dispatcher->pec }}"
                                name="pec">
                        </div>
                        <div class="col-md-6">
                            <label for="sdi" class="col-md-4 col-form-label">Unique SDI Code</label>
                            <input id="sdi" type="text" class="form-control" value="{{ $dispatcher->sdi }}"
                                name="sdi">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group">
                            <label for="address1">Address 1</label>
                            <textarea name="address1" class="form-control" id="address1">{{ $dispatcher->address2 }}</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group">
                            <label for="address2">Address 2(additional decriptions)</label>
                            <textarea name="address2" class="form-control" id="address2">{{ $dispatcher->address1 }}</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="zip">zip</label>
                            <input type="text" name="zip" class="form-control" id="zip"
                                value="{{ $dispatcher->zip }}" required>
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

                                $("#residential_state").trigger('change');

                                @if (!is_null($dispatcher->state_id))
                                    $('#residential_state').val({{ $dispatcher->state_id }});
                                    $('#residential_state').trigger('change')
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
                            'countryId': $(this).val(),
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
                                @if (!is_null($dispatcher->city_id))
                                    $('#residential_city').val({{ $dispatcher->city_id }});
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
                        $('#residential_country').trigger('change');
                        @if (!is_null($dispatcher->country_id))
                            $('#residential_country').val({{ $dispatcher->country_id }});
                            $('#residential_country').trigger('change')
                        @endif
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

            // @if (!is_null($dispatcher->country_id))
            //      let country = "{{ $dispatcher->country_id }}";
            //     setTimeout(() => {          
            //         $('#residential_country').val({{ $dispatcher->country_id }});
            //         $('#residential_country').trigger('change')
            //     }, 200);
            // @endif

        }
    </script>
@endsection
@endsection
