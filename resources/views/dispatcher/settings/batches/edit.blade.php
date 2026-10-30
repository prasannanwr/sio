@extends('admin.app')
@section('page_title', 'Edit Batch')
@section('content')
    <div class="container-fluid">
        {{-- <div class="card"> --}}
        {{-- <div class="card-body"> --}}
        {{-- <h5 class="card-title fw-semibold mb-4">Settings</h5> --}}
        {{-- @include('dispatcher.settings.nav') --}}
        {{-- <hr> --}}
        <div class="card">
            <div class="card-header">
                <h5>Add Batch Log</h5>
                {{-- <a href="{{ route('batches.index') }}" class="btn btn-danger float-right"><i class="fa fa-times"></i>Exit</a> --}}
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                @include('admin.partials.notification')
                {{-- @if (Session::has('message') or isset($message))
                    <div
                        class="alert alert-{{ Session::get('message_type', $message_type ?? 'danger') }} alert-dismissable">
                        <strong>Message &nbsp;</strong> {{ Session::get('message') ?? $message }}
                    </div>
                @endif --}}
                <div class="d-flex gap-5">
                    <p><b>Batch Name : {{ $batch->name }}</b></p>
                    {{-- <p><b>Batch Trackind : {{ $batch->batch_tracking_id }}</b></p> --}}
                </div>

                <form action="{{ route('batches.update', $batch->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row mb-2">
                        <div class="col-6">
                            <div class="ui-widget">
                                <label for="origin">Shipping From Country </label>
                                <input type="text" id="origin" name="origin_"
                                    value="{{ $batch->batchlogs->first()->shipFromCountry->name }}" class="form-control"
                                    autocomplete="off" readonly>

                            </div>
                        </div>
                        <div class="col-6">
                            <div class="ui-widget">
                                <label for="origin">Shipping From City </label>
                                <input type="text" id="origin" name="origin_"
                                    value="{{ $batch->batchlogs->first()->shipFromCity->name }}" class="form-control"
                                    autocomplete="off" readonly>

                            </div>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-6">
                            <div class="ui-widget">
                                <label for="origin">Shipping Origin Country </label>
                                <input type="text" id="origin" name="origin_"
                                    value="{{ $batch->batchlogs->first()->shipToCountry->name }}" class="form-control"
                                    autocomplete="off" readonly>

                            </div>
                        </div>
                        <div class="col-6">
                            <div class="ui-widget">
                                <label for="origin">Shipping Origin City </label>
                                <input type="text" id="origin" name="origin_"
                                    value="{{ $batch->batchlogs->first()->shipToCity->name }}" class="form-control"
                                    autocomplete="off" readonly>


                            </div>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-12 col-lg-6">
                            <label for="origin">Current Shipping Location Of Country<i class="text-danger">*</i> :
                            </label>
                            <select id="ship_to_country_id" name="ship_to_country_id" class="form-select">
                            </select>
                            @error('ship_to_country_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-12 col-lg-6">
                            <label for="origin">Current Shipping Location Of City<i class="text-danger">*</i> : </label>
                            <select name="ship_to_city_id" id="ship_to_city_id" class="form-control">
                            </select>
                            @error('ship_to_city_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                    <div class="row mb-2">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="assigned" {{ $batch->status == 'assigned' ? 'selected' : '' }}>Processing
                                </option>
                                <option value="in_transit" {{ $batch->status == 'in_transit' ? 'selected' : '' }}>In
                                    Transit</option>
                                <option value="delivered" {{ $batch->status == 'delivered' ? 'selected' : '' }}>
                                    Delivered</option>
                                <option value="cancelled" {{ $batch->status == 'cancelled' ? 'selected' : '' }}>
                                    Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <br>
                    <button type="submit"
                        onclick="return confirm('Are you sure you wish to update this batch, all changes set will affect all associated orders')"
                        class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
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
            $('#ship_to_country_id').select2();
            $('#ship_to_city_id').select2();

            countries();

            function countries() {
                $('#ship_to_country_id').html('<option value="">Select Country</option>');
                var _token = '{{ csrf_token() }}';
                let url = "{{ route('ajax-edit-countries') }}";
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
                                $("#ship_to_country_id").append('<option value="' + value.id +
                                    '">' + value.name + '</option>');
                            });

                            @if (isset($lastCountryId))
                                $('#ship_to_country_id').val({{ $lastCountryId }});
                                $('#ship_to_country_id').trigger('change');
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


            }
        });


        var ship_to_country_id = $("#ship_to_country_id");
        ship_to_country_id.wrap('<div class="position-relative"></div>');
        ship_to_country_id.on('change', function() {
            $("#ship_to_city_id").empty()
            $('#ship_to_city_id').html('<option value="">Select City</option>');

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
                                $("#ship_to_city_id").append('<option value="' +
                                    value
                                    .id + '">' + value.name + '</option>');
                            });
                            $("#ship_to_city_id").trigger('change');

                            @if (isset($lastCityId))
                                $('#ship_to_city_id').val({{ $lastCityId }});
                                $('#ship_to_city_id').trigger('change')
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
    </script>
@endsection
