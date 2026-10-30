@extends('admin.app')
@section('page_title', 'Shipping Costs')

@section('content')
    <div class="container-fluid">

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="mb-4">Update Cities Shipping Cost Percentage ({{ $country->name }})</h5>
                <form action="{{ route('save-city-shipping-cost-percentage', $country->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="multiple_cities" value="yes">
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="cities">City</label>
                            <select multiple name="cities[]" id="cities" class="form-control select2" required>
                                <option value="all" selected>All Cities</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <label for="percentage">Shipping Percentage <small class="text-danger">*</small></label>
                            <input type="number" name="percentage" id="all_shipping_percentage" class="form-control"
                                min="0" max="100" step="0.01" required>
                        </div>
                        <div class="col-4">
                            <label for="percentage">Day </label>
                            <input type="text" name="day" id="all_shipping_percentage" class="form-control"
                                required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>


        <div class="card">
            <div class="card-body">
                <h5>Cities Shipping Costs ({{ $country->name }})</h5>
                <div class="table-responsive">
                    <table id="cityShippingCostTable" class="table table-sm  table-bordered table-striped display">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Day</th>
                                <th>Percentage</th>
                                <th>Weight</th>
                                <th>Shipping Cost (Min-Max) €</th>

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
                        Update Shipping Cost Percentage
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('save-city-shipping-cost-percentage', $country->id) }}"
                        id="city_shipping_cost_form" method="POST">
                        @csrf
                        <input type="hidden" name="city_id">
                        <div class="col-12 mb-3">
                            <label for="percentage">Shipping Percentage</label>
                            <input type="number" name="percentage" id="shipping_percentage" class="form-control"
                                min="0" max="100" step="0.01" required>
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
        const options = {};

        // Initialize the modal with options
        const myModal = new bootstrap.Modal(
            document.getElementById("shipping_cost_modal"),
            options
        );

        function addShippingCost(city_id) {
            $.ajax({
                type: "get",
                url: "{{ route('get-city-shipping-cost', ['id' => ':id']) }}".replace(':id', city_id),
                data: "",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        $('input[name="city_id"]').val(city_id);
                        $('#modalTitleId').text(response.city_name ?? 'Update Percentage');
                        $('#shipping_percentage').val(response.shipping_percentage ?? 0);
                        // $('#shipping_cost_modal').modal('show');
                        myModal.show();

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

        $('#city_shipping_cost_form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        toastr.success('Shipping cost Percentage was successfully updated.');
                        myModal.hide();
                        $('#cityShippingCostTable').DataTable().ajax.reload();
                    } else {
                        toastr.error('Shipping cost Percentage Failed to update');
                        myModal.hide();
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Shipping cost Percentage Failed to update');
                     myModal.hide();
                }
            })
        })

        $('.select2').select2();




        $('#cityShippingCostTable').DataTable({
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
                "url": "{{ route('citiesShippingRatesList', $country->id) }}",
                "type": "GET"
            },
            "columns": [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "name"
                },
                {
                    "data": "day"
                },
                {
                    "data": "shipping_percentage"
                },
                {
                    "data": "weight"
                },
                {
                    "data": "shipping_cost"
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
