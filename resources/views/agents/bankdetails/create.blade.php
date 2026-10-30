@extends('admin.app')
@section('page_title', 'Create Bank Details')
@section('content')
    <div class="container-fluid">
        {{-- <div class="card"> --}}
        {{-- <div class="card-body"> --}}
        {{-- <h5 class="card-title fw-semibold mb-4">Settings</h5> --}}
        {{-- @include('dispatcher.settings.nav') --}}
        {{-- <hr> --}}
        <div class="card">
            {{--  @if (session::has('success'))
                <div class="alert alert-success" class="">{{ session::get('success') }}</div>
            @endif
            @if (session::has('error'))
                <div class="alert alert-success" class="">{{ session::get('error') }}</div>
            @endif  --}}
            <div class="card-header">
                <h5>{{ __('admin.bank_details.form_title') }}</h5>
            </div>
            <div class="card-body">
                {{--  @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <li class="text-danger">{{ $error }}</li>
                    @endforeach

                @endif
                @include('admin.partials.notification')  --}}

                <form method="POST" action="{{ route('bank_details.store') }}">
                    @method('POST')
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-12 col-lg-6">
                            <label for="bank_name">Name of Bank</label>
                            <input type="text" class="form-control" name="bank_name" id="bank_name" value="{{ old('bank_name') }}">
                            @error('bank_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 col-lg-6">
                            <label for="iban">Iban No</label>
                            <input type="text" class="form-control" name="iban" id="iban" value="{{ old('iban') }}">
                            @error('iban')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group col-md-12 col-lg-6">
                            <label for="account_name">Account Name</label>
                            <input type="text" class="form-control" name="account_name" id="account_name" value="{{ old('account_name') }}">
                            @error('account_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 col-lg-6">
                            <label for="acount_number">Account Number</label>
                            <input type="text" class="form-control" name="acount_number" id="acount_number" value="{{ old('acount_number') }}">
                            @error('acount_number')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group col-md-12 col-lg-6">
                            <label for="bank_code">Bank Code</label>
                            <input type="text" class="form-control" name="bank_code" id="bank_code" value="{{ old('bank_code') }}">
                            @error('bank_code')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 col-lg-6">
                            <label for="routing_number">Routing Number</label>
                            <input type="text" class="form-control" name="routing_number" id="routing_number" value="{{ old('routing_number') }}">
                            @error('routing_number')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <label for="country_id">Shipping from Country<i class="text-danger">*</i>:</label>
                            <select name="country_id" id="country_id" class="form-control">
                            </select>
                            @error('country_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-12 col-lg-6">
                            <label for="city_id">Shipping from City<i class="text-danger">*</i>:</label>
                            <select name="city_id" id="city_id" class="form-control">
                            </select>
                            @error('city_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <br>

                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> 
                        {{ __('admin.bank_details.submit_button') }}
                    </button>
                </form>

            </div>
        </div>
    </div>
    {{-- </div> --}}
    {{-- </div> --}}
@endsection
@section('scripts')
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


        $(document).ready(function() {
            $('#country_id').select2();
            $('#city_id').select2();

            countries();

            var country_id = $("#country_id");
            country_id.wrap('<div class="position-relative"></div>');
            country_id.on('change', function() {
                $("#city_id").empty()
                $('#city_id').html('<option value="">Select City</option>');

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
                                    $("#city_id").append('<option value="' +
                                        value
                                        .id + '">' + value.name + '</option>');
                                });
                                $("#city_id").trigger('change');
                                // hideBlockUI();
                                @if (!is_null(old('residential.city')))
                                    $('#city_id').val({{ old('residential.city') }});
                                    $('#city_id').trigger('change')
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

            $('#country_id').html('<option value="">Select Country</option>');
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
                            $("#country_id").append('<option value="' + value.id +
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
