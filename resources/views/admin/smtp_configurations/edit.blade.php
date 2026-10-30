@extends('admin.app')
@section('page_title', 'Edit SMTP Configuration')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Edit SMTP Configuration</h5>
                    <a href="{{ route('smtp-configurations.index') }}" class="btn btn-primary">All SMTP Configurations</a>
                </div>

                @include('admin.partials.notification')

                @include('admin.smtp_configurations._form', [
                    'action' => route('smtp-configurations.update', $configuration->id),
                    'method' => 'PUT',
                    'buttonText' => 'Update SMTP Configuration',
                ])

                <hr class="my-4">

                <h6 class="fw-semibold mb-3">Send Test Email</h6>
                <form action="{{ route('smtp-configurations.send-test', $configuration->id) }}" method="POST"
                    class="row g-2 align-items-end">
                    @csrf
                    <div class="col-md-6">
                        <label for="test_email" class="form-label">Recipient</label>
                        <input type="email" name="test_email" id="test_email" class="form-control"
                            placeholder="you@example.com" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-secondary">Send Test Email</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
