@extends('admin.app')
@section('page_title', 'Vehicles')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Vehicle Management</h5>
                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary">Add Vehicle</a>
                </div>

                @include('admin.partials.notification')

                <form action="{{ route('vehicles.index') }}" method="GET" class="row g-2 mb-4">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search brand, model, plate, delivery man or shipping rate">
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-control">
                            <option value="">All vehicle types</option>
                            @foreach ($vehicleTypes as $type)
                                <option value="{{ $type }}" @if (request('type') == $type) selected @endif>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="delivery_man_id" class="form-control">
                            <option value="">All delivery men</option>
                            @foreach ($deliveryMen as $deliveryMan)
                                <option value="{{ $deliveryMan->id }}" @if ((string) request('delivery_man_id') === (string) $deliveryMan->id) selected @endif>
                                    {{ $deliveryMan->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-secondary w-100">Filter</button>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-light">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Vehicle</th>
                                <th>Plate Number</th>
                                <th>Color / Year</th>
                                <th>Assigned Delivery Man</th>
                                <th>Origin</th>
                                <th>Destination</th>
                                <th>Documents</th>
                                <th>Status</th>
                                <th style="width: 160px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vehicles as $vehicle)
                                @php
                                    $documents = $vehicle->documents ?: [];
                                    $statusClass = $vehicle->status === 'active' ? 'success' : ($vehicle->status === 'inactive' ? 'secondary' : 'danger');
                                    $statusLabel = $vehicle->status === 'decommisioned' ? 'Decommissioned' : ucfirst(str_replace('_', ' ', $vehicle->status));
                                @endphp
                                <tr>
                                    <td>
                                        <span class="badge bg-info">{{ $vehicle->type }}</span><br>
                                        <strong>{{ $vehicle->brand }}</strong>
                                        <span>{{ $vehicle->model }}</span>
                                    </td>
                                    <td>{{ $vehicle->plate_number }}</td>
                                    <td>{{ $vehicle->color }} @if ($vehicle->year) / {{ $vehicle->year }} @endif</td>
                                    <td>
                                        @if ($vehicle->deliveryMan)
                                            <strong>{{ $vehicle->deliveryMan->name }}</strong><br>
                                            <small>{{ $vehicle->deliveryMan->phone ?? $vehicle->deliveryMan->email }}</small>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($vehicle->originCity)
                                            <strong>{{ $vehicle->originCity->name }}</strong>
                                            @if ($vehicle->originCity->country)
                                                <br><small class="text-muted">{{ $vehicle->originCity->country->name }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($vehicle->destinationCity)
                                            <strong>{{ $vehicle->destinationCity->name }}</strong>
                                            @if ($vehicle->destinationCity->country)
                                                <br><small class="text-muted">{{ $vehicle->destinationCity->country->name }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (count($documents))
                                            @foreach ($documents as $document)
                                                @if (! empty($document['path']))
                                                    <a href="{{ asset($document['path']) }}" target="_blank" class="d-block">
                                                        {{ $document['name'] ?? basename($document['path']) }}
                                                    </a>
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="text-muted">No documents</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span></td>
                                    <td>
                                        <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                                        <form action="{{ route('vehicles.destroy', $vehicle->id) }}" method="POST" class="d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this vehicle?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">No vehicles added yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $vehicles->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
