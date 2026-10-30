<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="name" class="form-label">Configuration Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $configuration->name) }}"
                class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Production Monnify" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-6">
            <label for="base_url" class="form-label">Base URL <span class="text-danger">*</span></label>
            <input type="text" name="base_url" id="base_url" value="{{ old('base_url', $configuration->base_url ?: 'https://sandbox.monnify.com') }}"
                class="form-control @error('base_url') is-invalid @enderror" placeholder="https://sandbox.monnify.com or https://api.monnify.com" required>
            <small class="form-text text-muted">Sandbox: https://sandbox.monnify.com · Live: https://api.monnify.com</small>
            @error('base_url') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-6">
            <label for="api_key" class="form-label">API Key <span class="text-danger">*</span></label>
            <input type="text" name="api_key" id="api_key" value="{{ old('api_key', $configuration->api_key) }}"
                class="form-control @error('api_key') is-invalid @enderror" placeholder="MK_..." autocomplete="off" required>
            @error('api_key') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-6">
            <label for="secret_key" class="form-label">
                Secret Key
                @if ($configuration->exists)
                    <small class="text-muted">(leave blank to keep current)</small>
                @else
                    <span class="text-danger">*</span>
                @endif
            </label>
            <input type="password" name="secret_key" id="secret_key"
                class="form-control @error('secret_key') is-invalid @enderror" placeholder="Monnify secret key"
                autocomplete="new-password" @if (!$configuration->exists) required @endif>
            @error('secret_key') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-6">
            <label for="contract_code" class="form-label">Contract Code <span class="text-danger">*</span></label>
            <input type="text" name="contract_code" id="contract_code" value="{{ old('contract_code', $configuration->contract_code) }}"
                class="form-control @error('contract_code') is-invalid @enderror" placeholder="e.g. 4934121693" required>
            @error('contract_code') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-3">
            <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
            <input type="text" name="currency" id="currency" value="{{ old('currency', $configuration->currency ?: 'NGN') }}"
                class="form-control @error('currency') is-invalid @enderror" placeholder="NGN" required>
            @error('currency') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-3">
            <label for="source_account_number" class="form-label">Disbursement Source Account</label>
            <input type="text" name="source_account_number" id="source_account_number"
                value="{{ old('source_account_number', $configuration->source_account_number) }}"
                class="form-control" placeholder="Wallet account no. (payouts)">
        </div>

        <div class="mb-3 col-md-6">
            <label for="webhook_secret" class="form-label">
                Webhook Secret
                @if ($configuration->exists)
                    <small class="text-muted">(leave blank to keep current / defaults to secret key)</small>
                @endif
            </label>
            <input type="password" name="webhook_secret" id="webhook_secret"
                class="form-control" placeholder="Webhook signature secret" autocomplete="new-password">
        </div>

        <div class="mb-3 col-md-6">
            <label for="redirect_url" class="form-label">Redirect URL</label>
            <input type="text" name="redirect_url" id="redirect_url" value="{{ old('redirect_url', $configuration->redirect_url) }}"
                class="form-control @error('redirect_url') is-invalid @enderror" placeholder="https://...">
            @error('redirect_url') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-12">
            <div class="form-check">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                    @if (old('is_active', $configuration->is_active)) checked @endif>
                <label class="form-check-label" for="is_active">
                    Set as active Monnify configuration (used for payments &amp; payouts)
                </label>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mt-2">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
        <a href="{{ route('monnify-configurations.index') }}" class="btn btn-light">Cancel</a>
    </div>
</form>
