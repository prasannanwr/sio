@extends('admin.app')
@section('page_title', 'Create Batch')
@section('content')
    <div class="container-fluid">
        {{-- <div class="card"> --}}
        {{-- <div class="card-body"> --}}
        {{-- <h5 class="card-title fw-semibold mb-4">Settings</h5> --}}
        {{-- @include('dispatcher.settings.nav') --}}
        {{-- <hr> --}}
        <div class="card">
            <div class="card-header">
                <h5>Create New Batch</h5>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                @include('admin.partials.notification')
                
                <form action="{{ route('batches.store') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name", id="name">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-12">
                            <div class="ui-widget">
                                <label for="origin">Shipping from <i class="text-danger">*</i> : </label>
                                <input type="text" id="origin" name="origin_" value="{{ old('origin_') }}"
                                    class="form-control" autocomplete="off">
                                <input type="hidden" name="origin_id" value="{{ old('origin_id') }}" id="origin_id">
                            </div>
                        </div>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                </form>
            </div>
        </div>
    </div>
    {{-- </div> --}}
    {{-- </div> --}}
@endsection
@section('scripts')
    <script>
        $('#origin').autocomplete({
            source: "{{ route('locations.search') }}",
            minLength: 1,
            select: function(event, ui) {
                // Update the hidden input field with the selected value
                $('#origin_id').val(ui.item.value);

                // Update the visible input field with the label
                $('#origin').val(ui.item.label);

                // Prevent the default behavior of filling the input with the label
                event.preventDefault();
            }
        });
    </script>
@endsection
