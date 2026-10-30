@extends('admin.app')
@section('page_title', 'Edit Coupon')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Edit Coupon</h5>
                    <a href="{{ route('coupons.index') }}" class="btn btn-primary">All Coupons</a>
                </div>

                @include('admin.partials.notification')

                @include('admin.coupons._form', [
                    'action' => route('coupons.update', $coupon->id),
                    'method' => 'PUT',
                    'buttonText' => 'Update Coupon',
                ])
            </div>
        </div>
    </div>
@endsection
