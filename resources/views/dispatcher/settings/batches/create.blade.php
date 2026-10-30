@extends('admin.app')
@section('page_title', 'Create Batch')
@section('content')
    <div class="container-fluid">
        {{-- <div class="card"> --}}
        {{-- <div class="card-body"> --}}
        {{-- <h5 class="card-title fw-semibold mb-4">Settings</h5> --}}
        {{-- @include('dispatcher.settings.nav') --}}
        {{-- <hr> --}}
        <div class="card">
            <div class="card-header">
                <h5> {{ __('admin.batch.form_title') }} </h5>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                {{--  @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <li class="text-danger">{{ $error }}</li>
                    @endforeach

                @endif
                @include('admin.partials.notification')  --}}

                <form action="{{ route('batches.store') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-12 col-lg-6">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" id="name">
                            @error('name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-12 col-lg-6">
                            {{--  {{ $order->tracking_id }}  --}}
                            <label for="origin">Select Orders<i class="text-danger">*</i> : </label>
                            <select name="order_id[]" id="orders" class="form-control" multiple>
                            </select>
                            @error('order_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <label for="origin">Shipping from Country<i class="text-danger">*</i> : </label>
                            <select name="ship_from_country_id" id="ship_from_country" class="form-control">

                            </select>
                            @error('ship_from_country_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-12 col-lg-6 ">
                            <label for="origin">Shipping from City<i class="text-danger">*</i> : </label>
                            <select name="ship_from_city_id" id="ship_from_city" class="form-control">
                            </select>
                            @error('ship_from_city_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <label for="origin">Shipping to Country<i class="text-danger">*</i> : </label>
                            <select name="ship_to_country_id" id="ship_to_country" class="form-control">

                            </select>
                            @error('ship_to_country_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-12 col-lg-6">
                            <label for="origin">Shipping to City<i class="text-danger">*</i> : </label>
                            <select name="ship_to_city_id" id="ship_to_city" class="form-control">
                            </select>
                            @error('ship_to_city_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <label for="origin">Dispachers<i class="text-danger">*</i> : </label>
                            <select name="dispatcher_id" id="dispatcher_id" class="form-control">
                                <option value="">Select Dispatcher</option>
                                @foreach ($dispachers as $dispacher)
                                    <option value="{{ $dispacher->id }}">{{ $dispacher->name }}</option>
                                @endforeach
                            </select>
                            @error('dispatcher_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> {{ __('admin.batch.submit_button') }} </button>
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
                $('#ship_from_country').select2();
                $('#ship_from_city').select2();

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
                                'countryId': $(this).val(),
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
                                    @if (!is_null(old('residential.city')))
                                        $('#ship_from_city').val({{ old('residential.city') }});
                                        $('#ship_from_city').trigger('change')
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

                $('#ship_from_country').html('<option value="">Select Country</option>');
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

            $(document).ready(function() {
                $('#ship_to_country').select2();
                $('#ship_to_city').select2();
                $('#orders').select2();

                countries2();

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

                                    @if (!is_null(old('residential.city')))
                                        $('#ship_to_city').val({{ old('residential.city') }});
                                        $('#ship_to_city').trigger('change')
                                    @endif
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


            function countries2() {

                $('#ship_to_country').html('<option value="">Select Country</option>');
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
                                $("#ship_to_country").append('<option value="' + value.id +
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
            gitOrders();

            function gitOrders() {
                let Url = "{{ route('ajax-get-paced-orders') }}";
                $('#orders').html('<option value="" >Select Order</option>');
                $.ajax({
                    url: Url,
                    type: 'get',
                    dataType: 'json',
                    success: function(responce) {
                        console.log(responce.ordres);
                        if (responce.ordres) {
                            $.each(responce.ordres, function(key, value) {
                                $("#orders").append('<option value="' + value.id + '">' + value
                                    .tracking_id +
                                    '</option>')
                            });

                            @if (!is_null('order_id'))
                                $('#orders').val({{ $order_id }});
                                $('#orders').trigger('change')
                            @endif

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                            });
                        }
                    }

                });
            }
           
        </script>
    @endsection
