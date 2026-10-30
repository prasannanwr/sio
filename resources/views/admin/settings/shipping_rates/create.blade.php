@extends('admin.app')
@section('page_title', 'Create Shipping Rate')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Settings</h5>
                @include('admin.settings.nav')
                <hr>
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route('locations.index') }}" class="btn btn-danger float-right"><i
                                class="fa fa-times"></i>Exit</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        @include('admin.partials.notification')
                        <h5>Create New Shipping Rate</h5>
                        <form action="{{ route('shipping_rates.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}">
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <div class="ui-widget">
                                        <label for="origin">Origin</label>
                                        <input type="text" id="origin" name="origin_" value="{{ old('origin_') }}"
                                            class="form-control" autocomplete="off">
                                        <input type="hidden" name="origin" value="{{ old('origin') }}" id="origin_id">
                                    </div>
                                    {{-- <select name="origin" id="origin" class="form-control">
                                        <option value="">--select origin--</option>
                                        @foreach ($locations as $loc)
                                            <option value="{{ $loc->id }}">{{ $loc->name }}[Lat:
                                                {{ $loc->latitude }}, Long: {{ $loc->longitude }}]</option>
                                        @endforeach
                                    </select> --}}
                                </div>
                                <div class="form-group col-md-6">

                                    <div class="ui-widget">
                                        <label for="dest">Destination</label>
                                        <input type="text" id="dest" name="dest_" value="{{ old('dest_') }}"
                                            class="form-control" autocomplete="off">
                                        <input type="hidden" name="dest" value="{{ old('dest') }}" id="dest_id">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="min_weight">Min. weight</label>
                                    <input step="any" min="0" class="form-control" type="number"
                                        name="min_weight" id="min_weight" value="{{ old('min_weight') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="max_weight">Max. weight</label>
                                    <input step="any" min="0" class="form-control" type="number"
                                        name="max_weight" id="max_weight" value="{{ old('max_weight') }}">
                                </div>
                            </div>
                            <br>
                            {{-- <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="pickup_cost_per_km">Package Pickup Cost per Km(&euro;)</label>
                                    <input step="any" min="0" class="form-control" type="number"
                                        name="pickup_cost_per_km" id="pickup_cost_per_km" value="{{ old('pickup_cost_per_km') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="delivery_cost_per_km">Package Delivery Cost per Km(&euro;)</label>
                                    <input step="any" min="0" class="form-control" type="number"
                                        name="delivery_cost_per_km" id="delivery_cost_per_km" value="{{ old('delivery_cost_per_km') }}">
                                </div>
                            </div> --}}
                            <br>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="width">Deimension-width(cm).</label>
                                    <input type="number" step="any" min = "0" value="0" class="form-control" name="width" id="width" value="{{ old('width') }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="len">Deimension-length(cm)</label>
                                    <input type="number" step="any" min = "0" value="0" class="form-control" name="len" id="len" value="{{ old('len') }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="height">Deimension-height(cm)</label>
                                    <input type="number" step="any" min = "0" value="0" class="form-control" name="height" id="height" value="{{ old('height') }}">
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="price">Price(&euro;)</label>
                                    <input step="any" min="0" class="form-control" type="number" name="price"
                                        id="price" value="{{ old('price') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="height">Transit Time(days)</label>
                                    <input type="number" step="1" min = "0" value="1" class="form-control" name="transit_days" id="transit_days" value="{{ old('transit_days') }}">
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="desc">Spec. / Terms</label>
                                    <textarea name="desc" id="desc" class="form-control">{{ old('desc') }}</textarea>
                                </div>
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    </script>
@endsection
