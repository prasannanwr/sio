@extends('admin.app')
@section('page_title', 'Global Trade Products')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">{{ __('admin.global_product.name') }} <a href="{{ route('products.create') }}"
                        class="btn btn-primary mb-3">{{ __('admin.global_product.add_button') }}</a></h5>

                @include('admin.partials.notification')
                <hr>
                <div class="d">
                    <!-- /.card-header -->
                    <div class="row">
                        @if (isset($products) && count($products) > 0)
                            @foreach ($products as $product)
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <img src="{{ asset('images/' . $product->image) }}" class="card-img-top"
                                            alt="{{ $product->name }}">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $product->name }}</h5>
                                            <p class="card-text">{{ $product->description }}</p>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item"><strong>Price per Tonne:</strong>
                                                    &euro;{{ $product->price_per_tonne }}</li>
                                                <li class="list-group-item"><strong>Price per Kg:</strong>
                                                    &euro;{{ $product->price_per_kg }}</li>
                                                <li class="list-group-item"><strong>Origin Port:</strong>
                                                    {{ $product->origin_port }}</li>
                                                <li class="list-group-item"><strong>Supported Ports:</strong>
                                                    {{ $product->supported_ports }}</li>
                                                <li class="list-group-item"><strong>Shipping Terms:</strong>
                                                    {{ $product->shipping_terms }}</li>
                                            </ul>
                                            <div class="mt-3">
                                                <a href="{{ route('products.edit', $product->id) }}"
                                                    class="btn btn-secondary">Edit</a>
                                                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                                    class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @endforeach

                            {{$products->links()}}
                        @else
                            <p><i>No products added yet</i></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

@endsection
