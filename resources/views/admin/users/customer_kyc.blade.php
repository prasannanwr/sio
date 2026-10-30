@extends('admin.app')
@section('page_title', 'Customer KYC')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs" id="adminTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="customer-kyc-tab" data-bs-toggle="tab"
                            data-bs-target="#customer-kyc-tab-pane" type="button" role="tab"
                            aria-controls="customer-kyc-tab-pane" aria-selected="true">Customer KYC</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="agent-kyc-tab" data-bs-toggle="tab"
                            data-bs-target="#agent-kyc-tab-pane" type="button" role="tab"
                            aria-controls="agent-kyc-tab-pane" aria-selected="false">Agents KYC</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="mobile-kyc-tab" data-bs-toggle="tab"
                            data-bs-target="#mobile-kyc-tab-pane" type="button" role="tab"
                            aria-controls="mobile-kyc-tab-pane" aria-selected="false">Mobile user KYC</button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                @include('admin.partials.notification')
                <div class="tab-content" id="adminTabContent">
                    <div class="tab-pane fade show active" id="customer-kyc-tab-pane" role="tabpanel"
                        aria-labelledby="customer-kyc" tabindex="0">
                        <h5 class="card-title fw-semibold mb-4">Customer KYC</h5>
                        <hr>
                        <div class="d">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="users_tbl" class="table table-sm  table-bordered table-striped" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Date</th>
                                                <th>Particulers</th>
                                                <th>KYC Status</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="agent-kyc-tab-pane" role="tabpanel" aria-labelledby="agent-kyc"
                        tabindex="0">
                        <h5 class="card-title fw-semibold mb-4">Agent KYC</h5>
                        <hr>
                        <div class="d2">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="agents_tbl" class="table table-sm  table-bordered table-striped" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Date</th>
                                                <th>Particulers</th>
                                                <th>KYC Status</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="mobile-kyc-tab-pane" role="tabpanel" aria-labelledby="mobile-kyc"
                        tabindex="0">
                        <h5 class="card-title fw-semibold mb-4">Agent KYC</h5>
                        <hr>
                        <div class="d3">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="mobile_tbl" class="table table-sm  table-bordered table-striped" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Date</th>
                                                <th>Particulers</th>
                                                <th>KYC Status</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('#agents_tbl').DataTable({
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
                "url": "{{ route('allAgentList') }}",
                "type": "GET"
            },
            "columns": [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "date"
                },
                {
                    "data": "surname"
                },
                {
                    "data": "kyc_actions"
                },
                {
                    "data": "kyc_status"
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
    <script>
        $('#mobile_tbl').DataTable({
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
                "url": "{{ route('allMobileUserList') }}",
                "type": "GET"
            },
            "columns": [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "date"
                },
                {
                    "data": "surname"
                },
                {
                    "data": "kyc_actions"
                },
                {
                    "data": "kyc_status"
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
    <script>
        $('#users_tbl').DataTable({
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
                "url": "{{ route('allWalkInCusList') }}",
                "type": "GET"
            },
            "columns": [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "date"
                },
                {
                    "data": "surname"
                },
                {
                    "data": "kyc_actions"
                },
                {
                    "data": "kyc_status"
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
