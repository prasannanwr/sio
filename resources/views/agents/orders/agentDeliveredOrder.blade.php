@extends('admin.app')
@section('page_title', 'Orders')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                @include('admin.partials.notification')
                <div class="d">
                    <div class="card-body">
                        <div class="text-end ">
                            <form class="justify-content-end d-flex gap-3" action="" method="post">
                                <input style="width: 20%" type="date" id="startDate" name="startDate"
                                    class="form-control" id="dateFillter" name="dateFillter" id="">
                                <input style="width:20%" type="date" id="endDate" name="endDate" class="form-control"
                                    id="dateFillter" name="dateFillter" id="">
                                <button id="dateFillter" class="btn btn-primary btn-sm" type="button">Save</button>
                            </form>
                        </div>
                        <h5>{{ __('admin.all_order_agent.name') }}</h5>
                        <style>
                            #orders_tbl th,
                            td {
                                white-space: nowrap;
                            }
                        </style>
                        <div class="table-responsive">

                            <table id="orders_tbl" class="table table-sm  table-bordered table-striped display">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Pickup Data</th>
                                        <th>Pickup Locations</th>
                                        <th>Delivery Man</th>
                                        <th>Sender</th>
                                        <th>Receiver</th>
                                        <th>Price(&euro;)</th>
                                        <th>Status</th>
                                        <th>Print</th>
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
        $("#dateFillter").on('click', function() {
            console.log($("#startDate").val())
            let url = "{{ route('agentsOrdersListDelivered') }}?startDate=" + $("#startDate").val() + "&endDate=" + $(
                "#endDate").val();
            console.log(url);
            $('#orders_tbl').DataTable().ajax.url(url).load();
        });

        $(document).ready(function() {
            var table = $('#orders_tbl').DataTable({
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
                    url: "{{ route('agentsOrdersListDelivered') }}",
                    typE: "GET",
                    data: function(d) {
                        d.startDate = $("#startDate").val();
                        d.endDate = $("#endDate").val();
                    },
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
                        "data": "deliveryMan"
                    },
                    {
                        "data": "sender"
                    },
                    {
                        "data": "receiver"
                    },
                    
                    {
                        "data": "price"
                    },
                    {
                        "data": "status"
                    },
                    {
                        "data": "print"
                    },
                ],
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true
            });
        });
    </script>
@endsection
