@extends('admin.app')
@section('page_title', 'Sub Admin Edit')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Sub Admin Edit <a href="{{ route('subAdmin.index') }}"
                        class="btn btn-primary mb-3">All Sub Admin</a></h5>

                @include('admin.partials.notification')
                <hr>
                <form action="{{ route('subAdmin.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Name </label>
                        <input type="text" name="name" class="form-control" value="{{$user->name}}" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Phone No</label>
                        <input type="text" name="mobile" class="form-control" required value="{{$user->mobile}}">
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required value="{{$user->email}}">
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required value="{{$user->pass}}">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Address</label>
                        <textarea name="address" class="form-control">{{$user->address}}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label"> Menu Permission </label>
                        {{-- {{ dd($menus) }} --}}
                        <ul  class="list-group">
                            @foreach ($menus as $menu)
                            <li  class="list-group-item"> 
                                <label for="{{ $menu->id }}" style="cursor: pointer;">
                                    <input type="checkbox" 
                                    @if($menu->status==1)
                                    checked
                                    @endif
                                    name="menus[]" value="{{ $menu->id }}" id="{{ $menu->id }}"> {{  __('admin.menus.'.$menu->name) }}
                                </label>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    
@endsection
