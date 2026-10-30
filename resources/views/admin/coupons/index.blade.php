@extends('admin.app')
@section('page_title', 'Coupons')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Coupon Management</h5>
                    <a href="{{ route('coupons.create') }}" class="btn btn-primary">Add Coupon</a>
                </div>

                @include('admin.partials.notification')

                <form action="{{ route('coupons.index') }}" method="GET" class="row g-2 mb-4">
                    <div class="col-md-5">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search code or description">
                    </div>
                    <div class="col-md-3">
                        <select name="currency" class="form-control">
                            <option value="">All currencies</option>
                            @foreach ($currencies as $currency)
                                <option value="{{ $currency }}" @if (request('currency') === $currency) selected @endif>{{ $currency }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">All statuses</option>
                            <option value="active" @if (request('status') === 'active') selected @endif>Active</option>
                            <option value="inactive" @if (request('status') === 'inactive') selected @endif>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-secondary w-100">Filter</button>
                        <a href="{{ route('coupons.index') }}" class="btn btn-light">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Discount</th>
                                <th>Currency</th>
                                <th>Min. Order</th>
                                <th>Usage</th>
                                <th>Validity</th>
                                <th>Status</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($coupons as $coupon)
                                <tr>
                                    <td>
                                        <strong>{{ $coupon->code }}</strong>
                                        @if ($coupon->description)
                                            <br><small class="text-muted">{{ $coupon->description }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($coupon->discount_type === \App\Models\Coupon::TYPE_PERCENTAGE)
                                            {{ rtrim(rtrim($coupon->discount_value, '0'), '.') }}%
                                        @else
                                            {{ $coupon->discount_value }} {{ $coupon->currency }}
                                        @endif
                                    </td>
                                    <td><span class="badge bg-info">{{ $coupon->currency }}</span></td>
                                    <td>{{ $coupon->min_order_value !== null ? $coupon->min_order_value . ' ' . $coupon->currency : '—' }}</td>
                                    <td>
                                        {{ $coupon->used_count }}
                                        @if ($coupon->usage_limit !== null)
                                            / {{ $coupon->usage_limit }}
                                            <br><small class="text-muted">{{ $coupon->remainingUses() }} left</small>
                                        @else
                                            <small class="text-muted">/ ∞</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small>
                                            <strong>From:</strong> {{ optional($coupon->starts_at)->format('d M Y, H:i') ?? 'Anytime' }}<br>
                                            <strong>Until:</strong> {{ optional($coupon->expires_at)->format('d M Y, H:i') ?? 'No expiry' }}
                                        </small>
                                    </td>
                                    <td>
                                        @if (! $coupon->is_active)
                                            <span class="badge bg-secondary">Inactive</span>
                                        @elseif ($coupon->isExpired())
                                            <span class="badge bg-danger">Expired</span>
                                        @elseif (! $coupon->hasRemainingUses())
                                            <span class="badge bg-warning text-dark">Used up</span>
                                        @elseif (! $coupon->hasStarted())
                                            <span class="badge bg-info">Scheduled</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('coupons.edit', $coupon->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                                        <form action="{{ route('coupons.destroy', $coupon->id) }}" method="POST" class="d-inline-block"
                                            onsubmit="return confirm('Delete this coupon?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No coupons created yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $coupons->links() }}
            </div>
        </div>
    </div>
@endsection
