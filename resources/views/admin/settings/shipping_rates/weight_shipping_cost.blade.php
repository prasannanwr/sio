@extends('admin.app')
@section('page_title', 'Shipping Costs')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5>Dimentions Shipping Costs ({{ $country->name }})</h5>
                <div class="table-responsive">
                    <table id="weightShippingCostTable" class="table table-sm  table-bordered table-striped display">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Weight</th>
                                <th>Shipping Cost €</th>
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
                        Update Shipping Cost 
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('shipping-cost-update') }}" id="city_shipping_cost_form" method="POST">
                        @csrf
                        <input type="hidden" name="shipping_id">
                        <div class="col-12 mb-3">
                            <label for="percentage">Shipping Cost</label>
                            <input type="number" name="cost" id="shipping_cost" class="form-control" min="0"
                                step="0.01" required>
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
        
        function addWeightCost(shippingCostId) {
            $.ajax({
                type: "get",
                url: "{{ route('get-weight-shipping-cost', ['id' => ':id']) }}".replace(':id', shippingCostId),
                data: "",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        $('input[name="shipping_id"]').val(response.shippingId);
                        $('#shipping_cost').val(response.shippingCost ?? 0);
                       $('#shipping_cost_modal').modal('show');

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to get shipping cost',
                            showDenyButton: false,
                        });
                    }
                }
            });
        }


        $('#weightShippingCostTable').DataTable({
            "dom": 'Bfrtip',
            "iDisplayLength": 20,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "buttons": ['pageLength', 'copy', 'excel', 'csv', 'pdf', 'print', 'colvis'],
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('weightShippingRatesList', $country->id) }}",
                "type": "GET"
            },
            "columns": [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "name"
                },

                {
                    "data": "weight"
                },
                {
                    "data": "cost"
                },
                {
                    "data": "action"
                },
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
