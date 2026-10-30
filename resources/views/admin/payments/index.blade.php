@extends('admin.app')
@section('page_title', 'Payments')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">All Payments</h5>
                <hr>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="payments_tbl" class="table table-sm  table-bordered table-striped display">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Order_ID</th>
                                    <th>Customer</th>
                                    <th>Amount(&euro;)</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>View</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('#payments_tbl').DataTable({
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
                "url": "{{ route('allPaymentsList') }}",
                "type": "GET"
            },
            "columns": [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "order_id"
                },
                {
                    "data": "customer"
                },
                {
                    "data": "amount"
                },
                {
                    "data": "status"
                },
                {
                    "data": "date"
                },
                {
                    "data": "view"
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
