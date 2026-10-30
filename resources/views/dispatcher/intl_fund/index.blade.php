@extends('admin.app')
@section('page_title', 'My Intenational Fund Transfer Orders')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">All My Intenational Fund Transfer Orders <a href="{{route('intl_fund_trasfer_order.create')}}" class="btn btn-primary" style="float: right">New Order</a></h5>

                @include('admin.partials.notification')
                <hr>
                <div class="d">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <h5>Intenational Fund Transfer Orders</h5>
                        <div class="table-responsive">
                            <table id="orders_tbl" class="table table-sm  table-bordered table-striped display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Data</th>
                                        <th>Sender</th>
                                        <th>Receiver</th>
                                        <th>Status</th>
                                        <th>View</th>
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
                    "url": "{{ route('dispatchIntlFundOrdersList') }}",
                    "type": "GET"
                },
                "columns": [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "date"
                    },
                    {
                        "data": "sender"
                    },
                    {
                        "data": "reciever"
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
