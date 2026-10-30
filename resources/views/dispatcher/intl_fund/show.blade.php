@extends('admin.app')
@section('page_title', 'International Fund Transfer Order Payment')
@section('content')
    <script src="https://js.stripe.com/v3/"></script>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">International Fund Transfer Order Payment</h5>
                @include('admin.partials.notification')
                <hr>
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route('intl_fund_trasfer_order.index') }}" class="btn btn-danger float-right"><i
                                class="fa fa-times"></i>Exit</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8" id="print_section">
                                <h5>Viewing Order [{{ $order->tracking_id }}]</h5>
                                <div class="table-reponsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>Tax code</th>
                                                <td>{{ $order->walk_in_customer->tax_code }}</td>
                                                <th>Tracking ID</th>
                                                <td>{{ $order->tracking_id }}</td>
                                            </tr>
                                            <tr>
                                                <th>Surname</th>
                                                <td>{{ $order->walk_in_customer->surname }}</td>
                                                <th>Firstname</th>
                                                <td>{{ $order->walk_in_customer->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Gender</th>
                                                <td>{{ $order->walk_in_customer->gender }}</td>
                                                <th>D.O.B</th>
                                                <td>{{ $order->walk_in_customer->birthDate }}</td>
                                            </tr>
                                            <tr>
                                                <th>Document Type</th>
                                                <td>{{ $order->walk_in_customer->doc_type }}</td>
                                                <th>Document Number</th>
                                                <td>{{ $order->walk_in_customer->doc_num }}</td>
                                            </tr>
                                            <tr>
                                                <th>Sender Email / Phone</th>
                                                <td>{{ $order->walk_in_customer->email }} /
                                                    {{ $order->walk_in_customer->phone }}</td>
                                                <th>Sender Address</th>
                                                <td>{{ $order->walk_in_customer->address }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>{{ $order->tx_status }}</td>
                                                <th>KYC Status</th>
                                                <td>{{ $order->walk_in_customer->kyc_status }}</td>
                                            </tr>
                                            <tr>
                                                <th>Sender Country/ Amount({{ $order->s_currency }})</th>
                                                <td>{{ $order->s_country }} / {{ number_format($order->s_amount) }}</td>
                                                <th>Receiver Country/ Amount({{ $order->rx_currency }})</th>
                                                <td>{{ $order->rx_country }} / {{ number_format($order->rx_amount) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Receiver bank name</th>
                                                <td>{{ $order->rx_bank_name }}</td>
                                                <th>Receiver Routing Code</th>
                                                <td>{{ $order->rx_bank_routing_no }}</td>
                                            </tr>
                                            <tr>
                                                <th>Receiver Account name</th>
                                                <td>{{ $order->rx_bank_account_name }}</td>
                                                <th>Receiver Account Number</th>
                                                <td>{{ $order->rx_bank_account_number }}</td>
                                            </tr>
                                            <tr>
                                                <th>Receiver Account name</th>
                                                <td>{{ $order->rx_bank_account_name }}</td>
                                                <th>Receiver Account Number</th>
                                                <td>{{ $order->rx_bank_account_number }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-primary"
                                    onclick="PrintElem('print_section')">Print</button>
                            </div>
                            <div class="col-md-4">
                                <h5>Payment Summary</h5>
                                <table class="table table-bordered">
                                    @php
                                        $main = $order->s_amount;
                                        $comm = $order->rate->calc == 'perc' ? ($order->rate->commision / 100) * $order->s_amount : $order->rate->commision;
                                        $subTotal = $main;
                                        $taxRate = 0;
                                        $taxCost = $taxRate;
                                        $totalCost = $subTotal + $taxCost;
                                    @endphp
                                    <tr>
                                        <th>Amount Sent(&euro;)</th>
                                        <td>{{ $main }}</td>
                                    </tr>
                                    <tr>
                                        <th>Commission(&euro;)</th>
                                        <td>{{ $comm }}</td>
                                    </tr>
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
                                {{-- <p>{{ $stripeIntent->status }}</p>
                                @if ($stripeIntent->status == 'succeeded')
                                    <table class="table">
                                        <tr>
                                            <th>Payment Status</th>
                                            <td><span><i class="fa fa-check text-success"></i> Successful</span></td>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <td>{{ date('Y-m-d H:i:s', $stripeIntent->created) }}</td>
                                        </tr>
                                    </table>
                                @elseif ($stripeIntent->status == 'processing')
                                    <p>Your payment is processing refresh this page later.</p>
                                    <table class="table">
                                        <tr>
                                            <th>Payment Status</th>
                                            <td><span><i class="fa fa-check text-success"></i> Processing</span></td>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <td>{{ date('Y-m-d H:i:s', $stripeIntent->created) }}</td>
                                        </tr>
                                    </table>
                                @else
                                    <form id="payment-form" data-secret="{{ $stripeIntent->client_secret }}">
                                        <div id="payment-element">
                                            <!-- Elements will create form elements here -->
                                        </div>
                                        <br>
                                        <button id="submit" class="btn btn-primary btn-lg">Pay Now</button>
                                        <div id="error-message">
                                            <!-- Display error message to your customers here -->
                                        </div>
                                    </form>
                                @endif --}}

                                @if ($stripeIntent->tx_status == 'pending')
                                    <table class="table">
                                        <tr>
                                            <th>Payment Status</th>
                                            <td><span><i class="fa fa-check text-success"></i> Successful</span></td>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <td>{{ date('Y-m-d H:i:s', $stripeIntent->created) }}</td>
                                        </tr>
                                    </table>
                                @else
                                    <table class="table">
                                        <tr>
                                            <th>Payment Status</th>
                                            <td><span><i class="fa fa-times text-danger"></i> Unknown</span></td>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <td>{{ date('Y-m-d H:i:s', $stripeIntent->created) }}</td>
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
            clientSecret: '{{ $stripeIntent->client_secret }}',
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
                    return_url: "{{ route('intl_fund_trasfer_order.show', $order->id) }}",
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

        function PrintElem(elem) {
            var mywindow = window.open('', 'PRINT', 'height=400,width=600');

            mywindow.document.write('<html><head><title>' + document.title + '</title>');
            mywindow.document.write(
                `<link rel="stylesheet" href="{{ asset('admin_assets/assets/css/styles.min.css') }}" />`)
            mywindow.document.write('</head><body>');
            mywindow.document.write(document.getElementById(elem).innerHTML);
            mywindow.document.write('</body></html>');

            mywindow.document.close(); //IE >=10
            mywindow.focus(); // IE >= 10

            mywindow.print();
            //mywindow.close();

            return true;
        }
    </script>
@endsection
