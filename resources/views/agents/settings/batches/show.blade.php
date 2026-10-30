@extends('admin.app')
@section('page_title', 'Location')
@section('content')
    <div class="container-fluid">
        {{-- <div class="card"> --}}
        {{-- <div class="card-body"> --}}
        {{-- <h5 class="card-title fw-semibold mb-4">Settings</h5> --}}
        {{-- @include('dispatcher.settings.nav') --}}
        {{-- <hr> --}}
        <div class="card">
            <div class="card-header">
               <h5>Show Batch</h5> 
                {{-- <a href="{{ route('batches.index') }}" class="btn btn-danger float-right"><i
                                class="fa fa-times"></i>Exit</a> --}}
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <p><b>Batch Name : [{{ $batch->name }}]</b></p>
                <p><b>Current Location : {{ $batch->location->name }}Lat: {{ $batch->location->latitude }} &nbsp;
                    &nbsp; Long: {{ $batch->location->longitude }} </b></p>
                <hr>
                <h5>Geolocation data</h5>
                <hr>
                <div id="map" style="height: 400px;"></div>

                <hr>
                <h5>Orders in this batch</h5>
                <hr>
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
                                <th>View / Track</th>
                                <th>Edit</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- </div> --}}
    {{-- </div> --}}
@endsection
@section('scripts')
    <script>
        // Get latitude and longitude values from the server
        var latitude = {{ $batch->location->latitude }};
        var longitude = {{ $batch->location->longitude }};

        // Initialize the map
        var map = L.map('map').setView([latitude, longitude], 10); // 16 is the zoom level

        // Add OpenStreetMap as the base layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add a marker at the specified location
        L.marker([latitude, longitude]).addTo(map)
            .bindPopup('{{ $batch->location->name }}')
            .openPopup();
    </script>
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
                "url": "{{ route('batchOrdersList', $batch->id) }}",
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
                {
                    "data": "edit"
                },
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
