@extends('admin.app')
@section('page_title', 'Currency Exchange Rates')
@section('content')
    <div class="container-fluid">
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-4">{{ __('admin.curr_exchange.name1') }}</h5></div>
            <div class="card-body">
                <form action="{{ route('storeCurrencyExchangeRate') }}" method="POST" id="storeCurrencyExchangeRateForm">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="country_id">Select Country</label>
                            <select name="country_id" id="country_id" class="select2 form-control" required>
                                <option value="" selected> Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="exchange_rate1">Currency Exchange Rate<small class="text-danger">*</small></label>
                            <input type="number" name="exchange_rate" id="exchange_rate1" class="form-control"
                                min="0"  step="0.01" required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button class="btn btn-primary">{{ __('admin.curr_exchange.submit_button') }}</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5>{{ __('admin.curr_exchange.name') }}</h5>
            </div>
            <div class="card-body">
                <div class=""></div>
                <div class="table-responsive">
                    <table id="currencyExchangeRateListTable" class="table table-sm  table-bordered table-striped display">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>ISO 2</th>
                                <th>Exchange Rate</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="shipping_cost_modal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Update Currency Exchange Rate
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('updateCurrencyExchangeRate') }}" id="exchange_rate_form" method="POST">
                        @csrf
                        <input type="hidden" name="currency_id">
                        <div class="col-12 mb-3">
                            <label for="percentage">Exchange Rate</label>
                            <input type="number" name="exchange_rate" id="exchange_rate" class="form-control"
                                min="0" step="0.01" required>
                        </div>

                        <div class="text-end">
                            <button class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('.select2').select2();

        function editWeightCost(currencyId) {
            $.ajax({
                type: "get",
                url: "{{ route('getCurrencyExchangeRate', ['id' => ':id']) }}".replace(':id', currencyId),
                data: "",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        $('input[name="currency_id"]').val(response.data.id);
                        $('#exchange_rate').val(response.data.exchange_rate ?? 0);
                        $('#shipping_cost_modal').modal('show');
                    } else {
                        toastr.error('Something went wrong');
                        $('#shipping_cost_modal').modal('hide');
                    }
                },
                error: function(response) {
                    toastr.error('Something went wrong');
                    $('#shipping_cost_modal').modal('hide');
                }
            });
        }

        $('#exchange_rate_form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#shipping_cost_modal').modal('hide');
                        $('#currencyExchangeRateListTable').DataTable().ajax.reload();
                    } else {
                        toastr.error(response.message);
                        $('#shipping_cost_modal').modal('hide');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Failed to update');
                    $('#shipping_cost_modal').modal('hide');
                }
            })
        });

        $('#storeCurrencyExchangeRateForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#currencyExchangeRateListTable').DataTable().ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Failed to update');
                }
            })
        });

        $('#currencyExchangeRateListTable').DataTable({
            "dom": 'Bfrtip',
            "iDisplayLength": 50,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "buttons": ['pageLength', 'copy', 'excel', 'csv', 'pdf', 'print', 'colvis'],
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('currencyExchangeRateList') }}",
                "type": "GET"
            },
            "columns": [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "name"
                },


                {
                    "data": "iso2"
                },
                {
                    "data": "exchange_rate"
                },
                {
                    "data": "action"
                },
                // {
                //     "data": "max_dimensions"
                // },
                // {
                //     "data": "view"
                // },
                // {
                //     "data": "edit"
                // },
                // {
                //     "data": "delete"
                // }
            ],
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true
        });
    </script>
@endsection
