@php
    use App\Models\User;
    use App\Models\Country;
    use App\Models\City;
    $dispatures = User::where('user_type', 'dispatcher')->get();
@endphp
@extends('admin.app')
@section('page_title', 'Accept Order')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">{{ __('admin.pick_up_order.name') }}</h5>
                <p class="mb-0">{{ __('admin.pick_up_order.name') }} </p>
                @include('admin.partials.notification')
                <hr>
                <form action="{{ route('agent.accept.search') }}" method="get" class="form-inline">
                    <div class="row">
                        <div class="col-sm-10">
                            <label for="order_track_id"> {{ __('admin.pick_up_order.traking_id') }}</label>
                            <input type="text" name="order_track_id" class="form-control"
                                value="{{ request()->get('order_track_id') }}" placeholder="SIPXXXXXXXXXX">
                        </div>
                        <div class="col-sm-2">
                            <br>
                            <button type="submit" class="btn btn-primary">{{ __('admin.pick_up_order.search_button') }}</button>
                        </div>
                    </div>
                </form>
                <hr>
                @if (isset($order))
                    <h5>Viewing Order [{{ $order->tracking_id }}]</h5>
                    <div class="table-reponsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Origin</th>
                                    <td>
                                        {{ $order->pickupCountry->name . ', ' . $order->pickupCity->name }}
                                        {{-- <p style="margin: 0px !important" class="text-nowrap">[Lat:
                                            {{ $order->pickupCity->latitude }}, Long:
                                            {{ $order->pickupCity->longitude }}]</p> --}}
                                        <br>
                                        Picked up at: {{ $order->pickup_time }}
                                    </td>
                                    <th>Destination / Current Location </th>
                                    <td>
                                        <b>Destination</b>:
                                        {{ $order->deliveryCountry->name . ', ' . $order->deliveryCity->name }}
                                        {{-- <p style="margin: 0px !important" class="text-nowrap">
                                            [Lat:{{ $order->deliveryCity->latitude }}, Long:
                                            {{ $order->deliveryCity->longitude }}]</p> --}}
                                        <br>

                                        <b>Current Location</b> :
                                        {{ $order->currentCountry->name . ', ' . $order->currentCity->name }}
                                        {{-- <p style="margin: 0px !important" class="text-nowrap">
                                            [Lat:{{ $order->currentCity->latitude }},
                                            Long:{{ $order->currentCity->longitude }}]</p> --}}
                                        <br>
                                        <b>Shipping Cost : </b>
                                        {{ fromEuroView(auth()->user()->currency_id ?? 0, $order->shipping_cost) }}
                                        <br>


                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <span class="badge bg-secondary">{{ $order->status }}</span>

                                            @if ($order->status == 'unpaid')
                                                <form action="{{ route('cancelWalkInAgentOrder') }}" method="post">
                                                    @csrf
                                                    <input type="hidden" value="{{ $order->id }}" name="order_id">
                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you wish to cancel this order')">Cancel
                                                        Order</button>
                                                </form>
                                            @endif
                                        </div>

                                    </td>
                                    <th>Price(&euro;) </th>
                                    <td>{{ fromEuroView(auth()->user()->currency_id ?? 0, $order->val_of_goods) }}</td>
                                </tr>
                                <tr>
                                    <th>Sender Name</th>
                                    <td>{{ $order->pickup_name }}</td>
                                    <th>Reciever Name</th>
                                    <td>{{ $order->delivery_name }}</td>
                                </tr>
                                <tr>
                                    <th>Sender Email</th>
                                    <td>{{ $order->pickup_email }}</td>
                                    <th>Reciever Email</th>
                                    <td>{{ $order->delivery_email }}</td>
                                </tr>
                                <tr>
                                    <th>Sender Phone</th>
                                    <td>{{ $order->pickup_phone }}</td>
                                    <th>Reciever Name</th>
                                    <td>{{ $order->delivery_phone }}</td>
                                </tr>
                                <tr>
                                    <th>Sender Phone Alt.</th>
                                    <td>{{ $order->pickup_phone_alt }}</td>
                                    <th>Reciever Phone Alt.</th>
                                    <td>{{ $order->delivery_phone_alt }}</td>
                                </tr>
                                <tr>
                                    <th>Sender Address 1</th>
                                    <td>{{ $order->pickup_address1 }}</td>
                                    <th>Reciever Address 1</th>
                                    <td>{{ $order->delivery_address1 }}</td>
                                </tr>
                                <tr>
                                    <th>Sender Address 2</th>
                                    <td>{{ $order->pickup_address2 }}</td>
                                    <th>Reciever Name</th>
                                    <td>{{ $order->delivery_address2 }}</td>
                                </tr>
                                <tr>
                                    <th>Sender Zip/ Postcode</th>
                                    <td>{{ $order->pickup_zip }}</td>
                                    <th>Reciever Zip/ Postcode</th>
                                    <td>{{ $order->delivery_zip }}</td>
                                </tr>
                                <tr>
                                    <th>Sender City</th>
                                    <td>{{ $order->pickup_city }}</td>
                                    @php
                                        $id = (int) $order->delivery_city;
                                        $city = City::find($id)?->name;
                                    @endphp
                                    <th>Reciever City</th>
                                    <td>{{ $city }}</td>
                                </tr>
                                <tr>
                                    <th>Sender State/ Province</th>
                                    <td>{{ $order->pickup_state }}</td>
                                    <th>Reciever State/ Province</th>
                                    <td>{{ $order->delivery_state }}</td>
                                </tr>
                                <tr>
                                    <th>Sender Country / Region</th>
                                    <td>{{ $order->pickup_country }}</td>
                                    
                                    @php
                                        $id = (int) $order->delivery_country;
                                        $country = Country::find($id)?->name;
                                    @endphp
                                    <th>Reciever Country / Region</th>
                                    <td>{{ $country }}</td>
                                </tr>
                                <tr>
                                    <th>Condition of Goods</th>
                                    <td>{{ $order->cond_of_goods }}</td>
                                    <th>Order Pick-up Commission </th>
                                    <td>{{ fromEuroView(auth()->user()->currency_id ?? 0, $order->val_of_goods * 0.015) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <h5>Order Contents</h5>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <th>Package Type</th>
                                <th>length(cm)</th>
                                <th>Width(cm)</th>
                                <th>Height(cm)</th>
                                <th>Weight(Kg)</th>
                                <th>Count/qty</th>
                            </thead>
                            <tbody id="items_list">
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            {{ $item->type }}
                                        </td>
                                        <td>
                                            {{ $item->length }}
                                        </td>
                                        <td>
                                            {{ $item->width }}
                                        </td>
                                        <td>
                                            {{ $item->height }}
                                        </td>
                                        <td>
                                            {{ $item->weight }}
                                        </td>
                                        <td>
                                            {{ $item->qty }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <hr>
                    <h5>Accept/ Assign batch</h5>
                    <p>Your Wallet will be billed for this order</p>
                    <hr>
                    @if ($order->status == 'unpaid')
                        <form action="{{ route('orderAccept', $order->id) }}" method="post">
                            @csrf
                            {{--  <div class="row">
                                <div class="col-6">
                                    <div class="ui-widget">
                                        <label for="origin">Batch <i class="text-danger">*</i> : </label>
                                        <input type="text" id="batch" name="batch_"
                                            value="{{ $order->batch ? $order->batch->name : '' }}" class="form-control"
                                            autocomplete="off" required>
                                        <input type="hidden" name="batch_id" value="{{ $order->batch_id }}"
                                            id="batch_id">
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="ui-widget">
                                        <label for="origin">Select Dispature <i class="text-danger">*</i> : </label>
                                        <select class="form-select" aria-label="Default select example"
                                            name="dispatcher_id">
                                            <option selected>Open this select menu</option>
                                            @foreach ($dispatures as $dispature)
                                                <option value="{{ $dispature->id }}">{{ $dispature->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>  --}}
                            <br>
                            <button type="submit" class="btn btn-primary"
                                onclick="return confirm('Are you sure you wish to Accept this Order and Assign it to the specified batch?')"><i
                                    class="fa fa-save"></i> Accept Order</button>
                        </form>
                    @else
                        <b>Batch Assingned : {{ $order?->batch?->name ?? 'Bathch Is Not Assigned Yet!' }}</b>

                        <hr>
                        <h5>Mark As Picked-Up</h5>
                        <p>Only fill this if the item is about to be picked Up </p>


                        <hr>
                        {{-- @if ($order->status == 'assigned') --}}
                        @if ($order->status == 'Accepted By Agent')
                            <form action="{{ route('orderPickedUp', $order->id) }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-primary"
                                    onclick="return confirm('Are you sure you wish to mark this item as Picked-up?')"><i
                                        class="fa fa-save"></i> Order Picked-up</button>
                            </form>
                        @else
                            <p><b>Note : You Can only Mark Order as Pick-Up After Approval of Admin </b></p>
                            {{--  <b>Order Has been Picked Up on - {{ $order->delivery_time }}</b>  --}}
                        @endif

                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // $('#batch').autocomplete({
        //     source: "{{ route('batches.search') }}",
        //     minLength: 1,
        //     select: function(event, ui) {
        //         // Update the hidden input field with the selected value
        //         $('#batch_id').val(ui.item.value);

        //         // Update the visible input field with the label
        //         $('#batch').val(ui.item.label);

        //         // Prevent the default behavior of filling the input with the label
        //         event.preventDefault();
        //     }
        // });
    </script>
@endsection
