@extends('admin.app')
@section('page_title', 'Location')
@section('content')
    <div class="container-fluid">
        {{-- <div class="card"> --}}
        {{-- <div class="card-body"> --}}
        {{-- <h5 class="card-title fw-semibold mb-4">Settings</h5> --}}
        {{-- @include('dispatcher.settings.nav') --}}
        {{-- <hr> --}}
        <div class="card">
            <div class="card-header">
                <h5>Show Blog </h5>
                {{-- <a href="{{ route('batches.index') }}" class="btn btn-danger float-right"><i
                                class="fa fa-times"></i>Exit</a> --}}
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <td> Name </td>
                        <td> {{ $user->name }} </td>
                    </tr>
                    <tr>
                        <td> Phone No </td>
                        <td> {{ $user->mobile }} </td>
                    </tr>
                    <tr>
                        <td> Email </td>
                        <td> {{ $user->email }} </td>
                    </tr>
                    <tr>
                        <td> Password </td>
                        <td> {{ $user->pass }} </td>
                    </tr>
                    <tr>
                        <td> Address </td>
                        <td> {{ $user->address }} </td>
                    </tr>
                </table>
                <hr>
                <div class="mb-3">
                    <label for="image" class="form-label"> Menu Permission </label>
                    {{-- {{ dd($menus) }} --}}
                    <ul  class="list-group">
                        @foreach ($menus as $menu)
                        <li  class="list-group-item"> 
                            <label for="{{ $menu->menu_id }}" style="cursor: pointer;">
                                <input type="checkbox" checked value="{{ $menu->menu_id }}" id="{{ $menu->menu_id }}"> {{  __('admin.menus.'.$menu->name) }}
                            </label>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            </div>
        </div>
    </div>
    {{-- </div> --}}
    {{-- </div> --}}
@endsection