@extends('admin.app')
@section('page_title', 'Add FAQ')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Add FAQ</h5>
                    <a href="{{ route('faqs.index') }}" class="btn btn-primary">All FAQs</a>
                </div>

                @include('admin.partials.notification')

                @include('admin.faqs._form', [
                    'action' => route('faqs.store'),
                    'method' => 'POST',
                    'buttonText' => 'Save FAQ',
                ])
            </div>
        </div>
    </div>
@endsection
