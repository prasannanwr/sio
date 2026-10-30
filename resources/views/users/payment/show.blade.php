@extends('admin.app')
@section('page_title', 'Order Payment')
@section('content')
    {{-- <script src="https://js.stripe.com/v3/"></script> --}}
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">My Order Payment</h5>
                @include('admin.partials.notification')
                <hr>
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route('orders.index') }}" class="btn btn-danger float-right"><i
                                class="fa fa-times"></i>Exit</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Viewing Order [{{ $order->tracking_id }}]</h5>
                                <div class="table-reponsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>Origin</th>
                                                <td>
                                                    {{ $order->pickup_location->postcode }} -
                                                    {{ $order->pickup_location->name }} [Lat:
                                                    {{ $order->pickup_location->longitude }}, Long:
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
                                                    <b>Estimated Transit Time(days):</b>
                                                    {{ $order->shipping_rate->transit_days }}
                                                    <br>
                                                    <b>Dilevery at</b>: {{ $order->delivery_time }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $order->status }}</span>
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
                                {{-- <hr>
                                <h5>Geolocation Data</h5>
                                <hr>
                                <div id="map" style="height: 400px;"></div> --}}
                            </div>
                            <div class="col-md-4">
                                <h5>Payment Summary</h5>
                                <table class="table table-bordered">
                                    @php
                                        $shippingCost = $order->shipping_rate->price;
                                        // $pickupCost = $order->shipping_rate->pickup_cost_per_km * $pickup_distance;
                                        // $deliveryCost = $order->shipping_rate->delivery_cost_per_km * $delivery_distance;
                                        // $subTotal = $shippingCost + $pickupCost + $deliveryCost;
                                        $subTotal = $shippingCost;
                                        $taxRate = 1;
                                        // $taxCost = $subTotal * $taxRate;
                                        $taxCost = 0;
                                        $totalCost = $subTotal + $taxCost;
                                    @endphp
                                    <tr>
                                        <th>Trunk Shipping cost(&euro;)</th>
                                        <td>{{ $shippingCost }}</td>
                                    </tr>
                                    {{-- <tr>
                                        <th>Doorstep Pickup Cost(&euro;)[{{ $pickup_distance }} Km]</th>
                                        <td>{{ $pickupCost }}</td>
                                    </tr>
                                    <tr>
                                        <th>Doorstep Delivery Cost(&euro;) [{{ $delivery_distance }} Km]</th>
                                        <td>{{ $deliveryCost }}</td>
                                    </tr> --}}
                                    <tr class="bg-light">
                                        <th>Sub-Total Cost(&euro;)</th>
                                        <td>{{ $subTotal }}</td>
                                    </tr>
                                    {{-- <tr>
                                        <th>Tax Cost(&euro;)- 7.5 %</th>
                                        <td>{{ $taxCost }}</td>
                                    </tr> --}}
                                    <tr class="bg-secondary">
                                        <th>Total Cost(&euro;)</th>
                                        <td>{{ $totalCost }}</td>
                                    </tr>
                                </table>
                                <hr>
                                {{-- <p>{{ $stripeIntent->status }}</p> --}}
                                @if ($payment_entry->status == 'done')
                                    <table class="table">
                                        <tr>
                                            <th>Payment Status</th>
                                            <td><span><i class="fa fa-check text-success"></i> Successful</span></td>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <td>{{ date('Y-m-d H:i:s', $payment_entry->created) }}</td>
                                        </tr>
                                    </table>
                                    {{-- @elseif ($payment_entry->status == 'processing')
                                    <p>Your payment is processing refresh this page later.</p>
                                    <table class="table">
                                        <tr>
                                            <th>Payment Status</th>
                                            <td><span><i class="fa fa-check text-success"></i> Processing</span></td>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <td>{{ date('Y-m-d H:i:s', $payment_entry->created) }}</td>
                                        </tr>
                                    </table> --}}
                                @else
                                    {{-- <form id="payment-form" data-secret="{{ $stripeIntent->client_secret }}">
                                        <div id="payment-element">
                                            <!-- Elements will create form elements here -->
                                        </div>
                                        <br>
                                        <button id="submit" class="btn btn-primary btn-lg">Pay Now</button>
                                        <div id="error-message">
                                            <!-- Display error message to your customers here -->
                                        </div>
                                    </form> --}}
                                    <table class="table">
                                        <tr>
                                            <th>Payment Status</th>
                                            <td><span><i class="fa fa-times text-danger"></i>
                                                    {{ $payment_entry->status }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <td>{{ date('Y-m-d H:i:s', $payment_entry->created) }}</td>
                                        </tr>
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // Set your publishable key: remember to change this to your live publishable key in production
        // See your keys here: https://dashboard.stripe.com/apikeys
        const stripe = Stripe('{{ env('STRIPE_KEY') }}');

        const options = {
            clientSecret: '',
            // Fully customizable with appearance API.
            appearance: {
                /*...*/
            },
        };

        // Set up Stripe.js and Elements to use in checkout form, passing the client secret obtained in a previous step
        const elements = stripe.elements(options);

        // Create and mount the Payment Element
        const paymentElement = elements.create('payment');
        paymentElement.mount('#payment-element');

        const form = document.getElementById('payment-form');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            $('#submit').attr('disabled', true);
            $('#error-message').text('Payment Processing...please wait')
            const {
                error
            } = await stripe.confirmPayment({
                //`Elements` instance that was used to create the Payment Element
                elements,
                confirmParams: {
                    return_url: "{{ route('payment.summary', ['order_id' => $order->id]) }}",
                },
            });

            if (error) {
                // This point will only be reached if there is an immediate error when
                // confirming the payment. Show error to your customer (for example, payment
                // details incomplete)
                const messageContainer = document.querySelector('#error-message');
                messageContainer.textContent = error.message;
            } else {
                // Your customer will be redirected to your `return_url`. For some payment
                // methods like iDEAL, your customer will be redirected to an intermediate
                // site first to authorize the payment, then redirected to the `return_url`.
            }
        });
    </script>
    <script>
        // // Get latitude and longitude values for the two locations from the server
        // var latitude1 = {{ $order->pickup_location->latitude }};
        // var longitude1 = {{ $order->pickup_location->longitude }};
        // var latitude2 = {{ $order->delivery_location->latitude }};
        // var longitude2 = {{ $order->delivery_location->longitude }};
        // var latitude3 = {{ $order->current_location->latitude }};
        // var longitude3 = {{ $order->current_location->longitude }};

        // // Initialize the map
        // var map = L.map('map').setView([latitude1, longitude1], 5);

        // // Add OpenStreetMap as the base layer
        // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        //     attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        // }).addTo(map);

        // // Add markers for the two locations
        // var marker1 = L.marker([latitude1, longitude1]).addTo(map).bindPopup('{{ $order->pickup_location->name }}');
        // var marker2 = L.marker([latitude2, longitude2]).addTo(map).bindPopup('{{ $order->delivery_location->name }}');
        // var marker3 = L.marker([latitude3, longitude3]).addTo(map).bindPopup(
        //     '{{ $order->current_location->name }} - Cur. loc.').openPopup();;

        // // Create a polyline between the two markers
        // // Create a blue polyline between origin and current location
        // var bluePolyline = L.polyline([
        //     [latitude1, longitude1],
        //     [latitude3, longitude3]
        // ], {
        //     color: 'blue'
        // }).addTo(map);

        // // Create a gray polyline between cur location and destination
        // var grayPolyline = L.polyline([
        //     [latitude3, longitude3],
        //     [latitude2, longitude2]
        // ], {
        //     color: 'gray'
        // }).addTo(map);

        // // Fit the map to the bounds of the polyline
        // map.fitBounds([latitude1, longitude1], [latitude2, longitude2]);
    </script>
@endsection
