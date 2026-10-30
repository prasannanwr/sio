@extends('admin.app')
@section('page_title', 'Profile')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">My Profile</h5>
                <p class="mb-0">This is the Dispatcher settings page </p>
                @include('dispatcher.settings.nav')
                @include('admin.partials.notification')
                <hr>
                <form action="{{ route('dispatchers.update', $dispatcher->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="form-group">
                            <label for="name">Name/ Business Name</label>
                            <input type="text" name="name" class="form-control" id="name"
                                value="{{ $dispatcher->name }}" required>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" class="form-control" id="phone"
                                value="{{ $dispatcher->phone }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="phone_alt">Phone Alt</label>
                            <input type="text" name="phone_alt" class="form-control" id="phone_alt"
                                value="{{ $dispatcher->phone_alt }}">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="agency-type" class="col-md-4 col-form-label">{{ __('Agency Type') }} <i
                                    class="text-danger">*</i></label>
                            <select name="agency_type" id="agency_type" class="form-control">
                                <option value="person" {{(($dispatcher->agency_type == 'person') ? 'selected' : '')}}>Personal</option>
                                <option value="agency" {{(($dispatcher->agency_type == 'agency') ? 'selected' : '')}}>Agency</option>
                                <option value="public_admin" {{(($dispatcher->agency_type == 'public_admin') ? 'selected' : '')}}>Public Adminiatration</option>
                            </select>
                            @error('agency_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                        <div class="col-md-6">
                            <label for="business-name" class="col-md-4 col-form-label">Business Name</label>
                            <input id="business-name" type="text" class="form-control" value="{{ $dispatcher->business_name }}" name="business_name">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="tax-id-code" class="col-md-4 col-form-label">Tax ID Code <i
                                    class="text-danger">*</i></label>
                            <input id="tax-id-code" type="text" class="form-control" name="tax_id_code" value="{{ $dispatcher->tax_id_code }}" required>
                            @error('tax_id_code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="vat-no" class="col-md-4 col-form-label">VAT Number</label>
                            <input id="vat-no" type="text" class="form-control" value="{{ $dispatcher->vat_no }}" name="vat_no">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="pec" class="col-md-4 col-form-label">PEC</label>
                            <input id="pec" type="text" class="form-control" value="{{ $dispatcher->pec }}" name="pec">
                        </div>
                        <div class="col-md-6">
                            <label for="sdi" class="col-md-4 col-form-label">Unique SDI Code</label>
                            <input id="sdi" type="text" class="form-control" value="{{ $dispatcher->sdi }}" name="sdi">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group">
                            <label for="address1">Address 1</label>
                            <textarea name="address1" class="form-control" id="address1">{{ $dispatcher->address2 }}</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group">
                            <label for="address2">Address 2(additional decriptions)</label>
                            <textarea name="address2" class="form-control" id="address2">{{ $dispatcher->address1 }}</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="zip">zip</label>
                            <input type="text" name="zip" class="form-control" id="zip"
                                value="{{ $dispatcher->zip }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="city">City</label>
                            <input type="text" name="city" class="form-control" id="city"
                                value="{{ $dispatcher->city }}" required>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="state">State/ Province</label>
                            <input type="text" name="state" class="form-control" id="state"
                                value="{{ $dispatcher->state }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="country">Country</label>
                            <input type="text" name="country" class="form-control" id="country"
                                value="{{ $dispatcher->country }}" required>
                        </div>
                    </div>
                    <br>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save</button>
                </form>
            </div>
        </div>
    </div>
@endsection
