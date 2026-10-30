@php
    use App\Models\BankDetail;
    use App\Models\PaymentRequest;
    $bank_details = BankDetail::select('id', 'bank_name', 'iban')->get();
@endphp
@extends('admin.app')
@section('page_title', 'Wallet')
@section('content')
    <script src="https://js.stripe.com/v3/"></script>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="card-header">
                    @if (\Route::current()->getName() !== 'admin-paymentRequestget')
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title fw-semibold mb-4">{{ __('admin.my_wallet.wallet') }}</h5>
                            <div>
                                <h4>{{ __('admin.my_wallet.wallet_history') }}</h4>
                                <h6 class="hide-menu">Total Account Amount :
                                    {{ fromEuroView(auth()->user()->currency_id ?? 0, getAccountbalances(Auth::id())['totalAmount']) }}
                                </h6>
                                <h6 class="hide-menu">Total Available Balance :
                                    {{ fromEuroView(auth()->user()->currency_id ?? 0, getAccountbalances(Auth::id())['balance']) }}
                                </h6>

                                <h6 class="hide-menu">Total Spendings :
                                    {{ fromEuroView(auth()->user()->currency_id ?? 0, getAccountbalances(Auth::id())['totalSpendings']) }}
                                </h6>
                            </div>
                            <div>
                                <h4>{{ __('admin.my_wallet.today_history') }}</h4>
                                <h6 class="hide-menu">Available Account Balance :
                                    {{ fromEuroView(auth()->user()->currency_id ?? 0, getAccountbalances(Auth::id())['balance']) }}
                                </h6>
                                <h6 class="hide-menu">Today's Commission:
                                    {{ fromEuroView(auth()->user()->currency_id ?? 0, getAccountbalances(Auth::id())['earningsToday']) }}
                                </h6>

                                <h6 class="hide-menu">Today's Spendings :
                                    {{ fromEuroView(auth()->user()->currency_id ?? 0, getAccountbalances(Auth::id())['spentToday']) }}
                                </h6>

                            </div>
                        </div>
                    @else
                        <h4>{{ __('admin.my_wallet.name') }}</h4>
                    @endif
                </div>
                {{-- <p class="mb-0">This is the Agent Wallet page </p> --}}
                {{-- @include('dispatcher.settings.nav') --}}
                @include('admin.partials.notification')
                <hr>
                @if (Auth::user()->user_type == 'admin')
                    <style>
                        #paymentRequestList1 tr th {
                            text-align: center;
                            white-space: nowrap;
                        }
                    </style>
                    <table id="paymentRequestList1" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="py-2">S/No</th>
                                <th class="py-2">Bank Name</th>
                                <th class="py-2">IBAN</th>
                                <th class="py-2">Country</th>
                                <th class="py-2">City</th>
                                <th class="py-2">Amount</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Action</th>
                                <th class="py-2">View</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{--  @dd($paymentRequests);  --}}

                            @foreach ($paymentRequests as $request)
                                <tr>
                                    <td class="py-2"> {{ $request->id }}</td>
                                    <td class="py-2"> {{ $request->bankDetail?->bank_name }}</td>
                                    <td class="py-2"> {{ $request->bankDetail?->iban }}</td>
                                    <td class="py-2"> {{ $request->bankDetail?->country->name }}</td>
                                    <td class="py-2"> {{ $request->bankDetail?->city->name }}</td>
                                    <td class="py-2">
                                        {{ fromEuroView(auth()->user()->currency_id ?? 0, $request->amount) }}</td>
                                    <td class="py-2"> <span class="badge bg-secondary">{{ $request->status }}</span></td>
                                    <td class="py-2">
                                        @if ($request->status === 'pending')
                                            <div class="d-flex align-item-center gap-2">
                                                <a class="btn btn-primary btn-sm"
                                                    href="{{ route('admin-accept-paymentRequest', $request->id) }}">Accept</a>
                                                <a class="btn btn-info btn-sm"
                                                    href="{{ route('admin-reject-paymentRequest', $request->id) }}">Reject</a>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-2">
                                        <a style="white-space: nowrap;" class="btn btn-sm btn-primary mx-5"
                                            data-bs-toggle="modal" data-bs-target="#mymodal"><i
                                                class="fa fa-eye"></i>Veiw</a>

                                        <div class="modal modal-lg fade" id="mymodal" tabindex="-1"
                                            aria-labelledby="mymodalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="mymodalLabel">Payment Request
                                                            Attachement</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">

                                                        </style>
                                                        <table
                                                            class="table table-sm w-100 mb-5 table-bordered table-striped display">
                                                            <thead>
                                                                <tr>
                                                                    <th>Name</th>
                                                                    <th>IBAN</th>
                                                                    <th>Amount</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td> {{ $request->bankDetail->bank_name }} </td>
                                                                    <td> {{ $request->bankDetail->iban }} </td>
                                                                    <td> {{ fromEuroView(auth()->user()->currency_id ?? 0, $request->amount) }}
                                                                    </td>
                                                                    <td class=""><span
                                                                            class="badge py-2 text-white bg-info text-dark">
                                                                            {{ $request->status }}</span></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <img class="img-fluid w-100 h-auto"
                                                            src="{{ asset('/uploads/payment/' . $request->reciept_attachement) }}">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{--  modal  --}}

                    {{--  modal  --}}
                @else
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('admin.my_wallet.name') }}</h5>
                        </div>
                        <div class="card-body">
                            <form class="mb-5" id="paymentRequest" action="{{ route('paymentRequestpost') }}"
                                method="post" enctype="multipart/form-data">
                                @csrf
                                @method('POST')
                                <div class="row">
                                    <div class="col-12 col-lg-6">
                                        <label for="bank_name">Name of Bank<i class="text-danger">*</i>:</label>
                                        <select name="bank_detail_id" id="bank_detail_id" class="form-control">
                                                <option value="{{ auth()->user()->bank_detail_id }}" selected>{{ auth()->user()->bankDetail?->bank_name }},
                                                    {{auth()->user()->bankDetail?->iban }}</option>
                                        </select>
                                        @error('bank_detail_id')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label for="file">Receipt Attachment<i class="text-danger">*</i>:</label>
                                        <input type="file" name="reciept_attachement" id="reciept_attachement"
                                            class="form-control">
                                        @error('reciept_attachement')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-12 col-lg-6">
                                        <label for="amount">Amount<i class="text-danger">*</i>:</label>
                                        <input type="number" name="amount" id="amount" class="form-control">
                                        @error('amount')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <br>
                                <button type="submit" class="btn btn-primary">{{ __('admin.my_wallet.save_button') }}</button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('admin.my_wallet.name1') }}</h5>
                        </div>
                        <div class="card-body">
                            <style>
                                #paymentRequestList tr th {
                                    width: 20% !important;
                                    text-align: left;
                                    white-space: nowrap;
                                }

                                #paymentRequestList td {
                                    padding-top: 5px;
                                    padding-bottom: 5px;
                                    text-align: left;
                                }
                            </style>

                            <table id="paymentRequestList" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="py-2">S/No</th>
                                        <th class="py-2">Bank Name</th>
                                        <th class="py-2">Amount</th>
                                        <th class="py-2">Status</th>
                                        <th class="py-2">View</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('admin.my_wallet.name2') }}</h5>
                        </div>
                        <div class="card-body">
                            <style>
                                #transectionsList tr th {
                                    width: 20% !important;
                                    text-align: left;
                                    white-space: nowrap;
                                }

                                #transectionsList td {
                                    padding-top: 5px;
                                    padding-bottom: 5px;
                                    text-align: left;
                                }
                            </style>

                            <table id="transectionsList" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="py-2">S/No</th>
                                        <th class="py-2">Trnsection ID #</th>
                                        <th class="py-2">Desciption</th>
                                        <th class="py-2">flag</th>
                                        <th class="py-2">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @if (null != request()->get('deposit_amt'))
        <script>
            const stripe = Stripe('{{ env('STRIPE_KEY') }}');

            const options = {
                clientSecret: '{{ $stripeIntent->client_secret }}',
                appearance: {},
            };
            const elements = stripe.elements(options);

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
                    elements,
                    confirmParams: {
                        return_url: "{{ route('my-wallet.store') }}",
                    },
                });

                if (error) {

                    const messageContainer = document.querySelector('#error-message');
                    messageContainer.textContent = error.message;
                } else {

                }
            });
        </script>
    @endif
    @if (Auth::user()->user_type !== 'admin')
        <script>
            $(document).ready(function() {
                var table = $('#paymentRequestList').DataTable({
                    "dom": 'Bfrtip',
                    "iDisplayLength": 30,
                    "lengthMenu": [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ],
                    "buttons": ['pageLength', 'copy', 'excel', 'csv', 'pdf', 'print', 'colvis'],
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('paymentRequestget') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'bank_detail_id',
                            name: 'bank_detail_id'
                        },
                        {
                            data: 'amount',
                            name: 'amount'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'veiw',
                            name: 'veiw',
                            searchable: false
                        }
                    ]
                });

                table.ajax.reload();

                $('#transectionsList').DataTable({
                    "dom": 'Bfrtip',
                    "iDisplayLength": 30,
                    "lengthMenu": [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ],
                    "buttons": ['pageLength', 'copy', 'excel', 'csv', 'pdf', 'print', 'colvis'],
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('get-transit') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'transId',
                            name: 'transId'
                        },
                        {
                            data: 'description',
                            name: 'description',
                            class: 'text-nowrap'
                        },
                        {
                            data: 'flag',
                            name: 'flag'
                        },
                        {
                            data: 'amount',
                            name: 'amount',
                            searchable: true
                        }
                    ]
                });

            });
        </script>
    @endif


@endsection
