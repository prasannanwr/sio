@extends('admin.app')
@section('page_title', 'Blog Update')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Blog Update <a href="{{ route('blogEntry.index') }}"
                        class="btn btn-primary mb-3">All Blogs</a></h5>

                @include('admin.partials.notification')
                <hr>
                <form action="{{ route('blogEntry.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Title </label>
                        <input type="text" name="title" onkeyup="createSlug(this.value)" class="form-control" value="{{ $blog->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label"> Slug </label>
                        <input type="text" readonly id="slug" name="slug" class="form-control" value="{{ $blog->slug }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="content" class="form-control">{{ $blog->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Product Image</label>
                        @if ($blog->image)
                            <br>
                            <img src="{{ asset('images/' . $blog->image) }}" style="width:200px;">
                            <br>
                            <br>
                        @endif
                        <span>Leave blank if you don't want to change image</span>
                        <input type="file" name="image" class="form-control">
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
