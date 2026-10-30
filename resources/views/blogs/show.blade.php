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
                <h5> {{ $batch->title }} </h5>
                <p> Slug : {{ $batch->slug }} </p>
                {{-- <p><b>Current Location : {{ $batch->location->name }}Lat: {{ $batch->location->latitude }} &nbsp;
                    &nbsp; Long: {{ $batch->location->longitude }} </b></p> --}}
                <hr>
                <h5> Description </h5>
                
                <div>{!! html_entity_decode($batch->description) !!}</div>
                <hr>
                <img src="{{ asset('images/'.$batch->image) }}" style="height: 400px;">
            </div>
        </div>
    </div>
    {{-- </div> --}}
    {{-- </div> --}}
@endsection