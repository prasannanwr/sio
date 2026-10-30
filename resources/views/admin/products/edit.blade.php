@extends('admin.app')
@section('page_title', 'Create Global Trade Product')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Create Global Trade Product <a href="{{ route('products.index') }}"
                        class="btn btn-primary mb-3">All Products</a></h5>

                @include('admin.partials.notification')
                <hr>
                <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    
                    <fieldset style="border: 1px solid #ddd;padding:10px;margin-bottom:10px;">
                        <legend style="    margin-top: -26px;
    background: white;
    width: 82px;
    margin-left: 20px;
    font-size: 20px;
    font-weight: bold;">English:</legend>
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" value="{{$product->name}}" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control"> {{$product->description}}</textarea>
                    </div>
                </fieldset>


                    <fieldset style="border: 1px solid #ddd;padding:10px;margin-bottom:10px;">
                        <legend style="    margin-top: -26px;
    background: white;
    width: 82px;
    margin-left: 20px;
    font-size: 20px;
    font-weight: bold;">arabic:</legend>
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" name="name_ar" class="form-control" value="{{$product->name_ar}}" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description_ar" class="form-control"> {{$product->description_ar}}</textarea>
                    </div>
                </fieldset>


                    <fieldset style="border: 1px solid #ddd;padding:10px;margin-bottom:10px;">
                        <legend style="    margin-top: -26px;
    background: white;
    width: 82px;
    margin-left: 20px;
    font-size: 20px;
    font-weight: bold;">Italiano:</legend>
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" name="name_it" class="form-control" value="{{$product->name_it}}" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description_it" class="form-control"> {{$product->description_it}}</textarea>
                    </div>
                </fieldset>


                    <fieldset style="border: 1px solid #ddd;padding:10px;margin-bottom:10px;">
                        <legend style="    margin-top: -26px;
    background: white;
    width: 82px;
    margin-left: 20px;
    font-size: 20px;
    font-weight: bold;">Cinese:</legend>
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" name="name_ch" class="form-control" value="{{$product->name_ch}}" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description_ch" class="form-control"> {{$product->description_ch}}</textarea>
                    </div>
                </fieldset>


                    <fieldset style="border: 1px solid #ddd;padding:10px;margin-bottom:10px;">
                        <legend style="    margin-top: -26px;
    background: white;
    width: 82px;
    margin-left: 20px;
    font-size: 20px;
    font-weight: bold;">France:</legend>
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" name="name_fr" class="form-control" value="{{$product->name_fr}}" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description_fr" class="form-control"> {{$product->description_fr}}</textarea>
                    </div>
                </fieldset>


                    <fieldset style="border: 1px solid #ddd;padding:10px;margin-bottom:10px;">
                        <legend style="    margin-top: -26px;
    background: white;
    width: 82px;
    margin-left: 20px;
    font-size: 20px;
    font-weight: bold;">Dauch:</legend>
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" name="name_dt" class="form-control" value="{{$product->name_dt}}" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description_dt" class="form-control"> {{$product->description_dt}}</textarea>
                    </div>
                </fieldset>


                    <fieldset style="border: 1px solid #ddd;padding:10px;margin-bottom:10px;">
                        <legend style="margin-top: -26px;
    background: white;
    width: 92px;
    margin-left: 20px;
    font-size: 20px;
    font-weight: bold;">Portugal:</legend>
                    <div class="mb-3">
                        <label for="name_pt" class="form-label">Product Name</label>
                        <input type="text" name="name_pt" class="form-control" value="{{$product->name_pt}}" required>
                    </div>
                    <div class="mb-3">
                        <label for="description_pt" class="form-label">Description</label>
                        <textarea name="description_pt" class="form-control"> {{$product->description_pt}}</textarea>
                    </div>
                </fieldset>

                    <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label for="price_per_tonne" class="form-label">Price per Tonne(&euro;)</label>
                            <input type="text" name="price_per_tonne" value="{{$product->price_per_tonne}}" class="form-control" required>
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label for="price_per_kg" class="form-label">Price per Kg(&euro;)</label>
                            <input type="text" name="price_per_kg" value="{{$product->price_per_kg}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label for="origin_port" class="form-label">Origin Ports(comma seperated)</label>
                            <input type="text" name="origin_port" value="{{$product->origin_port}}" class="form-control" required>
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label for="supported_ports" class="form-label">Supported Ports(comma seperated)</label>
                            <input type="text" name="supported_ports" value="{{$product->supported_ports}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="shipping_terms" class="form-label">Shipping Terms</label>
                        <input placeholder="e.g CIF, FOB" type="text" value="{{$product->shipping_terms}}" name="shipping_terms" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Product Image( <small>Leave blank if you don't want to change image</small> )</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    
@endsection
