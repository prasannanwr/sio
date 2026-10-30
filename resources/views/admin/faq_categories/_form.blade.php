<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                placeholder="e.g. Insurance" value="{{ old('name', $category->name) }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-6">
            <label for="icon" class="form-label">Icon</label>
            <input type="text" name="icon" id="icon" class="form-control @error('icon') is-invalid @enderror"
                placeholder="e.g. fa-solid fa-shield (icon class/keyword)" value="{{ old('icon', $category->icon) }}">
            <small class="form-text text-muted">Icon identifier the app uses to render the category icon.</small>
            @error('icon') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-6">
            <label for="sort_order" class="form-label">Sort Order</label>
            <input type="number" min="0" name="sort_order" id="sort_order" class="form-control"
                value="{{ old('sort_order', $category->sort_order ?? 0) }}">
        </div>

        <div class="mb-3 col-md-6">
            <label for="is_active" class="form-label">Status</label>
            <select name="is_active" id="is_active" class="form-control">
                <option value="1" @if (old('is_active', (int) $category->is_active) == 1) selected @endif>Active</option>
                <option value="0" @if (old('is_active', (int) $category->is_active) === 0) selected @endif>Inactive</option>
            </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    <a href="{{ route('faq-categories.index') }}" class="btn btn-light">Cancel</a>
</form>
