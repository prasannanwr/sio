@extends('admin.app')
@section('page_title', 'Location')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Settings</h5>
                @include('admin.settings.nav')
                <hr>
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route('locations.index') }}" class="btn btn-danger float-right"><i
                                class="fa fa-times"></i>Exit</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <h5>Viewing Location [{{ $location->name }}]</h5>
                        <p>Lat: {{ $location->latitude }} &nbsp; &nbsp; Long: {{ $location->longitude }} &nbsp; &nbsp; Country: {{ $location->country_code }}</p>
                        <div id="map" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    // Get latitude and longitude values from the server
    var latitude = {{ $location->latitude }};
    var longitude = {{ $location->longitude }};

    // Initialize the map
    var map = L.map('map').setView([latitude, longitude], 10); // 16 is the zoom level

    // Add OpenStreetMap as the base layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add a marker at the specified location
    L.marker([latitude, longitude]).addTo(map)
        .bindPopup('{{$location->name}}')
        .openPopup();
</script>
@endsection
