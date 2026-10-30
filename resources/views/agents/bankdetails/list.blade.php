@extends('admin.app')
@section('page_title', 'Currency Exchange Rates')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h5 class="card-title fw-semibold mb-4">{{ __('admin.bank_details.wallet') }}</h5>
                    <div>
                        <h4>{{ __('admin.bank_details.wallet_history') }}</h4>
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
                        <h4>{{ __('admin.bank_details.today_history') }}</h4>
                        <h6 class="hide-menu">Available Account Balance :
                            {{ fromEuroView(auth()->user()->currency_id ?? 0, getAccountbalances(Auth::id())['balance']) }}
                        </h6>
                        <h6 class="hide-menu">Today's Commission:
                            {{ fromEuroView(auth()->user()->currency_id ?? 0, getAccountbalances(Auth::id())['totalAdminEarningsToday']) }}
                        </h6>

                        <h6 class="hide-menu">Today's Spendings :
                            {{ fromEuroView(auth()->user()->currency_id ?? 0, getAccountbalances(Auth::id())['totalAdminSpentToday']) }}
                        </h6>

                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            @include('admin.partials.notification')
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">{{ __('admin.bank_details.name') }}</h5>
                <a class="btn btn-primary" href="{{ route('bank_details.create') }}">{{ __('admin.bank_details.add_button') }}</a>
            </div>
            {{--  </div>
        <div class="card">  --}}
            <div class="card-body">
                <div class=""></div>
                <div class="table-responsive">
                    <style>
                        #bankDetailsList th {
                            width: 16.6666666667% !important;
                        }
                    </style>
                    <table id="bankDetailsList" class="table table-sm w-100  table-bordered table-striped display">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Iban</th>
                                <th>Country</th>
                                <th>Total Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        $(function() {
            var table = $('#bankDetailsList').DataTable({
                "dom": 'Bfrtip',
                "iDisplayLength": 50,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "buttons": ['pageLength', 'copy', 'excel', 'csv', 'pdf', 'print', 'colvis'],
                processing: true,
                serverSide: true,
                ajax: "{{ route('bank_details.index') }}",

                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'bank_name',
                        name: 'bank_name'
                    },
                    {
                        data: 'iban',
                        name: 'iban',
                        class: 'text-nowrap'
                    },
                    {
                        data: 'country_id',
                        name: 'country_id',
                    },
                    {
                        data: 'total_debit',
                        name: 'total_debit',
                    },
                    {
                        data: 'action',
                        name: 'action',

                        searchable: false
                    },
                ]
            });
        });
    </script>
@endsection
