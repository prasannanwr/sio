<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="row">
        <div class="mb-3 col-md-8">
            <label for="question" class="form-label">Question</label>
            <input type="text" name="question" id="question" class="form-control @error('question') is-invalid @enderror"
                placeholder="e.g. Why my track is not showing" value="{{ old('question', $faq->question) }}" required>
            @error('question') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-4">
            <label for="faq_category_id" class="form-label">Category</label>
            <select name="faq_category_id" id="faq_category_id" class="form-control">
                <option value="">Uncategorized</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @if ((string) old('faq_category_id', $faq->faq_category_id) === (string) $category->id) selected @endif>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('faq_category_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-12">
            <label for="answer" class="form-label">Answer</label>
            <textarea name="answer" id="answer" rows="5" class="form-control @error('answer') is-invalid @enderror"
                placeholder="Write the answer shown when the FAQ is expanded" required>{{ old('answer', $faq->answer) }}</textarea>
            @error('answer') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3 col-md-4">
            <label for="is_popular" class="form-label">Popular Searched</label>
            <select name="is_popular" id="is_popular" class="form-control">
                <option value="1" @if (old('is_popular', (int) $faq->is_popular) == 1) selected @endif>Yes</option>
                <option value="0" @if (old('is_popular', (int) $faq->is_popular) === 0) selected @endif>No</option>
            </select>
            <small class="form-text text-muted">Show under "Popular Searched".</small>
        </div>

        <div class="mb-3 col-md-4">
            <label for="sort_order" class="form-label">Sort Order</label>
            <input type="number" min="0" name="sort_order" id="sort_order" class="form-control"
                value="{{ old('sort_order', $faq->sort_order ?? 0) }}">
        </div>

        <div class="mb-3 col-md-4">
            <label for="is_active" class="form-label">Status</label>
            <select name="is_active" id="is_active" class="form-control">
                <option value="1" @if (old('is_active', (int) $faq->is_active) == 1) selected @endif>Active</option>
                <option value="0" @if (old('is_active', (int) $faq->is_active) === 0) selected @endif>Inactive</option>
            </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    <a href="{{ route('faqs.index') }}" class="btn btn-light">Cancel</a>
</form>
