@extends('admin.app')
@section('page_title', 'Orders')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">All Orders In My city [{{Auth::user()->dispatcher->city}}]</h5>
                <i>You can modify your city in the settings page</i>
                @include('admin.partials.notification')
                <hr>
                <div class="d">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <h5>Orders</h5>
                        <div class="table-responsive">
                            <table id="orders_tbl" class="table table-sm  table-bordered table-striped display">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Data</th>
                                        <th>Locations</th>
                                        <th>Parties</th>
                                        <th>Price(&euro;)</th>
                                        <th>Status</th>
                                        <th>Accept/ Pickup</th>
                                    </tr>
                                </thead>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    $('#orders_tbl').DataTable({
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
                    "url": "{{ route('dispatchOrdersList') }}",
                    "type": "GET"
                },
                "columns": [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "date"
                    },
                    {
                        "data": "location"
                    },
                    {
                        "data": "parties"
                    },
                    {
                        "data": "price"
                    },
                    {
                        "data": "status"
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
