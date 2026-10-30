@extends('admin.app')
@section('page_title', 'Shipping Costs')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Location Shipping Costs</h5>
                    <a href="{{ route('shipping-location-rates.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus me-1"></i>Add Location Rate
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="countriesShippingCostTable" class="table table-sm  table-bordered table-striped display">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Origin City</th>
                                <th>Destination City</th>
                                <th>Freight Mode</th>
                                <th>Day</th>
                                <th>Weight</th>
                                <th>Shipping Cost €</th>
                                <th>Documents</th>
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
        $('#countriesShippingCostTable').DataTable({
            "dom": 'Bfrtip',
            "iDisplayLength": 20,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "buttons": ['pageLength', 'copy', 'excel', 'csv', 'pdf', 'print', 'colvis'],
            processing: true,
            serverSide: true,
            "ajax": {
                "url": "{{ route('shippingRatesList') }}",
                "type": "GET"
            },
            "columns": [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'origin', name: 'origin' },
                { data: 'destination', name: 'destination' },
                { data: 'freight_mode', name: 'freight_mode' },
                { data: 'day', name: 'day' },
                { data: 'weight_display', name: 'weight_display' },
                { data: 'shipping_cost', name: 'shipping_cost' },
                { data: 'documents', name: 'documents', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
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
