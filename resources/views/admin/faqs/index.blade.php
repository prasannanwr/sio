@extends('admin.app')
@section('page_title', 'FAQs')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Help Center — FAQs</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('faq-categories.index') }}" class="btn btn-light">Manage Categories</a>
                        <a href="{{ route('faqs.create') }}" class="btn btn-primary">Add FAQ</a>
                    </div>
                </div>

                @include('admin.partials.notification')

                <form action="{{ route('faqs.index') }}" method="GET" class="row g-2 mb-4">
                    <div class="col-md-5">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search question or answer">
                    </div>
                    <div class="col-md-4">
                        <select name="faq_category_id" class="form-control">
                            <option value="">All categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @if ((string) request('faq_category_id') === (string) $category->id) selected @endif>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-secondary w-100">Filter</button>
                        <a href="{{ route('faqs.index') }}" class="btn btn-light">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Category</th>
                                <th>Popular</th>
                                <th>Sort</th>
                                <th>Status</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($faqs as $faq)
                                <tr>
                                    <td>
                                        <strong>{{ $faq->question }}</strong>
                                        <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($faq->answer, 90) }}</small>
                                    </td>
                                    <td>{{ optional($faq->category)->name ?? 'Uncategorized' }}</td>
                                    <td>
                                        @if ($faq->is_popular)
                                            <span class="badge bg-info">Popular</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $faq->sort_order }}</td>
                                    <td>
                                        <span class="badge bg-{{ $faq->is_active ? 'success' : 'secondary' }}">
                                            {{ $faq->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('faqs.edit', $faq->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                                        <form action="{{ route('faqs.destroy', $faq->id) }}" method="POST" class="d-inline-block"
                                            onsubmit="return confirm('Delete this FAQ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No FAQs yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $faqs->links() }}
            </div>
        </div>
    </div>
@endsection
