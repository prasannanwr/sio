@extends('admin.app')
@section('page_title', 'Edit Order')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Settings</h5>
                @include('dispatcher.settings.nav')
                <hr>
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route('batches.index') }}" class="btn btn-danger float-right"><i
                                class="fa fa-times"></i>Exit</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        @include('admin.partials.notification')
                        <h5>Edit Order [{{$order->tracking_id}}]</h5>
                        <form action="{{ route('batchOrderEdit', $order->id) }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="ui-widget">
                                        <label for="origin">Batch <i class="text-danger">*</i> : </label>
                                        <input type="text" id="batch" name="batch_" value="{{(($order->batch) ? $order->batch->name : '')}}"
                                            class="form-control" autocomplete="off" required>
                                        <input type="hidden" name="batch_id" value="{{$order->batch_id}}"
                                            id="batch_id">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-12">
                                    <div class="ui-widget">
                                        <label for="origin">Current Location <i class="text-danger">*</i> : </label>
                                        <input type="text" id="current_location_" name="origin_" value="{{$loc_str}}"
                                            class="form-control" autocomplete="off" required>
                                        <input type="hidden" name="current_location_id" value="{{ $order->current_location_id }}"
                                            id="current_location_id">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="assigned" {{(($order->status == 'assigned') ? 'selected' : '')}}>Processing</option>
                                        <option value="in_transit" {{(($order->status == 'in_transit') ? 'selected' : '')}}>In Transit</option>
                                        <option value="delivered" {{(($order->status == 'delivered') ? 'selected' : '')}}>Delivered</option>
                                        <option value="cancelled" {{(($order->status == 'cancelled') ? 'selected' : '')}}>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <button type="submit" onclick="return confirm('Are you sure you wish to update this Order?')" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('#current_location_').autocomplete({
            source: "{{ route('locations.search') }}",
            minLength: 1,
            select: function(event, ui) {
                // Update the hidden input field with the selected value
                $('#current_location_id').val(ui.item.value);

                // Update the visible input field with the label
                $('#current_location_').val(ui.item.label);

                // Prevent the default behavior of filling the input with the label
                event.preventDefault();
            }
        });
        $('#batch').autocomplete({
            source: "{{ route('batches.search') }}",
            minLength: 1,
            select: function(event, ui) {
                // Update the hidden input field with the selected value
                $('#batch_id').val(ui.item.value);

                // Update the visible input field with the label
                $('#batch').val(ui.item.label);

                // Prevent the default behavior of filling the input with the label
                event.preventDefault();
            }
        });
    </script>
@endsection
