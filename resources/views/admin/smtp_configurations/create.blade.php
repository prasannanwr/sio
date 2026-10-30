@extends('admin.app')
@section('page_title', 'Create new SMTP')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Create new SMTP</h5>
                    <a href="{{ route('smtp-configurations.index') }}" class="btn btn-primary">All SMTP Configurations</a>
                </div>

                @include('admin.partials.notification')

                @include('admin.smtp_configurations._form', [
                    'action' => route('smtp-configurations.store'),
                    'method' => 'POST',
                    'buttonText' => 'Save SMTP Configuration',
                ])
            </div>
        </div>
    </div>
@endsection
