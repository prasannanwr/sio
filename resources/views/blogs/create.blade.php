@extends('admin.app')
@section('page_title', 'Blog Entry')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold">{{ __('admin.blog.name') }}</h5>
                    <a href="{{ route('blogEntry.index') }}" class="btn btn-primary">{{ __('admin.blog.name1') }}</a>
                </div>
                

                @include('admin.partials.notification')
                <hr>
                <form action="{{ route('blogEntry.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Blog Title</label>
                        <input type="text" name="title" onkeyup="createSlug(this.value)" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="slug" class="form-label">Blog Slug</label>
                        <input type="text" id="slug" readonly name="slug" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="content" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Product Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary"> {{ __('admin.blog.submit_button') }} </button>
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
