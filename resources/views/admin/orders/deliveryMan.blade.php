@extends('admin.app')
@section('page_title', 'Blog Update')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Delivery man assign</h5>

                @include('admin.partials.notification')
                <hr>
                <form action="{{ route('updateDeliveryMan') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        {{-- {{ dd($users) }} --}}
                        <input type="hidden" name="id" value="{{ $id }}">
                        <label for="name" class="form-label">Delivery Man </label>
                        <select  name="deliveryMan" class="form-control" required>
                            <option value=""> Select Delivery Man </option>
                            @foreach ($users as $user)
                            <option value="{{ $user['id'] }}"> {{ $user['name'] }} &nbsp; ({{ $user['country'] }})</option>
                            @endforeach
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
    function createSlug(e){
            let text  = e.toLowerCase()                // ছোট হাতের অক্ষরে রূপান্তর
        .trim()                       // শুরু এবং শেষের ফাঁকা জায়গা সরানো
        .replace(/[^a-z0-9\s-]/g, '') // বিশেষ ক্যারেক্টার সরানো
        .replace(/\s+/g, '-')         // স্পেসকে ড্যাশে রূপান্তর
        .replace(/-+/g, '-');         // একাধিক ড্যাশ একটিতে রূপান্তর
            //alert(text)
            $("#slug").val(text);
        }
</script>
@endsection
