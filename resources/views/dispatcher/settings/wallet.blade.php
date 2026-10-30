@extends('admin.app')
@section('page_title', 'Wallet')
@section('content')
    <script src="https://js.stripe.com/v3/"></script>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">My Wallet</h5>
                {{-- <p class="mb-0">This is the Agent Wallet page </p> --}}
                @include('dispatcher.settings.nav')
                @include('admin.partials.notification')
                <hr>
                <div class="row">
                    <div class="col-sm-8">
                        <h4>Wallet History</h4>
                        <h6 class="hide-menu">Today's Commision (&euro;) :
                            {{ getAccountbalances(Auth::id())['earningsToday'] }}</h6>
                        <h6 class="hide-menu">Today's Spendings (&euro;) : {{ getAccountbalances(Auth::id())['spentToday'] }}
                        </h6>
                        <h6 class="hide-menu">Account Balance (&euro;) : {{ getAccountbalances(Auth::id())['balance'] }}</h6>
                        <hr>
                        <form action="" method="get" class="from-inline">
                            {{-- <p>{{$message}}</p> --}}
                            <div class="row">
                                <div class="col-sm-4">
                                    <input type="date" name="date_from" id="date_from" class="form-control"
                                        placeholder="Start date">
                                </div>
                                <div class="col-sm-4">
                                    <input type="date" name="date_to" id="date_to" class="form-control"
                                        placeholder="End date">
                                </div>
                                <div class="col-sm-4">
                                    <button type="submit" class="btn btn-primary">Get History</button>
                                </div>
                            </div>
                        </form>
                        <hr>
                        @if (isset($wallet_history))
                            <table class="table table-sm table-stripped table-border">
                                <thead>
                                    <th>#</th>
                                    <th>Amount(&euro;)</th>
                                    <th>ID</th>
                                    <th>Date</th>
                                </thead>
                                <tbody>

                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach ($wallet_history as $history)
                                        <tr>
                                            <td>
                                                {{ $i }}
                                            </td>
                                            <td>
                                                @if ($history->flag = 'debit')
                                                    {{ $history->amount }}
                                                @else
                                                    -{{ $history->amount }}
                                                @endif
                                            </td>
                                            <td>{{ $history->transId }}</td>
                                            <td>{{ $history->created_at }}</td>
                                        </tr>
                                        @php
                                            $i++;
                                        @endphp
                                    @endforeach

                                </tbody>
                            </table>
                        @endif
                    </div>
                    <div class="col-sm-4">
                        <h4>Deposit Into wallet</h4>
                        @if (null != request()->get('deposit_amt'))
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
                        @else
                            <form action="" method="get">
                                @csrf
                                <div class="form-group">
                                    <label for="amount">Deposit Amount(&euro;)</label>
                                    <input type="number" name="deposit_amt" class="form-control" id="amount" required>
                                </div>
                                <br>
                                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @if (null != request()->get('deposit_amt'))
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
                        return_url: "{{ route('my-wallet.store') }}",
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
    @endif
@endsection
