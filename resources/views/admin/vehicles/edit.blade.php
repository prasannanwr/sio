@extends('admin.app')
@section('page_title', 'Edit Vehicle')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Edit Vehicle</h5>
                    <a href="{{ route('vehicles.index') }}" class="btn btn-primary">All Vehicles</a>
                </div>

                @include('admin.partials.notification')

                @include('admin.vehicles._form', [
                    'action' => route('vehicles.update', $vehicle->id),
                    'method' => 'PUT',
                    'buttonText' => 'Update Vehicle',
                ])
            </div>
        </div>
    </div>
@endsection
