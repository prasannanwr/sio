@extends('admin.app')
@section('page_title', 'Blog Update')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Delivery man Status Change </h5>

                @include('admin.partials.notification')
                <hr>
                <form action="{{ route('updateDeliverManStatus') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        {{-- {{ dd($users) }} --}}
                        <input type="hidden" name="id" value="{{ $id }}">
                        <label for="name" class="form-label"> Status </label>
                        @php
                            $pickedUpOrBeyond = in_array($order->delivery_man_status, ['Picked up', 'On the way', 'Arrived', 'Delivered'], true);
                        @endphp
                        <select name="status" class="form-control" required>
                            <option value=""> Select Status </option>
                            <option @if ($order->delivery_man_status == 'Accepted') selected @endif value="accepted">Accept Order</option>
                            <option @if ($order->status == 'processing') selected @endif value="processing">Processing
                            </option>
                            <option @if ($order->delivery_man_status == 'Picked up') selected @endif value="picked_up">Picked up</option>
                            <option @if ($order->status == 'in_transit') selected @endif value="in_transit">In Transit to
                                Destination
                                Agent</option>
                            <option @if ($order->delivery_man_status == 'Arrived') selected @endif value="arrived">Arrived</option>
                            <option @if ($order->status == 'delivered') selected @endif value="delivered"> Delivered</option>
                            @unless ($pickedUpOrBeyond)
                                <option @if ($order->status == 'cancelled') selected @endif value="cancelled">Cancelled</option>
                            @endunless
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function createSlug(e) {
            let text = e.toLowerCase() // ছোট হাতের অক্ষরে রূপান্তর
                .trim() // শুরু এবং শেষের ফাঁকা জায়গা সরানো
                .replace(/[^a-z0-9\s-]/g, '') // বিশেষ ক্যারেক্টার সরানো
                .replace(/\s+/g, '-') // স্পেসকে ড্যাশে রূপান্তর
                .replace(/-+/g, '-'); // একাধিক ড্যাশ একটিতে রূপান্তর
            //alert(text)
            $("#slug").val(text);
        }
    </script>
@endsection
