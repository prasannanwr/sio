@extends('admin.app')
@section('page_title', 'Location Shipping Rate')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $buttonText }}</h5>
                    <a href="{{ route('shipping_rates.index') }}" class="btn btn-primary btn-sm">All Rates</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($method !== 'POST')
                        @method($method)
                    @endif

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="origin_city_id" class="form-label">Origin City</label>
                            <select name="origin_city_id" id="origin_city_id" class="form-control location-select"
                                data-search-url="{{ $locationSearchUrl }}" required>
                                <option value="">Select origin city</option>
                                @if ($selectedOriginLocation)
                                    <option value="{{ $selectedOriginLocation->id }}" selected>
                                        {{ $selectedOriginLocation->display_label }}
                                    </option>
                                @endif
                            </select>
                            @error('origin_city_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="destination_city_id" class="form-label">Destination City</label>
                            <select name="destination_city_id" id="destination_city_id" class="form-control location-select"
                                data-search-url="{{ $locationSearchUrl }}" required>
                                <option value="">Select destination city</option>
                                @if ($selectedDestinationLocation)
                                    <option value="{{ $selectedDestinationLocation->id }}" selected>
                                        {{ $selectedDestinationLocation->display_label }}
                                    </option>
                                @endif
                            </select>
                            @error('destination_city_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label for="freight_mode" class="form-label">Freight Quotation</label>
                            <select name="freight_mode" id="freight_mode" class="form-control" required>
                                <option value="">Select freight mode</option>
                                @foreach (['Air', 'Sea', 'Road'] as $mode)
                                    <option value="{{ $mode }}" @if (old('freight_mode', $rate->freight_mode) === $mode) selected @endif>
                                        {{ $mode }}
                                    </option>
                                @endforeach
                            </select>
                            @error('freight_mode')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-4">
                            <label for="weight_range" class="form-label">Weight Range</label>
                            <select name="weight_range" id="weight_range" class="form-control">
                                <option value="">No range</option>
                                @foreach ($weightRanges as $value => $label)
                                    <option value="{{ $value }}" @if ((string) old('weight_range', $rate->weight_range) === (string) $value) selected @endif>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('weight_range')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-4">
                            <label for="weight" class="form-label">Max Weight (kg)</label>
                            <input type="number" name="weight" id="weight" class="form-control" min="0" step="0.01"
                                value="{{ old('weight', $rate->weight) }}">
                            @error('weight')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label for="cost_eur" class="form-label">Shipping Cost (EUR)</label>
                            <input type="number" name="cost_eur" id="cost_eur" class="form-control" min="0" step="0.01"
                                placeholder="e.g. 25.00" value="{{ old('cost_eur', $rate->cost_eur) }}">
                            @error('cost_eur')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-4">
                            <label for="cost_naira" class="form-label">Shipping Cost (NGN)</label>
                            <input type="number" name="cost_naira" id="cost_naira" class="form-control" min="0" step="0.01"
                                placeholder="e.g. 45000.00" value="{{ old('cost_naira', $rate->cost_naira) }}">
                            @error('cost_naira')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-4">
                            <label for="day" class="form-label">Delivery Day</label>
                            <input type="text" name="day" id="day" class="form-control"
                                value="{{ old('day', $rate->day) }}" placeholder="Example: 2-3 days">
                            @error('day')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-4">
                            <label for="distance_km" class="form-label">Distance (km)</label>
                            <input type="number" id="distance_km" class="form-control" min="0" step="0.01"
                                value="{{ $rate->distance_km }}" readonly>
                            @error('distance_km')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <small class="form-text text-muted">Calculated automatically from the selected cities.</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label for="rate_per_km_per_kg" class="form-label">Rate per km per kg</label>
                            <input type="number" name="rate_per_km_per_kg" id="rate_per_km_per_kg" class="form-control"
                                min="0" step="0.01" value="{{ old('rate_per_km_per_kg', $rate->rate_per_km_per_kg) }}">
                            @error('rate_per_km_per_kg')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        @foreach ([
                            'invoice_document' => 'Invoice',
                            'packing_list_document' => 'Packing List',
                            'id_document' => 'ID',
                            'permit_document' => 'Permits',
                        ] as $field => $label)
                            <div class="mb-3 col-md-6">
                                <label for="{{ $field }}" class="form-label">{{ $label }}</label>
                                <input type="file" name="{{ $field }}" id="{{ $field }}" class="form-control"
                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                @if ($rate->{$field})
                                    <small class="form-text">
                                        Current: <a href="{{ asset($rate->{$field}) }}" target="_blank">View {{ strtolower($label) }}</a>
                                    </small>
                                @endif
                                @error($field)
                                    <small class="text-danger d-block">{{ $message }}</small>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <div class="text-end">
                        <a href="{{ route('shipping_rates.index') }}" class="btn btn-light">Cancel</a>
                        <button class="btn btn-primary">{{ $buttonText }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('.location-select').select2({
            width: '100%',
            ajax: {
                url: function() {
                    return $(this).data('search-url');
                },
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return data;
                },
                cache: true
            },
            minimumInputLength: 1
        });
    </script>
@endsection
