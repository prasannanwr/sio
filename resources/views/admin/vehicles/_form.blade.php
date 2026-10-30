@php
    $canCreateDeliveryMan = $canCreateDeliveryMan ?? ! $vehicle->exists;
    $creatingDeliveryMan = old('create_delivery_man') === '1';
    $selectedShippingCost = $selectedShippingCost ?? ($vehicle->shippingCost ?? null);
    $shippingCostSearchUrl = $shippingCostSearchUrl ?? route('vehicles.shipping-costs.search');
    $selectedOriginLocation = $selectedOriginLocation ?? null;
    $selectedDestinationLocation = $selectedDestinationLocation ?? null;
    $locationSearchUrl = $locationSearchUrl ?? route('vehicles.locations.search');
    $weightRanges = [];
    for ($i = 1; $i <= 50; $i++) {
        $weightRanges[(string) $i] = $i . ' kg';
    }
    $existingDocuments = collect($vehicle->documents ?: []);
    $existingVehiclePhotos = $existingDocuments->filter(fn ($document) => ($document['category'] ?? 'vehicle_photo') === 'vehicle_photo');
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="row">
        <div class="mb-3 col-md-4">
            <label for="type" class="form-label">Vehicle Type</label>
            <select name="type" id="type" class="form-control" required>
                <option value="">Select vehicle type</option>
                @foreach ($vehicleTypes as $type)
                    <option value="{{ $type }}" @if (old('type', $vehicle->type) === $type) selected @endif>{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3 col-md-4">
            <label for="brand" class="form-label">Brand</label>
            <input type="text" name="brand" id="brand" value="{{ old('brand', $vehicle->brand) }}"
                class="form-control" required>
        </div>
        <div class="mb-3 col-md-4">
            <label for="model" class="form-label">Model</label>
            <input type="text" name="model" id="model" value="{{ old('model', $vehicle->model) }}"
                class="form-control" required>
        </div>
    </div>

    <div class="row">
        <div class="mb-3 col-md-4">
            <label for="plate_number" class="form-label">Plate Number</label>
            <input type="text" name="plate_number" id="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}"
                class="form-control" required>
        </div>
        <div class="mb-3 col-md-4">
            <label for="color" class="form-label">Color</label>
            <input type="text" name="color" id="color" value="{{ old('color', $vehicle->color) }}"
                class="form-control" required>
        </div>
        <div class="mb-3 col-md-4">
            <label for="year" class="form-label">Year</label>
            <input type="number" name="year" id="year" min="1900" max="{{ date('Y') + 1 }}"
                value="{{ old('year', $vehicle->year) }}" class="form-control" required>
        </div>
    </div>

    <div class="row">
        <div class="mb-3 col-md-4">
            <label for="delivery_man_id" class="form-label">Assign Delivery Man</label>
            <select name="delivery_man_id" id="delivery_man_id" class="form-control">
                <option value="">Not assigned</option>
                @foreach ($deliveryMen as $deliveryMan)
                    <option value="{{ $deliveryMan->id }}" @if ((string) old('delivery_man_id', $vehicle->delivery_man_id) === (string) $deliveryMan->id) selected @endif>
                        {{ $deliveryMan->name }} @if ($deliveryMan->phone) ({{ $deliveryMan->phone }}) @endif
                    </option>
                @endforeach
            </select>
            @error('delivery_man_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <!-- <div class="mb-3 col-md-4">
            <label for="shipping_cost_id" class="form-label">Shipping Rate</label>
            <select name="shipping_cost_id" id="shipping_cost_id" class="form-control"
                data-search-url="{{ $shippingCostSearchUrl }}">
                <option value="">No shipping rate selected</option>
                @if ($selectedShippingCost)
                    <option value="{{ $selectedShippingCost->id }}" selected>
                        {{ $selectedShippingCost->display_label }}
                    </option>
                @endif
            </select>
            @error('shipping_cost_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div> -->
        <div class="mb-3 col-md-4">
            <label for="origin_location_id" class="form-label">Origin Location</label>
            <select name="origin_location" id="origin_location_id" class="form-control location-select"
                data-search-url="{{ $locationSearchUrl }}">
                <option value="">No location selected</option>
                @if ($selectedOriginLocation)
                    <option value="{{ $selectedOriginLocation->id }}" selected>
                        {{ $selectedOriginLocation->display_label }}
                    </option>
                @endif
            </select>
            @error('origin_location')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
         <div class="mb-3 col-md-4">
            <label for="destination_location_id" class="form-label">Destination Location</label>
            <select name="destination_location" id="destination_location_id" class="form-control location-select"
                data-search-url="{{ $locationSearchUrl }}">
                <option value="">No location selected</option>
                @if ($selectedDestinationLocation)
                    <option value="{{ $selectedDestinationLocation->id }}" selected>
                        {{ $selectedDestinationLocation->display_label }}
                    </option>
                @endif
            </select>
            @error('destination_location')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-3 col-md-4">
            <label for="weight_range_id" class="form-label">Weight Range</label>
            <select
            name="weight_range"
            id="weight_range_id"
            class="form-control weight-range-select"
        >
            <option value="">Select Weight Range</option>
            @foreach ($weightRanges as $value => $label)
                <option value="{{ $value }}" @if ((string) old('weight_range', $vehicle->weight_range) === (string) $value) selected @endif>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        </div>
        <div class="mb-3 col-md-4">
           <label for="rate_per_km_per_kg" class="form-label">
                Rate per km per kg
            </label>

            <input
                type="number"
                step="0.01"
                min="0"
                name="rate_per_km_per_kg"
                id="rate_per_km_per_kg"
                class="form-control"
                placeholder="Example: 0.5"
                value="{{ old('rate_per_km_per_kg', $vehicle->rate_per_km_per_kg) }}"
            >

            @error('rate_per_km_per_kg')
                <small class="text-danger">{{ $message }}</small>
            @enderror

            <small class="form-text text-muted">
                Example: 0.5
            </small>
        </div>

        <div class="mb-3 col-md-4">
            <label for="rate_eur" class="form-label">Rate (EUR)</label>
            <input
                type="number"
                step="0.01"
                min="0"
                name="rate_eur"
                id="rate_eur"
                class="form-control"
                placeholder="Example: 0.50"
                value="{{ old('rate_eur', $vehicle->rate_eur) }}"
            >
            @error('rate_eur')
                <small class="text-danger">{{ $message }}</small>
            @enderror
            <small class="form-text text-muted">Price in EUR. Set EUR and/or Naira.</small>
        </div>

        <div class="mb-3 col-md-4">
            <label for="rate_naira" class="form-label">Rate (Naira)</label>
            <input
                type="number"
                step="0.01"
                min="0"
                name="rate_naira"
                id="rate_naira"
                class="form-control"
                placeholder="Example: 500.00"
                value="{{ old('rate_naira', $vehicle->rate_naira) }}"
            >
            @error('rate_naira')
                <small class="text-danger">{{ $message }}</small>
            @enderror
            <small class="form-text text-muted">Price in Naira (NGN). Set EUR and/or Naira.</small>
        </div>

        <div class="mb-3 col-md-4">
            <label for="delivery_man_phone" class="form-label">Delivery Man Phone</label>
            <input
                type="text"
                name="delivery_man_phone"
                id="delivery_man_phone"
                class="form-control @error('delivery_man_phone') is-invalid @enderror"
                placeholder="Example: +234 0803 123 4567"
                value="{{ old('delivery_man_phone', optional($vehicle->deliveryMan)->phone) }}"
            >
            @error('delivery_man_phone')
                <small class="text-danger">{{ $message }}</small>
            @enderror
            <small class="form-text text-muted">Saved cleaned (drops "+", spaces and leading zeros). Applies to the assigned delivery man.</small>
        </div>

        @php $currentLocation = $currentLocation ?? null; @endphp
        <div class="mb-3 col-md-4">
            <label for="current_latitude" class="form-label">Delivery Man Current Latitude</label>
            <input
                type="number"
                step="any"
                name="current_latitude"
                id="current_latitude"
                class="form-control @error('current_latitude') is-invalid @enderror"
                placeholder="Example: 6.5244"
                value="{{ old('current_latitude', optional($currentLocation)->latitude) }}"
            >
            @error('current_latitude')
                <small class="text-danger">{{ $message }}</small>
            @enderror
            <small class="form-text text-muted">Saves the delivery man's current GPS position (set latitude and longitude together).</small>
        </div>

        <div class="mb-3 col-md-4">
            <label for="current_longitude" class="form-label">Delivery Man Current Longitude</label>
            <input
                type="number"
                step="any"
                name="current_longitude"
                id="current_longitude"
                class="form-control @error('current_longitude') is-invalid @enderror"
                placeholder="Example: 3.3792"
                value="{{ old('current_longitude', optional($currentLocation)->longitude) }}"
            >
            @error('current_longitude')
                <small class="text-danger">{{ $message }}</small>
            @enderror
            @if ($currentLocation && $currentLocation->recorded_at)
                <small class="form-text text-muted">Last updated: {{ $currentLocation->recorded_at->format('d M Y, H:i') }}</small>
            @endif
        </div>

        @if ($vehicle->exists && ! is_null($vehicle->distance_km))
            <div class="mb-3 col-md-4">
                <label for="distance_km" class="form-label">Distance (km)</label>
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    id="distance_km"
                    class="form-control"
                    value="{{ old('distance_km', $vehicle->distance_km) }}"
                    readonly
                >
            </div>
        @endif
        
        <div class="mb-3 col-md-4">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @if (old('status', $vehicle->status ?: 'active') === $status) selected @endif>
                        {{ $status === 'decommisioned' ? 'Decommissioned' : ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($canCreateDeliveryMan)
        <div class="mb-3">
            <div class="form-check">
                <input type="checkbox" name="create_delivery_man" id="create_delivery_man" value="1"
                    class="form-check-input" @if ($creatingDeliveryMan) checked @endif>
                <label for="create_delivery_man" class="form-check-label">
                    Add a new delivery man and assign this vehicle
                </label>
            </div>
        </div>

        <div id="new_delivery_man_fields" class="border rounded p-3 mb-3"
            style="{{ $creatingDeliveryMan ? '' : 'display: none;' }}">
            <div class="row">
                <div class="mb-3 col-md-4">
                    <label for="new_delivery_man_name" class="form-label">Delivery Man Name</label>
                    <input type="text" name="new_delivery_man[name]" id="new_delivery_man_name"
                        value="{{ old('new_delivery_man.name') }}"
                        class="form-control @error('new_delivery_man.name') is-invalid @enderror">
                    @error('new_delivery_man.name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3 col-md-4">
                    <label for="new_delivery_man_email" class="form-label">Email</label>
                    <input type="email" name="new_delivery_man[email]" id="new_delivery_man_email"
                        value="{{ old('new_delivery_man.email') }}"
                        class="form-control @error('new_delivery_man.email') is-invalid @enderror">
                    @error('new_delivery_man.email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3 col-md-4">
                    <label for="new_delivery_man_phone" class="form-label">Phone</label>
                    <input type="text" name="new_delivery_man[phone]" id="new_delivery_man_phone"
                        value="{{ old('new_delivery_man.phone') }}"
                        class="form-control @error('new_delivery_man.phone') is-invalid @enderror">
                    @error('new_delivery_man.phone')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="new_delivery_man_password" class="form-label">Password</label>
                    <input type="password" name="new_delivery_man[password]" id="new_delivery_man_password"
                        class="form-control @error('new_delivery_man.password') is-invalid @enderror"
                        autocomplete="new-password">
                    @error('new_delivery_man.password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3 col-md-6">
                    <label for="new_delivery_man_password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" name="new_delivery_man[password_confirmation]"
                        id="new_delivery_man_password_confirmation" class="form-control"
                        autocomplete="new-password">
                </div>
            </div>
        </div>
    @endif

    <div class="mb-3">
        <label for="documents" class="form-label">Vehicle Photos</label>
        <input
            type="file"
            name="documents[]"
            id="documents"
            class="form-control @error('documents') is-invalid @enderror @error('documents.*') is-invalid @enderror"
            multiple
            accept=".jpg,.jpeg,.png"
            data-min-files="{{ $vehicle->exists ? 0 : 2 }}"
            data-max-files="5"
            @if (! $vehicle->exists) required @endif
        >
        @error('documents')
            <span class="invalid-feedback d-block">{{ $message }}</span>
        @enderror
        @error('documents.*')
            <span class="invalid-feedback d-block">{{ $message }}</span>
        @enderror
        <small class="text-muted">
            Upload {{ $vehicle->exists ? 'up to 5' : '2 to 5' }} vehicle photos. Accepted formats: JPG, JPEG, PNG. Maximum size: 5MB each.
        </small>
    </div>

    @if ($vehicle->exists && $existingVehiclePhotos->count())
        <div class="mb-3">
            <label class="form-label">Existing Photos</label>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Uploaded</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($existingVehiclePhotos as $document)
                            @if (! empty($document['path']))
                                <tr>
                                    <td>
                                        <a href="{{ asset($document['path']) }}" target="_blank">
                                            {{ $document['name'] ?? basename($document['path']) }}
                                        </a>
                                    </td>
                                    <td>{{ $document['uploaded_at'] ?? '-' }}</td>
                                    <td>
                                        <label class="d-flex align-items-center gap-2">
                                            <input type="checkbox" name="remove_documents[]" value="{{ $document['path'] }}">
                                            Remove
                                        </label>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    <a href="{{ route('vehicles.index') }}" class="btn btn-light">Cancel</a>
</form>

<script>
    (function () {
        function initShippingRateSelect() {
            if (!window.jQuery || !window.jQuery.fn.select2) {
                return;
            }

            // var field = window.jQuery('#origin_location_id');


            window.jQuery('.location-select').each(function () {

                var field = window.jQuery(this);

                field.select2({
                    width: '100%',
                    placeholder: 'Search country, city/state',
                    allowClear: true,
                    minimumInputLength: 1,
                    ajax: {
                        url: field.data('search-url'),
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function (data) {
                            return data;
                        },
                        cache: true
                    }
                });
            });

            jQuery('.weight-range-select').select2({
                width: '100%',
                placeholder: 'Select Weight Range',
                allowClear: true
            });
        }

        document.addEventListener('DOMContentLoaded', initShippingRateSelect);

        document.addEventListener('DOMContentLoaded', function () {
            var photoInput = document.getElementById('documents');

            if (!photoInput) {
                return;
            }

            photoInput.addEventListener('change', function () {
                var fileCount = photoInput.files ? photoInput.files.length : 0;
                var minFiles = parseInt(photoInput.dataset.minFiles || '0', 10);
                var maxFiles = parseInt(photoInput.dataset.maxFiles || '5', 10);

                if (fileCount && fileCount < minFiles) {
                    photoInput.setCustomValidity('Please upload at least ' + minFiles + ' vehicle photos.');
                } else if (fileCount > maxFiles) {
                    photoInput.setCustomValidity('Please upload no more than ' + maxFiles + ' vehicle photos.');
                } else {
                    photoInput.setCustomValidity('');
                }
            });
        });
    })();
</script>

@if ($canCreateDeliveryMan)
    <script>
        (function () {
            var toggle = document.getElementById('create_delivery_man');
            var fields = document.getElementById('new_delivery_man_fields');
            var deliveryManSelect = document.getElementById('delivery_man_id');
            var requiredFields = [
                document.getElementById('new_delivery_man_name'),
                document.getElementById('new_delivery_man_email'),
                document.getElementById('new_delivery_man_phone'),
                document.getElementById('new_delivery_man_password'),
                document.getElementById('new_delivery_man_password_confirmation')
            ];

            function syncDeliveryManFields() {
                var enabled = toggle && toggle.checked;

                if (fields) {
                    fields.style.display = enabled ? '' : 'none';
                }

                if (deliveryManSelect) {
                    deliveryManSelect.disabled = enabled;
                    if (enabled) {
                        deliveryManSelect.value = '';
                    }
                }

                requiredFields.forEach(function (field) {
                    if (field) {
                        field.required = enabled;
                    }
                });
            }

            if (toggle) {
                toggle.addEventListener('change', syncDeliveryManFields);
                syncDeliveryManFields();
            }
        })();
    </script>
@endif
