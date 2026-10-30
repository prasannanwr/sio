@extends('admin.app')
@section('page_title', 'Orders')
@section('content')
    <script src="https://unpkg.com/@bitjson/qr-code@1.0.2/dist/qr-code.js"></script>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">My Orders</h5>
                @include('admin.partials.notification')
                <hr>
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route('orders.index') }}" class="btn btn-danger float-right"><i
                                class="fa fa-times"></i>Exit</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <h5> Tracking ID: [{{ $order->tracking_id }}]</h5>
                        <div class="table-reponsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        {{-- https://github.com/bitjson/qr-code --}}
                                        <th colspan="2">
                                            <qr-code id="qr1"
                                                contents="{{ route('track.shipping', $order->tracking_id) }}"
                                                module-color="#5D87FF" position-ring-color="#5A6A85"
                                                position-center-color="#539BFF" mask-x-to-y-ratio="1.2"
                                                style="
                                              width: 300px;
                                              height: 300px;
                                              margin: 2em auto;
                                              background-color: #fff;
                                            ">
                                                {{-- <img src="{{asset('admin_assets/assets/images/logos/favicon.png')}}" style="width: 100%" slot="icon" /> --}}
                                            </qr-code>

                                            <script>
                                                document.getElementById('qr1').addEventListener('codeRendered', () => {
                                                    document.getElementById('qr1').animateQRCode('MaterializeIn');
                                                });
                                            </script>
                                        </th>
                                        <th colspan="2">
                                            <img src="{{ asset('admin_assets/assets/images/logos/favicon.png') }}"
                                                style="width: 100%" slot="icon" />
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Origin</th>
                                        <td>
                                            {{ $order->pickup_location->postcode }} - {{ $order->pickup_location->name }}
                                            [Lat: {{ $order->pickup_location->longitude }}, Long:
                                            {{ $order->pickup_location->longitude }}]
                                            <br>
                                            Picked up at: {{ $order->pickup_time }}
                                        </td>
                                        <th>Destination / Current Location </th>
                                        <td>
                                            <b>Destination</b>: {{ $order->delivery_location->postcode }} -
                                            {{ $order->delivery_location->name }} [Lat:
                                            {{ $order->delivery_location->longitude }}, Long:
                                            {{ $order->delivery_location->longitude }}]
                                            <br>
                                            <br>
                                            <b>Current Location</b> : {{ $order->current_location->postcode }} -
                                            {{ $order->current_location->name }} [Lat:
                                            {{ $order->current_location->longitude }}, Long:
                                            {{ $order->current_location->longitude }}]
                                            <br>
                                            <br>
                                            <b>Dilevery at</b>: {{ $order->delivery_time }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span class="badge bg-secondary">{{ $order->status }}</span>
                                            {{-- @if ($order->status == 'unpaid')
                                                <a href="{{ route('payment.summary', ['order_id' => $order->id]) }}"
                                                    class="btn btn-primary">$ Pay</a>
                                            @endif --}}

                                            @if ($order->status && request()->get('mode') != '0')
                                                <form action="{{ route('cancelOrder') }}" method="post">
                                                    @csrf
                                                    <input type="hidden" value="{{ $order->id }}" name="order_id">
                                                    <button class="btn btn-danger"
                                                        onclick="return confirm('Are you sure you wish to cancel this order')">x
                                                        Cancel Order</button>
                                                </form>
                                            @endif
                                        </td>
                                        <th>Price(&euro;)</th>
                                        <td>{{ $order->shipping_rate->price }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sender Name</th>
                                        <td>{{ $order->pickup_name }}</td>
                                        <th>Reciever Name</th>
                                        <td>{{ $order->delivery_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sender Email</th>
                                        <td>{{ $order->pickup_email }}</td>
                                        <th>Reciever Email</th>
                                        <td>{{ $order->delivery_email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sender Phone</th>
                                        <td>{{ $order->pickup_phone }}</td>
                                        <th>Reciever Name</th>
                                        <td>{{ $order->delivery_phone }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sender Phone Alt.</th>
                                        <td>{{ $order->pickup_phone_alt }}</td>
                                        <th>Reciever Phone Alt.</th>
                                        <td>{{ $order->delivery_phone_alt }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sender Address 1</th>
                                        <td>{{ $order->pickup_address1 }}</td>
                                        <th>Reciever Address 1</th>
                                        <td>{{ $order->delivery_address1 }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sender Address 2</th>
                                        <td>{{ $order->pickup_address2 }}</td>
                                        <th>Reciever Name</th>
                                        <td>{{ $order->delivery_address2 }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sender Zip/ Postcode</th>
                                        <td>{{ $order->pickup_zip }}</td>
                                        <th>Reciever Zip/ Postcode</th>
                                        <td>{{ $order->delivery_zip }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sender City</th>
                                        <td>{{ $order->pickup_city }}</td>
                                        <th>Reciever City</th>
                                        <td>{{ $order->delivery_city }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sender State/ Province</th>
                                        <td>{{ $order->pickup_state }}</td>
                                        <th>Reciever State/ Province</th>
                                        <td>{{ $order->delivery_state }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sender Country / Region</th>
                                        <td>{{ $order->pickup_country }}</td>
                                        <th>Reciever Country / Region</th>
                                        <td>{{ $order->delivery_country }}</td>
                                    </tr>
                                    <tr>
                                        <th>Condition of Goods</th>
                                        <td>{{ $order->cond_of_goods }}</td>
                                        <th>Value of goods</th>
                                        <td>{{ $order->val_of_goods }} [{{ $order->val_cur }}]</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <h5>Order Contents</h5>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <th>Package Type</th>
                                    <th>length(cm)</th>
                                    <th>Width(cm)</th>
                                    <th>Height(cm)</th>
                                    <th>Weight(Kg)</th>
                                    <th>Count/qty</th>
                                </thead>
                                <tbody id="items_list">
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td>
                                                {{ $item->type }}
                                            </td>
                                            <td>
                                                {{ $item->length }}
                                            </td>
                                            <td>
                                                {{ $item->width }}
                                            </td>
                                            <td>
                                                {{ $item->height }}
                                            </td>
                                            <td>
                                                {{ $item->weight }}
                                            </td>
                                            <td>
                                                {{ $item->qty }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <h5>Geolocation Data</h5>
                        <hr>
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
        var latitude1 = {{ $order->pickup_location->latitude }};
        var longitude1 = {{ $order->pickup_location->longitude }};
        var latitude2 = {{ $order->delivery_location->latitude }};
        var longitude2 = {{ $order->delivery_location->longitude }};
        var latitude3 = {{ $order->current_location->latitude }};
        var longitude3 = {{ $order->current_location->longitude }};

        // Initialize the map
        var map = L.map('map').setView([latitude1, longitude1], 5);

        // Add OpenStreetMap as the base layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add markers for the two locations
        var marker1 = L.marker([latitude1, longitude1]).addTo(map).bindPopup('{{ $order->pickup_location->name }}');
        var marker2 = L.marker([latitude2, longitude2]).addTo(map).bindPopup('{{ $order->delivery_location->name }}');
        var marker3 = L.marker([latitude3, longitude3]).addTo(map).bindPopup(
            '{{ $order->current_location->name }} - Cur. loc.').openPopup();;

        // Create a polyline between the two markers
        // Create a blue polyline between origin and current location
        var bluePolyline = L.polyline([
            [latitude1, longitude1],
            [latitude3, longitude3]
        ], {
            color: 'blue'
        }).addTo(map);

        // Create a gray polyline between cur location and destination
        var grayPolyline = L.polyline([
            [latitude3, longitude3],
            [latitude2, longitude2]
        ], {
            color: 'gray'
        }).addTo(map);

        // Fit the map to the bounds of the polyline
        map.fitBounds([latitude1, longitude1], [latitude2, longitude2]);
    </script>

@endsection
