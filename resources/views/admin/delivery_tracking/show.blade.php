@extends('admin.app')
@section('page_title', 'Delivery Tracking Details')
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
                    <h5 class="card-title fw-semibold mb-0">Delivery {{ $delivery->tracking_id }}</h5>
                    <a href="{{ route('admin.deliveries.web.index') }}" class="btn btn-primary">All Deliveries</a>
                </div>

                @include('admin.partials.notification')

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="border p-3 h-100">
                            <small class="text-muted">Tracking Status</small><br>
                            <span class="badge bg-{{ $delivery->tracking_status_class }}">{{ $delivery->tracking_status_label }}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border p-3 h-100">
                            <small class="text-muted">Delivery Man</small>
                            <h6 class="mb-1">{{ optional($delivery->deliveryMan)->name ?: 'Not assigned' }}</h6>
                            <small>{{ optional($delivery->deliveryMan)->phone ?? optional($delivery->deliveryMan)->email }}</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border p-3 h-100">
                            <small class="text-muted">Order Status</small>
                            <h6 class="mb-0">{{ $delivery->status ?: '-' }}</h6>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border p-3 h-100">
                            <small class="text-muted">Accepted At</small>
                            <h6 class="mb-0">{{ $delivery->dispatcher_date ? \Carbon\Carbon::parse($delivery->dispatcher_date)->format('d M Y') : '-' }}</h6>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border p-3 h-100">
                            <small class="text-muted">Delivered At</small>
                            <h6 class="mb-0">{{ $delivery->delivery_time ? \Carbon\Carbon::parse($delivery->delivery_time)->format('d M Y') : '-' }}</h6>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="border p-3 h-100">
                            <h6 class="fw-semibold">Pickup</h6>
                            <p class="mb-1">{{ $delivery->pickup_name ?: '-' }} / {{ $delivery->pickup_phone ?: '-' }}</p>
                            <p class="mb-0">
                                {{ $delivery->pickup_address1 }}
                                {{ optional($delivery->pickupCity)->name ?: $delivery->pickup_city }}
                                {{ optional($delivery->pickupCountry)->name ? ', ' . optional($delivery->pickupCountry)->name : '' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border p-3 h-100">
                            <h6 class="fw-semibold">Delivery</h6>
                            <p class="mb-1">{{ $delivery->delivery_name ?: '-' }} / {{ $delivery->delivery_phone ?: '-' }}</p>
                            <p class="mb-0">
                                {{ $delivery->delivery_address1 }}
                                {{ optional($delivery->deliveryCity)->name ?: $delivery->delivery_city }}
                                {{ optional($delivery->deliveryCountry)->name ? ', ' . optional($delivery->deliveryCountry)->name : '' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="border p-3 h-100">
                            <h6 class="fw-semibold">Assign Delivery Man</h6>
                            <form action="{{ route('admin.deliveries.web.assign-delivery-man', $delivery->tracking_id) }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <select name="delivery_man_id" class="form-control" required>
                                        <option value="">Select delivery man</option>
                                        @foreach ($deliveryMen as $deliveryMan)
                                            <option value="{{ $deliveryMan->id }}" @if ((int) $delivery->dispatcher_id === (int) $deliveryMan->id) selected @endif>
                                                {{ $deliveryMan->name }} @if ($deliveryMan->phone) ({{ $deliveryMan->phone }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <input type="text" name="note" class="form-control" placeholder="Assignment note">
                                </div>
                                <button type="submit" class="btn btn-primary">Assign Delivery Man</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border p-3 h-100">
                            <h6 class="fw-semibold">Update Tracking Status</h6>
                            <form action="{{ route('admin.deliveries.web.status', $delivery->tracking_id) }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <select name="status" class="form-control" required>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}" @if ($delivery->tracking_status === $status) selected @endif>
                                                {{ $statusLabels[$status] ?? $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <select name="delivery_man_id" class="form-control">
                                        <option value="">Keep delivery man</option>
                                        @foreach ($deliveryMen as $deliveryMan)
                                            <option value="{{ $deliveryMan->id }}" @if ((int) $delivery->dispatcher_id === (int) $deliveryMan->id) selected @endif>
                                                {{ $deliveryMan->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <input type="text" name="note" class="form-control" placeholder="Status note">
                                </div>
                                <button type="submit" class="btn btn-secondary">Update Status</button>
                            </form>
                        </div>
                    </div>
                </div>

                <h5 class="fw-semibold mb-3">Tracking Timeline</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Delivery Man</th>
                                <th>Performed By</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($delivery->trackingEvents->sortByDesc('created_at') as $event)
                                @php
                                    $eventStatus = $event->new_delivery_man_status === \App\Constraints\DeliveryManStatus::DELIVERED || $event->new_status === \App\Constraints\OrderStatus::DELIVERED
                                        ? 'DELIVERED'
                                        : ($event->new_delivery_man_status === \App\Constraints\DeliveryManStatus::ON_THE_WAY || $event->new_status === \App\Constraints\OrderStatus::IN_TRANSIT
                                            ? 'ON_THE_WAY'
                                            : ($event->delivery_man_id ? 'ACCEPTED' : 'PENDING'));
                                @endphp
                                <tr>
                                    <td>{{ optional($event->created_at)->format('d M Y, h:i A') }}</td>
                                    <td><span class="badge bg-{{ $statusClasses[$eventStatus] ?? 'secondary' }}">{{ $statusLabels[$eventStatus] ?? $eventStatus }}</span></td>
                                    <td>{{ optional($event->deliveryMan)->name ?: '-' }}</td>
                                    <td>{{ optional($event->performedBy)->name ?: '-' }}</td>
                                    <td>{{ $event->note ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td>{{ optional($delivery->created_at)->format('d M Y, h:i A') }}</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>{{ optional($delivery->deliveryMan)->name ?: '-' }}</td>
                                    <td>-</td>
                                    <td>Delivery created.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
