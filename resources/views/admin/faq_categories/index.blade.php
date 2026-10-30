@extends('admin.app')
@section('page_title', 'FAQ Categories')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Help Center — Categories</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('faqs.index') }}" class="btn btn-light">Manage FAQs</a>
                        <a href="{{ route('faq-categories.create') }}" class="btn btn-primary">Add Category</a>
                    </div>
                </div>

                @include('admin.partials.notification')

                <form action="{{ route('faq-categories.index') }}" method="GET" class="row g-2 mb-4">
                    <div class="col-md-6">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search category name">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-secondary w-100">Filter</button>
                        <a href="{{ route('faq-categories.index') }}" class="btn btn-light">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Icon</th>
                                <th>FAQs</th>
                                <th>Sort</th>
                                <th>Status</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->icon ?? '—' }}</td>
                                    <td>{{ $category->faqs_count }}</td>
                                    <td>{{ $category->sort_order }}</td>
                                    <td>
                                        <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('faq-categories.edit', $category->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                                        <form action="{{ route('faq-categories.destroy', $category->id) }}" method="POST" class="d-inline-block"
                                            onsubmit="return confirm('Delete this category? Its FAQs will remain but become uncategorized.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No categories yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $categories->links() }}
            </div>
        </div>
    </div>
@endsection
