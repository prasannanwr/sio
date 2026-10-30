@php
    use App\Models\User;
    use App\Models\Country;
    use App\Models\City;
@endphp
@extends('admin.app')
@section('page_title', 'Orders')
@section('content')
    {{-- https://github.com/bitjson/qr-code --}}
    <script src="https://unpkg.com/@bitjson/qr-code@1.0.2/dist/qr-code.js"></script>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-semibold mb-4">My Orders</h5>
                    @if ($order->batch_id == null && $order->status == 'placed')
                        <a class="btn btn-primary pbn-sn"
                            href="{{ route('batches.create', ['order_id' => $order->id]) }}">Assigned Batch</a>
                    @endif
                </div>
                @include('admin.partials.notification')
                <hr>
                <div class="card" style="width:559px;">
                    <div class="card-header">
                        {{-- <a href="{{ route('orders.index') }}" class="btn btn-danger float-right"><i
                                class="fa fa-times"></i>Exit</a> --}}
                        <h5><b>For Packege</b></h5>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        
                        <div id="print_section_header">

                            <table class="w-full" style="width:100%;">
                                <tr>
                                    <th> <p>Tracking ID <br> {{ $order->tracking_id }}</p> </th>
                                    <th> <p style="text-align:center;">G.SH CODE<br> {{ $order->sh_id }}</p> </th>
                                    <th class="text-right"> <p class="text-right" style="text-align: right;">Date <br> {{ substr($order->created_at,0,10) }}</p> </th>
                                </tr>
                            </table>
                            <table class="table table-bordered table-sm">
                                <tbody>
                                    <tr>
                                        {{-- <th colspan="2">
                                            <div style="">
                                               
                                            </div>
                                        </th> --}}
                                        <th colspan="4" style="text-align: center;">
                                            <img src="{{ asset('admin_assets/assets/images/logos/favicon.png') }}"
                                            style="width: 30%;height:104px;margin-top:-20px;" slot="icon"/>
                                            <svg id="barcode"></svg><br>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <button type="button" class="btn btn-primary"  onclick="PrintElemHeader('print_section_header')" > Print </button>


                        <div id="print_section" style="margin-top: 100px;">

                            <table class="w-full" style="width:100%;">
                                <tr>
                                    <th> <p>Tracking ID <br> {{ $order->tracking_id }}</p> </th>
                                    <th> <p style="text-align:center;">G.SH CODE<br> {{ $order->sh_id }}</p> </th>
                                    <th class="text-right"> <p class="text-right" style="text-align: right;">Date <br> {{ substr($order->created_at,0,10) }}</p> </th>
                                </tr>
                            </table>
                            

                            <div class="table-reponsive">
                                <table class="table table-bordered table-sm">
                                    <tbody>
                                        <tr>
                                            {{-- <th colspan="2">
                                                <div style="">
                                                   
                                                </div>
                                            </th> --}}
                                            <th colspan="4" style="text-align: center;">
                                                <img src="{{ asset('admin_assets/assets/images/logos/favicon.png') }}"
                                                style="width: 30%;height:104px;margin-top:-20px;" slot="icon"/>
                                                <svg id="barcode"></svg><br>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>


                                <table class="table table-bordered table-sm sender-table">
                                        <tr>
                                            <td class="" colspan="4">
                                                <h5><b>For Custommer</b></h5>
                                            </td>
                                        </tr>
                                        
                                        <tr>
                                            <th colspan="4" style="background:#8080804f;"> 
                                                <h5><b> Sender Information </b></h5>
                                                 </th>
                                        </tr>
                                        <tr>
                                            <th style="width:150px;">Sender Name</th>
                                            <td>{{ $order->pickup_name }}</td>
                                        </tr>
                                       
                                        <tr>
                                            <th>Sender Email</th>
                                            <td>{{ $order->pickup_email }}</td>
                                            
                                        </tr>
                                       
                                        <tr>
                                            <th>Sender Phone</th>
                                            <td>
                                                {{ $order->pickup_phone }},
                                                {{ $order->pickup_phone_alt }}
                                            </td>
                                        </tr>
                                       
                                        <tr>
                                            <th>Sender Address </th>
                                            <td>
                                                {{ $order->pickup_address1 }}
                                                <br>
                                                {{ $order->pickup_address2 }}
                                            </td>
                                            
                                        </tr>
                                        <tr>
                                            <th>Sender Zip/ Postcode</th>
                                            <td>{{ $order->pickup_zip }}</td>
                                        </tr>
                                       
                                        <tr>
                                            <th>Sender City</th>
                                            <td>{{ $order->pickup_city }}</td>
                                        </tr>
                                        <tr>
                                            <th>Sender State/ Province</th>
                                            <td>{{ $order->pickup_state }}</td>
                                        </tr>
                                       
                                        <tr>
                                            <th>Sender Country / Region</th>
                                            <td>{{ $order->pickup_country }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <hr>
                               
                                {{-- receiver-table --}}
                                <table class="table table-bordered table-sm receiver-table">
                                    <tbody>
                                    <tr>
                                        <th colspan="4" style="background:#8080804f;"> 
                                            <h5><b> Receiver Information </b></h5>
                                             </th>
                                    </tr>
                                    <tr>
                                        <th>Reciever Name</th>
                                        <td>{{ $order->delivery_name }}</td>
                                    </tr>
                                       
                                    <tr>
                                        <th>Reciever Email</th>
                                        <td>{{ $order->delivery_email }}</td>
                                    </tr>
                                    <tr>
                                       
                                        <th>Reciever Phone</th>
                                        <td>
                                            {{ $order->delivery_phone }},
                                            {{ $order->delivery_phone_alt }}
                                        </td>
                                    </tr>
                                       
                                    <tr>
                                        <th>Reciever Address </th>
                                        <td>
                                            {{ $order->delivery_address1 }}
                                            <br>
                                            {{ $order->delivery_address2 }}
                                        </td>
                                    </tr>
                                   
                                    <tr>
                                        <th>Reciever Zip/ Postcode</th>
                                        <td>{{ $order->delivery_zip }}</td>
                                        @php
                                            $id = (int) $order->delivery_city;
                                            $city = City::find($id)?->name;
                                        @endphp
                                        </tr>
                                       
                                        <tr>
                                        <th>Reciever City</th>
                                        <td>{{ $city }}</td>
                                    </tr>
                                   
                                    <tr>
                                        
                                        <th>Reciever State/ Province</th>
                                        <td>{{ $order->delivery_state }}</td>
                                        @php
                                            $id = (int) $order->delivery_country;
                                            $country = Country::find($id)?->name;
                                        @endphp
                                        </tr>
                                       
                                        <tr>
                                        <th>Reciever Country / Region</th>
                                        <td>{{ $country }}</td>
                                    </tr>
                                    <tr>
                                        <th>Condition of Goods</th>
                                        <td>{{ $order->cond_of_goods }}</td>
                                    </tr>
                                       
                                    <tr>
                                        <th class="commission">Commission</th>
                                        <td class="commission">{{ number_format($order->val_of_goods * 0.015, 2) }} €</td>
                                    </tr>
                                </tbody>
                            </table>

                            </div>
 
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <tr>
                                        <th colspan="6"> <h5>Order Contents</h5> </th>
                                    </tr>
                                    <tr>
                                        <th>Package Type</th>
                                        <th>length <br> (cm)</th>
                                        <th>Width <br> (cm)</th>
                                        <th>Height <br> (cm)</th>
                                        <th>Weight <br> (Kg)</th>
                                        <th>Count/qty</th>
                                    </tr>
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
                                
                                <div style="margin-top: 250px;">
                                <table style="width:100%; border-top:2px solid #707070;margin-top:20px">
                                    <tr>
                                        <td> Thank you for choosing  Siopay Logistics! </td>
                                        {{-- <td> <img src="{{ asset('admin_assets/assets/images/logos/favicon.png') }}"
                                            style="width: 120px;float:right;" slot="icon" /> </td> --}}
                                    </tr>
                                </table>

                                <table style="width:100%;margin-top:10px">
                                    <tr>
                                        <th style="border-right:2px solid #3a3a3a;"> +3909681916024 </th>
                                        <th style="border-right:2px solid #3a3a3a;text-align:center;"> support@sioshipping.eu </th>
                                        <th style="text-align: right;"> <a href="https://www.sioshipping.eu/">sioshipping.eu</a> </th>
                                    </tr>
                                </table>
                            </div>

                            </div>
                        </div>
                        <hr>
                        <button class="btn btn-primary" onclick="PrintElem('print_section')">For Company</button>
                        <button class="btn btn-primary ms-3" onclick="PrintElem2('print_section')">For Custommer</button>
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
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"
        integrity="sha256-UuAyU0w/mJdq2Vy4wguvgO0MyD1CWQYCqM8dsW4uIu0=" crossorigin="anonymous"></script>
    <script>

        $(document).ready(function() {
            let trakingId = "WAYBILL {{ $order->tracking_id }}"
            let totalWidth = 300; // মোট প্রস্থ (px)
            let charCount = trakingId.length; // ট্র্যাকিং আইডির অক্ষরের সংখ্যা
            let barWidth = totalWidth / (charCount * 11); // প্রতিটি বার এর প্রস্থ
            JsBarcode("#barcode", trakingId, {
                format: "CODE128",
                width: barWidth,
            });
        });

        // Get latitude and longitude values for the two locations from the server
        var latitude1 = {{ $order->pickupCity->latitude }};
        var longitude1 = {{ $order->pickupCity->longitude }};
        var latitude2 = {{ $order->deliveryCity->latitude }};
        var longitude2 = {{ $order->deliveryCity->longitude }};
        var latitude3 = {{ $order->currentCity->latitude }};
        var longitude3 = {{ $order->currentCity->longitude }};

        // Initialize the map
        var map = L.map('map').setView([latitude1, longitude1], 5);

        // Add OpenStreetMap as the base layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add markers
        // for the two locations
        var marker1 = L.marker([latitude1, longitude1]).addTo(map).bindPopup('{{ $order->pickupCity->name }}');
        var marker2 = L.marker([latitude2, longitude2]).addTo(map).bindPopup('{{ $order->deliveryCity->name }}');
        var marker3 = L.marker([latitude3, longitude3]).addTo(map).bindPopup(
            '{{ $order->currentCity->name }} - Cur. loc.').openPopup();;

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


        map.fitBounds([latitude1, longitude1], [latitude2, longitude2]);
    </script>
    <script>
        function PrintElemHeader(elem) {
            
            var mywindow = window.open('', 'PRINT', 'height=400,width=600');

            mywindow.document.write('<html><head><title>' + document.title + '</title>');
                mywindow.document.write(`
    <style>
        @media print {
            @page:first {
                size: 200px auto; /* প্রথম পৃষ্ঠার উচ্চতা 400px */
            }
            @page {
                size: A5; 
                margin: 0; 
                padding: 0;
                margin-top:50px;
            }
            body {
                font-family: Arial, sans-serif; 
                margin: 0;
            }
            .receiver-table{
                margin-top:400px;
            }
            .sender-table{
            margin-top:600px;
            }
        }
    </style>
`);
            mywindow.document.write(
                `<link rel="stylesheet" href="{{ asset('admin_assets/assets/css/styles.min.css') }}" />`);
            mywindow.document.write('</head><body class="px-5 py-4">');
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
    <script>
        function PrintElem(elem) {
            
            var mywindow = window.open('', 'PRINT', 'height=400,width=600');

            mywindow.document.write('<html><head><title>' + document.title + '</title>');
                mywindow.document.write(`
    <style>
        @media print {
            @page:first {
                size: 200px auto; /* প্রথম পৃষ্ঠার উচ্চতা 400px */
            }
            @page {
                size: A5; 
                margin: 0; 
                padding: 0;
                margin-top:50px;
            }
            body {
                font-family: Arial, sans-serif; 
                margin: 0;
            }
            .receiver-table{
                margin-top:400px;
            }
            .footer-print{
            margin-top:200px;
            }
        }
    </style>
`);
            mywindow.document.write(
                `<link rel="stylesheet" href="{{ asset('admin_assets/assets/css/styles.min.css') }}" />`);
            mywindow.document.write('</head><body class="px-5 py-4">');
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
    <script>
         function PrintElem2(elem) {
    const priceElements = document.getElementsByClassName('price');
    const commissionElements = document.getElementsByClassName('commission');
    for (let i = 0; i < commissionElements.length; i++) {
        commissionElements[i].style.display = 'none';
    }
    for (let i = 0; i < priceElements.length; i++) {
        priceElements[i].style.display = 'none';
    }

    const mywindow = window.open('', 'PRINT', 'height=400,width=600');
    mywindow.document.write('<html><head><title>' + document.title + '</title>');
        mywindow.document.write(`
    <style>
        @media print {
            @page:first {
                size: 200px auto; /* প্রথম পৃষ্ঠার উচ্চতা 400px */
            }
            @page {
                size: A5; 
                margin: 0; 
                padding: 0;
                margin-top:50px;
            }
            body {
                font-family: Arial, sans-serif; 
                margin: 0;
            }
            .receiver-table{
                margin-top:180px;
            }
        }
    </style>
`);
    mywindow.document.write('<link rel="stylesheet" href="' + '{{ asset('admin_assets/assets/css/styles.min.css') }}' + '" />');
    mywindow.document.write('</head><body class="px-5 py-4">');
    
    mywindow.document.write('<link rel="preload" href="https://unpkg.com/@bitjson/qr-code@1.0.2/dist/qr-code.js" as="script">');
    
    // Add QR code script
    const script = mywindow.document.createElement('script');
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

    for (let i = 0; i < priceElements.length; i++) {
        priceElements[i].style.display = '';
    }

    return true;
}
    </script>
@endsection
