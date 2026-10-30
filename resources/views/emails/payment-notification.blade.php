<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Payment Notification</title>
</head>

<body>
    <div class="container">
        <img src="{{ asset('landing/assets/img/gallery/sioshipping_logo.png') }}" alt="Sioshipping Logo" height="100px">
        <p>
            The Shipping Order with tracking id
            {{ $order->tracking_id }}
            was Recently updated. <br>
            You are recieving this email because you are a party in this transaction. <br>
            The current status ot the order is <span class="badge bg-secondary">{{ $order->status }}</span><br>
            Bellow are the details off the order.
        </p>
        <hr>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>Origin</th>
                    <td>
                        {{ $order->pickupCountry->name . ', ' . $order->pickupCity->name }}
                        <p style="margin: 0px !important" class="text-nowrap">[Lat:
                            {{ $order->pickupCity->latitude }}, Long:
                            {{ $order->pickupCity->longitude }}]</p>
                        <br>
                        Picked up at: {{ $order->pickup_time }}
                    </td>
                    <th>Destination / Current Location </th>
                    <td>
                        <b>Destination</b>:
                        {{ $order->deliveryCountry->name . ', ' . $order->deliveryCity->name }}
                        <p style="margin: 0px !important" class="text-nowrap">[Lat:{{ $order->deliveryCity->latitude }},
                            Long:
                            {{ $order->deliveryCity->longitude }}]</p>
                        <br>

                        <b>Current Location</b> : {{ $order->currentCountry->name . ', ' . $order->currentCity->name }}
                        <p style="margin: 0px !important" class="text-nowrap">[Lat:{{ $order->currentCity->latitude }},
                            Long:{{ $order->currentCity->longitude }}]</p>
                        <br>
                        <b>Shipping Cost</b> {{ $order->shipping_cost }}
                        <br>


                    </td>
                </tr>
           
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge bg-secondary">{{ $order->status }}</span>
                    </td>
                    <th>Price(&euro;)</th>
                    <td>{{ $order->val_of_goods }}</td>
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
        &copy; {{ date('Y') }} <a href="https://sioshipping.eu">{{ env('APP_NAME') }}</a>
    </div>
</body>

</html>
