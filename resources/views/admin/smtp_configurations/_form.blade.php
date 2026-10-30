<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="name" class="form-label">Configuration Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name"
                value="{{ old('name', $configuration->name) }}"
                class="form-control" placeholder="e.g. Production SMTP" required>
        </div>

        <div class="mb-3 col-md-6">
            <label for="mailer" class="form-label">Mailer <span class="text-danger">*</span></label>
            <input type="text" name="mailer" id="mailer"
                value="{{ old('mailer', $configuration->mailer ?: 'smtp') }}"
                class="form-control" placeholder="smtp" required>
        </div>

        <div class="mb-3 col-md-8">
            <label for="host" class="form-label">Host <span class="text-danger">*</span></label>
            <input type="text" name="host" id="host"
                value="{{ old('host', $configuration->host) }}"
                class="form-control" placeholder="mail.privateemail.com" required>
        </div>

        <div class="mb-3 col-md-4">
            <label for="port" class="form-label">Port <span class="text-danger">*</span></label>
            <input type="number" name="port" id="port" min="1" max="65535"
                value="{{ old('port', $configuration->port ?: 465) }}"
                class="form-control" placeholder="465" required>
        </div>

        <div class="mb-3 col-md-6">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username"
                value="{{ old('username', $configuration->username) }}"
                class="form-control" placeholder="support@example.com" autocomplete="off">
        </div>

        <div class="mb-3 col-md-6">
            <label for="password" class="form-label">
                Password
                @if ($configuration->exists)
                    <small class="text-muted">(leave blank to keep current)</small>
                @else
                    <span class="text-danger">*</span>
                @endif
            </label>
            <input type="password" name="password" id="password"
                class="form-control" placeholder="SMTP password" autocomplete="new-password"
                @if (!$configuration->exists) required @endif>
        </div>

        <div class="mb-3 col-md-4">
            <label for="encryption" class="form-label">Encryption</label>
            <select name="encryption" id="encryption" class="form-control">
                @foreach ($encryptions as $value => $label)
                    <option value="{{ $value }}"
                        @if ((string) old('encryption', $configuration->encryption) === (string) $value) selected @endif>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3 col-md-4">
            <label for="from_address" class="form-label">From Address <span class="text-danger">*</span></label>
            <input type="email" name="from_address" id="from_address"
                value="{{ old('from_address', $configuration->from_address) }}"
                class="form-control" placeholder="support@example.com" required>
        </div>

        <div class="mb-3 col-md-4">
            <label for="from_name" class="form-label">From Name <span class="text-danger">*</span></label>
            <input type="text" name="from_name" id="from_name"
                value="{{ old('from_name', $configuration->from_name ?: config('app.name')) }}"
                class="form-control" placeholder="{{ config('app.name') }}" required>
        </div>

        <div class="mb-3 col-md-12">
            <div class="form-check">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                    @if (old('is_active', $configuration->is_active)) checked @endif>
                <label class="form-check-label" for="is_active">
                    Set as active SMTP configuration (used for outgoing email)
                </label>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mt-2">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
        <a href="{{ route('smtp-configurations.index') }}" class="btn btn-light">Cancel</a>
    </div>
</form>
