@extends('admin.app')
@section('page_title', 'Sub Admin Entry')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold">{{ __('admin.sub_admin.add_button') }}</h5>
                    <a href="{{ route('subAdmin.index') }}" class="btn btn-primary">{{ __('admin.sub_admin.name1') }}</a>
                </div>
                

                @include('admin.partials.notification')
                <hr>
                <form action="{{ route('subAdmin.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Phone No</label>
                        <input type="text" name="mobile" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Address</label>
                        <textarea name="address" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label"> Menu Permission </label>
                        {{-- {{ dd($menus) }} --}}
                        <ul  class="list-group">
                            @foreach ($menus as $menu)
                            <li  class="list-group-item"> 
                                <label for="{{ $menu->id }}" style="cursor: pointer;">
                                    <input type="checkbox" name="menus[]" value="{{ $menu->id }}" id="{{ $menu->id }}"> {{ __('admin.menus.'.$menu->name) }}
                                </label>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="submit" class="btn btn-primary"> {{ __('admin.sub_admin.submit_button') }} </button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    
@endsection
