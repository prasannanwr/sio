@extends('admin.app')
@section('page_title', 'Users')
@section('css')
    <style>
        .pdf {
            width: 100%;
            aspect-ratio: 4 / 3;
        }
    </style>

@endsection
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs" id="adminTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-users-tab" data-bs-toggle="tab"
                            data-bs-target="#all-users-tab-pane" type="button" role="tab"
                            aria-controls="all-users-tab-pane" aria-selected="true">{{ __('admin.manage_user.all_user') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="agent-kyc-tab" data-bs-toggle="tab"
                            data-bs-target="#agent-kyc-tab-pane" type="button" role="tab"
                            aria-controls="agent-kyc-tab-pane" aria-selected="false">{{ __('admin.manage_user.all_agent') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="mobile-kyc-tab" data-bs-toggle="tab"
                            data-bs-target="#mobile-kyc-tab-pane" type="button" role="tab"
                            aria-controls="mobile-kyc-tab-pane" aria-selected="false">{{ __('admin.manage_user.dispatcher.name') }}</button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                @include('admin.partials.notification')
                <div class="tab-content" id="adminTabContent">
                    <div class="tab-pane fade show active" id="all-users-tab-pane" role="tabpanel"
                        aria-labelledby="customer-kyc" tabindex="0">
                        <h5 class="card-title fw-semibold mb-4">{{ __('admin.manage_user.all_user1') }}</h5>
                        <hr>
                        <div class="d">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="users_tbl" class="table table-sm  table-bordered table-striped display">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Date</th>
                                                <th>Particulers</th>
                                                <th>User type</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="agent-kyc-tab-pane" role="tabpanel" aria-labelledby="agent-kyc"
                        tabindex="0">
                        <h5 class="card-title fw-semibold mb-4">{{ __('admin.manage_user.all_agent1') }}</h5>
                        <hr>
                        <div class="d2">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="agents_tbl" class="table table-sm  table-bordered table-striped"
                                        style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Date</th>
                                                <th>Particulers</th>
                                                {{-- <th>Account type</th> --}}
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="mobile-kyc-tab-pane" role="tabpanel" aria-labelledby="mobile-kyc"
                        tabindex="0">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title fw-semibold mb-4">{{ __('admin.manage_user.dispatcher.name1') }}</h5>
                            <a href="{{route('create.dispatcher')}}" class="btn btn-primary" style="height: 38px !important;">{{ __('admin.manage_user.dispatcher.add_button') }}</a>
                        </div>
                        <hr>
                        <div class="d3">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="dispatcher_tbl" class="table table-sm  table-bordered table-striped"
                                        style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Date</th>
                                                <th>Particulers</th>
                                                {{-- <th>Account type</th> --}}
                                                <th>Status</th>
                                                <th>Actions</th>

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
                "url": "{{ route('allUsersList') }}",
                "type": "GET"
            },
            "columns": [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "date"
                },
                {
                    "data": "email"
                },
                {
                    "data": "user_type"
                },
                {
                    "data": "blocked"
                },
                {
                    "data": "actions"
                },
            ],
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true
        });

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
                    "data": "email"
                },
                {
                    "data": "blocked"
                },
                {
                    "data": "actions"
                },
            ],
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true
        });

        $('#dispatcher_tbl').DataTable({
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
                "url": "{{ route('allDispatcherList') }}",
                "type": "GET"
            },
            "columns": [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "date"
                },
                {
                    "data": "email"
                },
                {
                    "data": "blocked"
                },
                {
                    "data": "actions"
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
