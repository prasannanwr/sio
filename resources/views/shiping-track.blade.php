<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ env('APP_NAME') }} | @yield('page_title')</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Shipping in italy" name="keywords">
    <meta content="Shipping in italy" name="description">

    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#218bff">
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('landing2/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('landing2/css/style.css') }}" rel="stylesheet">
    {{-- https://github.com/bitjson/qr-code --}}
    <script src="https://unpkg.com/@bitjson/qr-code@1.0.2/dist/qr-code.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        @media only screen and (max-width: 767px) {

            .table>tbody>tr>td,
            .table>tbody>tr>th,
            .table>tfoot>tr>td,
            .table>tfoot>tr>th,
            .table>thead>tr>td,
            .table>thead>tr>th {
                display: block;
                min-width: 100% !important;
            }

            .table>thead>tr>th {
                display: none;
            }

            .table .filterBody span {
                display: block;
            }
        }
    </style>
</head>

<body>
    <!-- Topbar Start -->
    @include('navbar')
    <!-- Navbar End -->


    <!-- Header Start -->
    <div class="jumbotron jumbotron-fluid mb-5">
        <div class="container text-center py-5">
            <h1 class="text-white display-3">Track Order</h1>
            <div class="d-inline-flex align-items-center text-white">
                <p class="m-0"><a class="text-white" href="">Home</a></p>
                <i class="fa fa-circle px-3"></i>
                <p class="m-0">Track Order</p>
            </div>
        </div>
    </div>
    <!-- Header End -->

    {{-- content --}}

    {{-- /content end --}}
    <!-- Contact Start -->
    <div class="card container">
        <!-- /.card-header -->
        <div class="card-body">
            <div id="print_section">
                <h5>Tracking ID: [{{ $order->tracking_id }}]</h5>
                <div class="table-reponsive">
                    <table class="table table-bordered table-sm">
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

                                    {{-- <script>
                                        document.getElementById('qr1').addEventListener('codeRendered', () => {
                                            document.getElementById('qr1').animateQRCode('MaterializeIn');
                                        });
                                    </script> --}}
                                </th>
                                <th colspan="2">
                                    <img src="{{ asset('admin_assets/assets/images/logos/favicon.png') }}"
                                        style="width: 100%" slot="icon" />
                                </th>
                            </tr>
                            <tr>
                                <th>Origin</th>
                                <td>
                                    {{ $order->pickup_location->postcode }} -
                                    {{ $order->pickup_location->name }}
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
                                    <b>Estimated Transit Time(days):</b> {{ $order->shipping_rate->transit_days }}
                                    <br>
                                    <b>Dilevery at</b>: {{ $order->delivery_time }}
                                </td>
                            </tr>
                            {{-- <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-secondary">{{ $order->status }}</span>
                                    @if ($order->status == 'unpaid')
                                        <a href="{{ route('payment.summary', ['order_id' => $order->id]) }}"
                                            class="btn btn-primary">$ Pay</a>
                                    @endif

                                    @if ($order->status && request()->get('mode') != '0')
                                        <form action="{{ route('cancelOrder') }}" method="post" class="form-inline">
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
                            </tr> --}}
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
                                <td>
                                    {{ $order->pickup_phone }},
                                    {{ $order->pickup_phone_alt }}
                                </td>
                                <th>Reciever Phone</th>
                                <td>
                                    {{ $order->delivery_phone }},
                                    {{ $order->delivery_phone_alt }}
                                </td>
                            </tr>
                            <tr>
                                <th>Sender Address </th>
                                <td>
                                    {{ $order->pickup_address1 }}
                                    <br>
                                    {{ $order->pickup_address2 }}
                                </td>
                                <th>Reciever Address </th>
                                <td>
                                    {{ $order->delivery_address1 }}
                                    <br>
                                    {{ $order->delivery_address2 }}
                                </td>
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
                    <table class="table table-bordered table-sm">
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
            </div>
            <hr>
            <button class="btn btn-primary" onclick="PrintElem('print_section')">Print Label</button>
            <hr>
            <h5>Geolocation Data</h5>
            <hr>
            <div id="map" style="height: 400px;"></div>
        </div>
    </div>
    <!-- Contact End -->


    <!-- Footer Start -->
    @include('footer')
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
    <script>
        function PrintElem(elem) {
            var mywindow = window.open('', 'PRINT', 'height=400,width=600');

            mywindow.document.write('<html><head><title>' + document.title + '</title>');
            mywindow.document.write(
                `<link rel="stylesheet" href="{{ asset('admin_assets/assets/css/styles.min.css') }}" />`);
            mywindow.document.write('</head><body>');
            mywindow.document.write(
                `<link rel="preload" href="https://unpkg.com/@bitjson/qr-code@1.0.2/dist/qr-code.js" as="script">`);
            var script = document.createElement('script');
            script.src = 'https://unpkg.com/@bitjson/qr-code@1.0.2/dist/qr-code.js';
            mywindow.document.head.appendChild(script);

            mywindow.document.write(document.getElementById(elem).innerHTML);
            mywindow.document.write('</body></html>');

            mywindow.document.close(); // IE >= 10

            // Wait for the window and its contents to load
            mywindow.onload = function() {
                mywindow.focus(); // Set focus for IE
                mywindow.print();
                // Optional: Uncomment the line below to close the window after printing
                // mywindow.close();
            };

            return true;
        }
    </script>
</body>

</html>
