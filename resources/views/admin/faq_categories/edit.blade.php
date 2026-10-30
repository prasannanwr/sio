@extends('admin.app')
@section('page_title', 'Edit FAQ Category')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Edit FAQ Category</h5>
                    <a href="{{ route('faq-categories.index') }}" class="btn btn-primary">All Categories</a>
                </div>

                @include('admin.partials.notification')

                @include('admin.faq_categories._form', [
                    'action' => route('faq-categories.update', $category->id),
                    'method' => 'PUT',
                    'buttonText' => 'Update Category',
                ])
            </div>
        </div>
    </div>
@endsection
