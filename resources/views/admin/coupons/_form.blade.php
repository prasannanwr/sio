<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="code" class="form-label">Coupon Code</label>
            <input type="text" name="code" id="code" class="form-control text-uppercase @error('code') is-invalid @enderror"
                placeholder="e.g. WELCOME10" value="{{ old('code', $coupon->code) }}" required>
            @error('code') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-6">
            <label for="description" class="form-label">Description</label>
            <input type="text" name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                placeholder="Optional internal note" value="{{ old('description', $coupon->description) }}">
            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-4">
            <label for="discount_type" class="form-label">Discount Type</label>
            <select name="discount_type" id="discount_type" class="form-control" required>
                @foreach ($discountTypes as $type)
                    <option value="{{ $type }}" @if (old('discount_type', $coupon->discount_type) === $type) selected @endif>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
            @error('discount_type') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-4">
            <label for="discount_value" class="form-label">Discount Value</label>
            <input type="number" step="0.01" min="0" name="discount_value" id="discount_value"
                class="form-control @error('discount_value') is-invalid @enderror"
                value="{{ old('discount_value', $coupon->discount_value) }}" required>
            <small class="form-text text-muted">For percentage, 0–100. For fixed, amount in the selected currency.</small>
            @error('discount_value') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-4">
            <label for="currency" class="form-label">Currency (strict match)</label>
            <select name="currency" id="currency" class="form-control" required>
                <option value="">Select currency</option>
                @foreach ($currencies as $currency)
                    <option value="{{ $currency }}" @if (old('currency', $coupon->currency) === $currency) selected @endif>
                        {{ $currency }}
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">Coupon applies only to orders in this currency.</small>
            @error('currency') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-4">
            <label for="min_order_value" class="form-label">Minimum Order Value</label>
            <input type="number" step="0.01" min="0" name="min_order_value" id="min_order_value"
                class="form-control @error('min_order_value') is-invalid @enderror"
                placeholder="Leave blank for no minimum" value="{{ old('min_order_value', $coupon->min_order_value) }}">
            @error('min_order_value') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-4">
            <label for="usage_limit" class="form-label">Usage Limit</label>
            <input type="number" min="1" name="usage_limit" id="usage_limit"
                class="form-control @error('usage_limit') is-invalid @enderror"
                placeholder="Leave blank for unlimited" value="{{ old('usage_limit', $coupon->usage_limit) }}">
            @error('usage_limit') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-4">
            <label for="is_active" class="form-label">Status</label>
            <select name="is_active" id="is_active" class="form-control">
                <option value="1" @if (old('is_active', (int) $coupon->is_active) == 1) selected @endif>Active</option>
                <option value="0" @if (old('is_active', (int) $coupon->is_active) === 0) selected @endif>Inactive</option>
            </select>
            @error('is_active') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-6">
            <label for="starts_at" class="form-label">Starts At</label>
            <input type="datetime-local" name="starts_at" id="starts_at"
                class="form-control @error('starts_at') is-invalid @enderror"
                value="{{ old('starts_at', optional($coupon->starts_at)->format('Y-m-d\TH:i')) }}">
            @error('starts_at') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-6">
            <label for="expires_at" class="form-label">Expires At</label>
            <input type="datetime-local" name="expires_at" id="expires_at"
                class="form-control @error('expires_at') is-invalid @enderror"
                value="{{ old('expires_at', optional($coupon->expires_at)->format('Y-m-d\TH:i')) }}">
            @error('expires_at') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>

    <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    <a href="{{ route('coupons.index') }}" class="btn btn-light">Cancel</a>
</form>
