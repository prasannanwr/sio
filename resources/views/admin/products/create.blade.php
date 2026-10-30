@extends('admin.app')
@section('page_title', 'Edit Global Trade Product')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">{{ __('admin.global_product.form_title') }} <a href="{{ route('products.index') }}"
                        class="btn btn-primary mb-3">{{ __('admin.global_product.name1') }}</a></h5>

                @include('admin.partials.notification')
                <hr>
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <fieldset style="border: 1px solid #ddd;padding:10px;margin-bottom:10px;">
                        <legend style="    margin-top: -26px;
    background: white;
    width: 82px;
    margin-left: 20px;
    font-size: 20px;
    font-weight: bold;">English:</legend>
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control"></textarea>
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
                        <input type="text" name="name_ar" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description_ar" class="form-control"></textarea>
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
                        <input type="text" name="name_it" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description_it" class="form-control"></textarea>
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
                        <input type="text" name="name_ch" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description_ch" class="form-control"></textarea>
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
                        <input type="text" name="name_fr" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description_fr" class="form-control"></textarea>
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
                        <input type="text" name="name_dt" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description_dt" class="form-control"></textarea>
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
                        <input type="text" name="name_pt" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description_pt" class="form-label">Description</label>
                        <textarea name="description_pt" class="form-control"></textarea>
                    </div>
                </fieldset>



                    <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label for="price_per_tonne" class="form-label">Price per Tonne(&euro;)</label>
                            <input type="text" name="price_per_tonne" class="form-control" required>
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label for="price_per_kg" class="form-label">Price per Kg(&euro;)</label>
                            <input type="text" name="price_per_kg" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label for="origin_port" class="form-label">Origin Ports(comma seperated)</label>
                            <input type="text" name="origin_port" class="form-control" required>
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label for="supported_ports" class="form-label">Supported Ports(comma seperated)</label>
                            <input type="text" name="supported_ports" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="shipping_terms" class="form-label">Shipping Terms</label>
                        <input placeholder="e.g CIF, FOB" type="text" name="shipping_terms" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Product Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('admin.global_product.submit_button') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    
@endsection
