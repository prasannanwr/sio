@extends('admin.app')
@section('page_title', 'Add Monnify Configuration')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Add Monnify Configuration</h5>
                    <a href="{{ route('monnify-configurations.index') }}" class="btn btn-primary">All Configurations</a>
                </div>

                @include('admin.partials.notification')

                @include('admin.monnify_configurations._form', [
                    'action' => route('monnify-configurations.store'),
                    'method' => 'POST',
                    'buttonText' => 'Save Configuration',
                ])
            </div>
        </div>
    </div>
@endsection
