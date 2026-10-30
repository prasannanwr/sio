@extends('admin.app')
@section('page_title', 'Delivery Tracking')
@section('content')
    @php
        $statusLabels = [
            'PENDING' => 'Pending',
            'ACCEPTED' => 'Accepted',
            'ON_THE_WAY' => 'On the way',
            'DELIVERED' => 'Delivered',
        ];
        $statusClasses = [
            'PENDING' => 'warning',
            'ACCEPTED' => 'primary',
            'ON_THE_WAY' => 'info',
            'DELIVERED' => 'success',
        ];
    @endphp

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Delivery Tracking</h5>
                </div>

                @include('admin.partials.notification')

                <form action="{{ route('admin.deliveries.web.index') }}" method="GET" class="row g-2 mb-4">
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">All statuses</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @if (($filters['status'] ?? null) === $status) selected @endif>
                                    {{ $statusLabels[$status] ?? $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="delivery_man_id" class="form-control">
                            <option value="">All delivery men</option>
                            @foreach ($deliveryMen as $deliveryMan)
                                <option value="{{ $deliveryMan->id }}" @if ((string) ($filters['delivery_man_id'] ?? '') === (string) $deliveryMan->id) selected @endif>
                                    {{ $deliveryMan->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control"
                            placeholder="Tracking, sender, receiver, phone">
                    </div>
                    <div class="col-md-1 d-flex gap-2">
                        <button type="submit" class="btn btn-secondary w-100">Filter</button>
                    </div>
                </form>

                <div class="row g-3 mb-4">
                    @foreach ($statuses as $status)
                        <div class="col-md-3">
                            <div class="border p-3 h-100">
                                <small class="text-muted">{{ $statusLabels[$status] ?? $status }}</small>
                                <h4 class="mb-0">{{ $summary[$status] ?? 0 }}</h4>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Delivery</th>
                                <th>Status</th>
                                <th>Delivery Man</th>
                                <th>Route</th>
                                <th>Customer</th>
                                <th>Assign Delivery Man</th>
                                <th>Update Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($deliveries as $delivery)
                                <tr>
                                    <td>
                                        @if ($delivery->tracking_id)
                                            <a href="{{ route('admin.deliveries.web.show', $delivery->tracking_id) }}">
                                                <strong>{{ $delivery->tracking_id }}</strong>
                                            </a>
                                        @else
                                            <strong>#{{ $delivery->id }}</strong>
                                        @endif
                                        <br>
                                        <small>{{ optional($delivery->created_at)->format('d M Y, h:i A') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $delivery->tracking_status_class }}">
                                            {{ $delivery->tracking_status_label }}
                                        </span>
                                        <br>
                                        <small>Order: {{ $delivery->status ?: '-' }}</small><br>
                                        <small>Delivery man: {{ $delivery->delivery_man_status ?: '-' }}</small>
                                    </td>
                                    <td>
                                        @if ($delivery->deliveryMan)
                                            <strong>{{ $delivery->deliveryMan->name }}</strong><br>
                                            <small>{{ $delivery->deliveryMan->phone ?? $delivery->deliveryMan->email }}</small>
                                            @if ($delivery->deliveryMan->relationLoaded('activeVehicles') && $delivery->deliveryMan->activeVehicles->count())
                                                <br>
                                                @foreach ($delivery->deliveryMan->activeVehicles as $vehicle)
                                                    <small class="d-block">{{ $vehicle->type }} {{ $vehicle->plate_number }}</small>
                                                @endforeach
                                            @endif
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>Pickup:</strong>
                                        {{ optional($delivery->pickupCity)->name ?: $delivery->pickup_city }}
                                        {{ optional($delivery->pickupCountry)->name ? ', ' . optional($delivery->pickupCountry)->name : '' }}
                                        <br>
                                        <strong>Delivery:</strong>
                                        {{ optional($delivery->deliveryCity)->name ?: $delivery->delivery_city }}
                                        {{ optional($delivery->deliveryCountry)->name ? ', ' . optional($delivery->deliveryCountry)->name : '' }}
                                        <br>
                                        <small>
                                            Current:
                                            {{ optional($delivery->currentCity)->name ?: '-' }}
                                            {{ optional($delivery->currentCountry)->name ? ', ' . optional($delivery->currentCountry)->name : '' }}
                                        </small>
                                    </td>
                                    <td>
                                        <strong>{{ $delivery->delivery_name ?: '-' }}</strong><br>
                                        <small>{{ $delivery->delivery_phone ?: '-' }}</small>
                                    </td>
                                    <td>
                                        @if ($delivery->tracking_id)
                                            <form action="{{ route('admin.deliveries.web.assign-delivery-man', $delivery->tracking_id) }}" method="POST">
                                                @csrf
                                                <select name="delivery_man_id" class="form-control form-control-sm mb-1" required>
                                                    <option value="">Select delivery man</option>
                                                    @foreach ($deliveryMen as $deliveryMan)
                                                        <option value="{{ $deliveryMan->id }}" @if ((int) $delivery->dispatcher_id === (int) $deliveryMan->id) selected @endif>
                                                            {{ $deliveryMan->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="text" name="note" class="form-control form-control-sm mb-1" placeholder="Note">
                                                <button type="submit" class="btn btn-sm btn-primary w-100">Assign</button>
                                            </form>
                                        @else
                                            <span class="text-muted">No tracking ID</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($delivery->tracking_id)
                                            <form action="{{ route('admin.deliveries.web.status', $delivery->tracking_id) }}" method="POST">
                                                @csrf
                                                <select name="status" class="form-control form-control-sm mb-1" required>
                                                    @foreach ($statuses as $status)
                                                        <option value="{{ $status }}" @if ($delivery->tracking_status === $status) selected @endif>
                                                            {{ $statusLabels[$status] ?? $status }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <select name="delivery_man_id" class="form-control form-control-sm mb-1">
                                                    <option value="">Keep delivery man</option>
                                                    @foreach ($deliveryMen as $deliveryMan)
                                                        <option value="{{ $deliveryMan->id }}" @if ((int) $delivery->dispatcher_id === (int) $deliveryMan->id) selected @endif>
                                                            {{ $deliveryMan->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="text" name="note" class="form-control form-control-sm mb-1" placeholder="Status note">
                                                <button type="submit" class="btn btn-sm btn-secondary w-100">Update</button>
                                            </form>
                                        @else
                                            <span class="text-muted">No tracking ID</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No deliveries found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $deliveries->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
