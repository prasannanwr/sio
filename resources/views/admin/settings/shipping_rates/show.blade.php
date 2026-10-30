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
                        <a href="{{ route('shipping_rates.index') }}" class="btn btn-danger float-right"><i
                                class="fa fa-times"></i>Exit</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <h5>Viewing Rate / Route [{{ $rate->name }}]</h5>
                        <p>
                            <h6>Details</h6>
                            Price(&euro;): {{$rate->price}} <br>
                            Dimensions(cm): w - {{$rate->width}}, l - {{$rate->length}},  h - {{$rate->height}}<br>
                            Weight(kg): Min - {{$rate->weight_start}}, Max - {{$rate->weight_end}}<br>
                            Spec.: {{$rate->desc}}
                        </p>
                        <p>
                            <h6>Origin</h6>
                            Lat: {{ $rate->origin->latitude }} &nbsp; &nbsp; Long: {{ $rate->origin->longitude }}
                        </p>
                        <p>
                            <h6>Destination</h6>
                            Lat: {{ $rate->destination->latitude }} &nbsp; &nbsp; Long: {{ $rate->destination->longitude }}
                        </p>
                        <div id="map" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    // Get latitude and longitude values for the two locations from the server
    var latitude1 = {{ $rate->origin->latitude }};
    var longitude1 = {{ $rate->origin->longitude }};
    var latitude2 = {{ $rate->destination->latitude }};
    var longitude2 = {{ $rate->destination->longitude }};

    // Initialize the map
    var map = L.map('map').setView([latitude1, longitude1], 10);

    // Add OpenStreetMap as the base layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add markers for the two locations
    var marker1 = L.marker([latitude1, longitude1]).addTo(map).bindPopup('{{$rate->origin->name}}')
        .openPopup();
    var marker2 = L.marker([latitude2, longitude2]).addTo(map).bindPopup('{{$rate->destination->name}}')
        .openPopup();

    // Create a polyline between the two markers
    var polyline = L.polyline([
        [latitude1, longitude1],
        [latitude2, longitude2]
    ]).addTo(map);

    // Fit the map to the bounds of the polyline
    map.fitBounds(polyline.getBounds());
</script>

@endsection
