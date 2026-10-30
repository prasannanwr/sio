@extends('admin.app')
@section('page_title', 'New Shipping Order')
@section('css')
    <style>
        .select2-selection__arrow {
            display: none !important;
        }

        .select2-results__options {
            overflow-y: clip !important;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">{{ __('admin.all_order_agent.form_title') }}</h5>
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                @endif
                <hr>
                <form action="{{ route('walkInOrderAgents.store') }}" method="post" id="order-form"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="ui-widget">
                                <label for="tax_code">Customer No / Tax code <i class="text-danger">*</i> : </label>
                                <input type="text" id="tax_code" name="tax_code_" value="{{ old('tax_code_') }}"
                                    class="form-control" autocomplete="off" required>
                                @error('tax_code_')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <button type="button" onclick="verifyTaxCode()" class="btn btn-primary mt-4">Verify</button>
                        </div>
                        <br>
                        <em id="tax_code_status_text"></em>
                        <br>
                        <em id="kyc_status_text"></em>
                    </div>
                    {{-- <hr> --}}


                    <div class="row">
                        <div class="col-md-6">
                        <div class="col-md-12" style="border:1px solid #646464;padding:10px;">
                            <h4> <u>{{ __('admin.all_order_agent.sender') }}</u>  </h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="ui-widget">
                                        <label for="company_name1">Company Name<i class="text-danger">*</i> : </label>
                                        <input type="text" id="company_name1" name="company_name1" value="{{ old('company_name1') }}"
                                            class="form-control" autocomplete="off">
                                        @error('company_name1')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="ui-widget">
                                        <label for="surname">Surname <i class="text-danger">*</i> : </label>
                                        <input type="text" id="surname" name="surname_" value="{{ old('surname_') }}"
                                            class="form-control" autocomplete="off">
                                        @error('surname_')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                        <input type="hidden" id="cus_id" name="cus_id">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ui-widget">
                                        <label for="name">Firstname <i class="text-danger">*</i> : </label>
                                        <input type="text" id="name" name="name_" value="{{ old('name_') }}"
                                            class="form-control" autocomplete="off">
                                        @error('name_')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- <br> --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="ui-widget">
                                        <label for="gender">Gender <i class="text-danger">*</i> : </label>
                                        <select class="form-select" name="gender_" id="gender">
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
    
                                        @error('gender_')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ui-widget">
                                        <label for="dob">D.O.B <i class="text-danger">*</i> : </label>
                                        <input type="date" id="dob" name="dob_" value="{{ old('dob_') }}"
                                            class="form-control" autocomplete="off">
                                        @error('dob_')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="ui-widget">
                                        <label for="sender_email"> Email <i class="text-danger">*</i> : </label>
                                        <input type="text" id="sender_email" name="sender_email_" value="{{ old('sender_email_') }}"
                                            class="form-control" autocomplete="off">
                                        @error('sender_email_')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ui-widget">
                                        <label for="sender_phone">Sender Phone <i class="text-danger">*</i> : </label>
                                        <input type="text" id="sender_phone" name="sender_phone_" value="{{ old('sender_phone_') }}"
                                            class="form-control" autocomplete="off">
                                        @error('sender_phone_')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                        
                                    </div>
                                </div>
                                
                            </div>

                            <div class="row">
                                
                                <div class="col-md-6">
                                    <div class="ui-widget">
                                        <label for="sender_address"> Address <i class="text-danger">*</i> : </label>
                                        <input type="text" id="sender_address" name="sender_address_" value="{{ old('sender_address_') }}"
                                            class="form-control" autocomplete="off">
                                        @error('sender_address_')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="ui-widget">
                                        <label for="origin">Sender Country<i class="text-danger">*</i> : </label>
                                        <select name="ship_from_country" id="ship_from_country" class="form-control">
    
                                        </select>
                                        @error('ship_from_country')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
    
    
                                    </div>
                                </div>
    
                                <div class="col-6">
                                    <div class="ui-widget">
                                        <label for="origin">Sender City<i class="text-danger">*</i> : </label>
                                        <select name="ship_from_city" id="ship_from_city" class="form-control">
                                        </select>
                                        @error('ship_from_city')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="ui-widget">
                                        <label for="ship_from_state">Sender State<i class="text-danger">*</i> : </label>
                                        <input name="ship_from_state" id="ship_from_state"
                                            class="form-control form-control-sm" placeholder="Enter Sender State">
                                        @error('ship_from_state')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="ui-widget">
                                        <label for="ship_from_zip">Sender Zip/Postal code<i class="text-danger">*</i> : </label>
                                        <input name="ship_from_zip" id="ship_from_zip"
                                            class="form-control form-control-sm" placeholder="Enter Sender Zip/Postal code">
                                        @error('ship_from_zip')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>


                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="ui-widget">
                                        <label for="gender">Document Type  : </label>
                                        <select name="doc_type_" id="doc_type" class="form-control">
                                            <option value="">--select document type--</option>
                                            <option value="EU National ID Card / Carta d'Identita' Europea">EU National ID Card
                                                / Carta d'Identita' Europea</option>
                                            <option value="ID Card / Carta Di Identità">ID Card / Carta Di Identità </option>
                                            <option value="EU Driving License / Patente Di Guida">EU Driving License / Patente
                                                Di Guida</option>
                                            <option value="Others / Altro">Others / Altro</option>
                                            <option value="Passport / Passporto">Passport / Passporto</option>
                                            <option value="EU Resident Card / Permesso Di Soggiorno">EU Resident Card / Permesso
                                                Di Soggiorno</option>
                                        </select>
                                        @error('doc_type_')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ui-widget">
                                        <label for="doc_num">Document Number  : </label>
                                        <input type="text" id="doc_num" name="doc_num_" value="{{ old('doc_num_') }}"
                                            class="form-control" autocomplete="off">
                                        @error('doc_num_')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="ui-widget">
                                        <label for="doc_front">Document Front  : </label>
                                        <input type="file" id="doc_front" name="doc_front_" class="form-control">
                                        @error('doc_front_')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                        <br>
                                        <img src="" alt="doc_front" id="doc_front_img"
                                            style="width: 100%; border:1px solid gray">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ui-widget">
                                        <label for="doc_back">Document back  : </label>
                                        <input type="file" id="doc_back" name="doc_back_" class="form-control">
                                        @error('doc_back_')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                        <br>
                                        <img src="" alt="doc_back" id="doc_back_img"
                                            style="width: 100%; border:1px solid gray">
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>

                        <div class="col-md-6 pt-2 pb-2" style="border:1px solid #646464">
                            <h4> <u>{{ __('admin.all_order_agent.receiver') }}</u>  </h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="ui-widget">
                                        <label for="company_name2">Company Name<i class="text-danger">*</i> : </label>
                                        <input type="text" id="company_name2" name="company_name2" value="{{ old('company_name2') }}"
                                            class="form-control" autocomplete="off">
                                        @error('company_name2')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="rx_name">Receiver Name <i class="text-danger">*</i></label>
                                    <input type="text" name="rx_name" value="{{ old('rx_name') }}"
                                        class="form-control">
                                    @error('rx_name')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="rx_email">Receiver Email <i class="text-danger">*</i></label>
                                    <input type="email" name="rx_email" value="{{ old('rx_email') }}"
                                        class="form-control">
                                    @error('rx_email')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="rx_phone">Receiver Phone <i class="text-danger">*</i></label>
                                    <input type="text" name="rx_phone" value="{{ old('rx_phone') }}"
                                        class="form-control">
                                    @error('rx_phone')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="rx_phone_alt">Receiver Phone Alt. (optional)</label>
                                    <input type="text" name="rx_phone_alt"
                                        value="{{ old('rx_phone_alt') }}" class="form-control">
                                    @error('rx_phone_alt')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rx_aadress1">Address1 <i class="text-danger">*</i></label>
                                    <textarea name="rx_address1" class="form-control">{{ old('rx_address1') }}</textarea>
                                    @error('rx_address1')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rx_aadress2">Address 2</label>
                                    <textarea name="rx_address2" value="{{ old('rx_address2') }}" class="form-control"></textarea>
                                    @error('rx_address2')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            </div>
                            <br>
                           
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="rx_zip">Receiver zip <i class="text-danger">*</i></label>
                                    <input type="text" name="rx_zip" value="{{ old('rx_zip') }}"
                                        class="form-control">
                                    @error('rx_zip')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="origin">Receiver Country<i class="text-danger">*</i> :
                                    </label>
                                    <select name="customer_country_id" id="customer_country_id"
                                        class="form-control">

                                    </select>
                                    @error('customer_country_id')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-12 col-lg-6 ">
                                    <label for="origin">Receiver City<i class="text-danger">*</i> : </label>
                                    <select name="customer_city_id" id="customer_city_id"
                                        class="form-control">
                                    </select>
                                    @error('customer_city_id')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="origin">Receiver State<i class="text-danger">*</i> :
                                    </label>
                                    <input name="customer_state_id" id="customer_state_id"
                                        class="form-control form-control-sm" placeholder="Enter State Name">


                                    @error('customer_state_id')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="stage0">
                        
                        {{-- <hr>
                        <h5>Shipping details</h5>
                        <hr>
                        <div class="row">
                            
                            <div class="col-8 mb-2">
                            </div>

                            
                        </div> --}}
                        <div class="row d-none">
                            <div class="col-4 mb-2">
                                <div class="ui-widget">
                                    <label for="ship_to_country1">Shipping to Country <i class="text-danger">*</i> :
                                    </label>
                                    <input name="ship_to_country" id="ship_to_country1" class="form-control">
{{-- 
                                    </input> --}}
                                    @error('ship_to_country')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-4 mb-2">
                                <div class="ui-widget">
                                    <label for="ship_to_city1">Shipping to City<i class="text-danger">*</i> : </label>
                                    <input name="ship_to_city" id="ship_to_city1" class="form-select select2">
                                    
                                    @error('ship_to_city')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-4 mb-2">
                                <div class="ui-widget">
                                    <label for="ship_to_state">Enter State Name<i class="text-danger">*</i> : </label>
                                    <input name="ship_to_state" id="ship_to_state" class="form-control form-control-sm"
                                        placeholder="Enter State Name">
                                    @error('ship_to_state')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <hr>


                        <div class="row">
                            <div class="col-4 mb-2">
                                <div class="ui-widget">
                                    <label for="sh_id">G.SH No<i class="text-danger">*</i> : </label>
                                    <input type="text" name="sh_id" id="sh_id"
                                        class="form-control form-control-sm" placeholder="Enter G.SH Id">
                                    @error('sh_id')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        
                        <div class="repeater">

                            


                            <div data-repeater-list="items">
                                <div class="text-end">
                                    <button class="btn btn-primary btn-sm" type="button" data-repeater-create><i
                                            class="fa fa-plus"></i> Add</button>
                                </div>

                                <div data-repeater-item class="mt-2">
                                   
                                        <div class="row">
                                        <div class="col">
                                            <label for="">Select Package</label>
                                            <select name="type" class="form-control type">
                                                <option value="">--select package type--</option>
                                                <option value="percel">Percel</option>
                                                <option value="doc">Document</option>
                                                <option value="pallet">Pallet</option>
                                            </select>
                                            @error('type')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col">
                                            <label for="">Length</label>
                                            <input type="number" step="any" min="0" value="0"
                                                class="form-control len" name="len">
                                            @error('len')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col">
                                            <label for="">Width </label>
                                            <input type="number" step="any" min="0" value="0"
                                                class="form-control width" name="width">
                                            @error('width')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col">
                                            <label for="">Height </label>
                                            <input type="number" step="any" min="0" value="0"
                                                class="form-control height" name="height">
                                            @error('height')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col">
                                            <label for="">Weight </label>
                                            <input type="number" step="any" min="0" value="0"
                                                class="form-control weight" name="weight">
                                            @error('weight')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col">
                                            <label for="">Dscription </label>
                                            <input type="text" class="form-control" name="item_desc"
                                                placeholder="Description...">
                                            @error('item_desc')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col">
                                            <label for="">Price </label>
                                            <input type="number" step="any" min="0" value="0.0"
                                                class="form-control itemvalue" name="item_value">
                                            @error('item_value')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col">
                                            <label for="">Quantitty </label>
                                            <input type="number" step="1" min="1" value="1"
                                                class="form-control count" name="count">
                                            @error('count')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="text-end mt-2">
                                        <button data-repeater-delete class="btn btn-sm btn-danger" type="button"><i
                                                class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <p class="text-nowrap">Packages: <span id="type-tot"></span></p>
                                <input type="hidden" name="type_tot" id="type_tot">
                            </div>
                            <div class="col">
                                <p class="text-nowrap">Total: <span id="len-tot"></span></p>
                                <input type="hidden" name="len_tot" id="len_tot">
                            </div class="col">
                            <div class="col">
                                <p class="text-nowrap">Total: <span id="width-tot"></span></p>


                                <input type="hidden" name="width_tot" id="width_tot">
                            </div>
                            <div class="col">
                                <p class="text-nowrap"> Total : <span id="height-tot"></span></p>
                                <input type="hidden" name="height_tot" id="height_tot">
                            </div>
                            <div class="col">
                                <p class="text-nowrap">Total : <span id="weight-tot"></span></p>
                                <input type="hidden" name="weight_tot" id="weight_tot">
                            </div>
                            <div class="col">
                                {{-- Content <i class="text-danger">*</i> --}}
                            </div>
                            <div class="col">
                                <p class="text-nowrap">Total : <span id="itemvalue-tot"></span></p>
                                <input type="hidden" name="itemvalue_tot" id="itemvalue_tot">
                            </div>
                            <div class="col">
                                <p class="text-nowrap">Total : <span id="count-tot"></span></span></p>
                                <input type="hidden" name="count_tot" id="count_tot">
                            </div>
                        </div>
                        <button type="button" onclick="getRates()" class="btn btn-primary">Calculate Shiping
                            Cost</button>
                        <hr>
                        <h5>Shipping Cost</h5>
                        <div class="table-responsive">
                            <table id="locations_tbl" class="table table-sm  table-bordered table-striped display">
                                <thead>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>Ship From</i></th>
                                        <th>Ship To</th>
                                        <th>Total Weight</th>
                                        <th>Shipping Cost</th>
                                        <th>Total Price</th>
                                        <th>Carrier</th>
                                    </tr>
                                </thead>
                                <tbody id="shipping_rate_list">
                                    <div id="ajaxLoader" class="text-center my-3" style="display:none;">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </tbody>
                                <input type="hidden" name="shipping_cost" id="shipping_cost">

                            </table>
                        </div>
                    </div>

                    <div class="stage_2 ">
                        <div>
                            
                            <div class="add_set">
                                <div class="row">
                                    {{-- <div class="col-md-6">
                                        <h6>Sender Address</h6>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="s_name">Sender Name <i class="text-danger">*</i></label>
                                                <input type="text" name="s_name" class="form-control" readonly
                                                    value="{{ auth()->user()->name }}">
                                                @error('s_name')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="s_email">Sender Email <i class="text-danger">*</i></label>
                                                <input type="email" name="s_email" class="form-control" readonly
                                                    value="{{ auth()->user()->email }}">
                                                @error('s_email')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="s_phone">Sender Phone <i class="text-danger">*</i></label>
                                                <input type="text" readonly value="{{ auth()->user()->agent->phone }}"
                                                    name="s_phone" class="form-control">
                                                @error('s_phone')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="s_phone_alt">Sender Phone Alt. (optional)</label>
                                                <input type="text" readonly
                                                    value="{{ auth()->user()->agent->phone_alt }}" name="s_phone_alt"
                                                    class="form-control">
                                                @error('s_phone_alt')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="form-group">
                                                <label for="s_aadress1">Address1 <i class="text-danger">*</i></label>
                                                <textarea name="s_address1" readonly class="form-control">{{ auth()->user()->agent->address1 }}</textarea>
                                                @error('s_address1')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="form-group">
                                                <label for="s_aadress2">Address 2(additional data-optional)</label>
                                                <textarea name="s_address2" readonly class="form-control">{{ auth()->user()->agent->address2 }}</textarea>
                                                @error('s_address2')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="s_zip">Sender zip <i class="text-danger">*</i></label>
                                                <input type="text" readonly name="s_zip"
                                                    value="{{ auth()->user()->agent->zip }}" class="form-control">
                                                @error('s_zip')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="s_city">Sender city <i class="text-danger">*</i></label>
                                                <input type="text" readonly name="s_city"
                                                    value="{{ auth()->user()->agent->city->name }}" class="form-control">
                                                @error('s_city')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="s_state">Sender State/Province <i
                                                        class="text-danger">*</i></label>
                                                <input type="text" readonly name="s_state"
                                                    value="{{ auth()->user()->agent->state->name }}"
                                                    class="form-control">
                                                @error('s_state')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="s_country">Sender Country/ Region <i
                                                        class="text-danger">*</i></label>
                                                <input type="text" readonly name="s_country"
                                                    value="{{ auth()->user()->agent->country->name }}"
                                                    class="form-control" id="s_country" autocomplete="off">
                                                @error('s_country')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="r_date">Pickup Date (optional) <i class="text-danger">*</i></label>
                                                <input type="date" name="r_date" value="{{ old('r_date') }}"
                                                    class="form-control">
                                                @error('r_date')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div> --}}
                                   
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="r_date">Pickup Date (optional) <i class="text-danger">*</i></label>
                                <input type="date" name="r_date" value="{{ old('r_date') }}"
                                    class="form-control">
                                @error('r_date')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <label for="cond_of_goods">Condition Of goods/ Customs Declaration</label>
                                <input type="text" name="cond_of_goods" value="{{ old('cond_of_goods') }}"
                                    class="form-control" id="cond_of_goods">
                                @error('cond_of_goods')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="cond_of_goods">Terms Of Sale</label>
                                <select name="terms_of_sale" id="terms_of_sale" class="form-control">
                                    <option value="">--select term--</option>
                                    <option value="GIFT">Gift</option>
                                    <option value="SOLD">Sold</option>
                                    <option value="NOT_SOLD">Not Sold</option>
                                </select>
                                @error('terms_of_sale')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="customs_inv_num">Invoice Numbers(comma seperated)</label>
                                <input type="text" name="customs_inv_num" value="{{ old('customs_inv_num') }}"
                                    class="form-control" id="customs_inv_num" placeholder="e.g INV-001, INV-002">
                                @error('customs_inv_num')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="val_of_goods">Value Of the goods</label>
                                <input type="text" name="val_of_goods" value="{{ old('val_of_goods') }}"
                                    class="form-control" id="val_of_goods">
                                @error('val_of_goods')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="val_cur">Curency Of Value</label>
                                <select class="form-control" name="val_cur"id="val_cur">
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="AUD">AUD</option>
                                    <option value="CNY">CNY</option>
                                    <option value="CAD">CAD</option>
                                    <option value="AFN">AFN</option>
                                    <option value="DZD">DZD</option>
                                    <option value="BHD">BHD</option>
                                    <option value="BDT">BDT</option>
                                    <option value="INR">INR</option>
                                    <option value="NOK">NOK</option>
                                    <option value="BRL">BRL</option>
                                    <option value="BND">BND</option>
                                    <option value="INR">INR</option>
                                    <option value="XOF">XOF</option>
                                    <option value="PKR">PKR</option>
                                    <option value="QAR">QAR</option>
                                </select>
                             
                                @error('val_cur')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="invoice_document">Invoice Document</label>
                                <input type="file" name="invoice_document" value="{{ old('invoice_document') }}"
                                    class="form-control" id="invoice_document">
                                @error('invoice_document')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="cummercial_invoice">Sioshipping Commercial Invoice</label>
                                <div class="d-flex gap-3 align-items-center">
                                    <input type="file" name="cummercial_invoice" accept="pdf"
                                        value="{{ old('cummercial_invoice') }}" class="form-control"
                                        id="cummercial_invoice">
                                    <a style="white-space: nowrap" class="btn btn-primary"
                                        href="{{ asset('sioshipping-cummercail-invoice.pdf') }}" target="_blank">Create
                                        Commercial
                                        Invoice</a>
                                </div>
                                @error('cummercial_invoice')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <hr>
                        <p>By clicking Proceed bellow, you attest that you have read and understood our terms.</p>
                        <button type="submit" class="btn proccess_btn btn-primary"><i class="fa fa-save"></i>
                            Proceed</button>
                    </div>
                </form>
            </div>
        </div>
        <style>
            .d-none {
                display: none;
            }

            .d-block {
                display: block;
            }
        </style>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery.repeater@1.2.1/jquery.repeater.min.js"></script>
    <script>
        $(document).ready(function() {
            function updateImageVisibility() {
                var inputVal = $('#doc_front').val();
                if (inputVal === '') {
                    $('#doc_front_img').removeClass('d-block').addClass('d-none');
                } else {
                    $('#doc_front_img').removeClass('d-none').addClass('d-block');
                }
            }
            updateImageVisibility();
            $('#doc_front').on('change', function() {
                updateImageVisibility();
            });

            function updateImageVisibility1() {
                var inputVal = $('#doc_back').val();
                if (inputVal === '') {
                    $('#doc_back_img').removeClass('d-block').addClass('d-none');
                } else {
                    $('#doc_back_img').removeClass('d-none').addClass('d-block');
                }
            }
            updateImageVisibility1();
            $('#doc_back').on('change', function() {
                updateImageVisibility1();
            });
        });


        $('.proccess_btn').attr('disabled', true);
        $(document).ready(function() {
            $('#customer_country_id').select2();
            $('#customer_city_id').select2();
            countries();

            var customer_country_id = $("#customer_country_id");
            customer_country_id.wrap('<div class="position-relative"></div>');
            customer_country_id.on('change', function() {
                $("#customer_city_id").empty()
                $('#customer_city_id').html('<option value="">Select City</option>');

                var _token = '{{ csrf_token() }}';
                let url =
                    "{{ route('ajax-get-country-cities', ['countryId' => ':countryId']) }}"
                    .replace(':countryId', $(this).val());
                if ($(this).val() > 0) {
                    // showBlockUI();
                    $.ajax({
                        url: url,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'countryId': $(this).val(),
                            '_token': _token
                        },
                        success: function(response) {
                            if (response.success) {
                                $.each(response.cities, function(key, value) {
                                    $("#customer_city_id").append('<option value="' +
                                        value
                                        .id + '">' + value.name + '</option>');
                                });
                                $('#customer_city_id').trigger('change');
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                    title: 'Are You Sure',
                                });
                            }
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                } else {
                    console.log('FSAD');
                }

            });
        })
        $(document).ready(function() {
            function calculateTot() {
                let the_classes = ['len', 'width', 'height', 'weight',
                    'itemvalue', 'count'
                ];

                for (let i = 0; i < the_classes.length; i++) {
                    let total = 0;
                    $('.' + the_classes[i]).each(function() {
                        let value = parseFloat($(this).val()) ||
                            0;
                        total += value;
                    });
                    $('#' + the_classes[i] + '-tot').text(total.toFixed(2));
                    $('#' + the_classes[i] + '_tot').val(total.toFixed(
                        2));
                }
            }


            function calculateTotItems(item_class) {
                let total = 0;
                $('.' + item_class).each(function(i, e) {
                    if ($(this).val() != '') {
                        total = total + 1
                    }
                })
                $('#' + item_class + '-tot').text(total);
                $('#type-tot').val(total);
            }

            function bindEvents() {
                $('.len, .width, .height, .weight, .count, .itemvalue').off('keyup').on('keyup',
                    function() {
                        calculateTot();
                    });
                $('.type').off('change').on('change', function() {
                    calculateTotItems('type');
                });
            }

            $('.repeater').repeater({
                isFirstItemUndeletable: true,
                show: function() {
                    $(this).slideDown();
                    calculateTot();
                    calculateTotItems('type');
                    bindEvents();
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this element?')) {
                        $(this).slideUp(deleteElement, function() {
                            $(this).remove();
                            calculateTotItems('type');
                            calculateTot();
                        });
                    }
                }
            });

            bindEvents();
        });
        $(document).ready(function() {

            $('#ship_from_country').select2({
                // theme: "classic",
            });

            $('#ship_from_city').select2({
                // theme: "classic",
            });
            $('#ship_to_country').select2({
                // theme: "classic",
            });

            $('#ship_to_city').select2({
                // theme: "classic",
            });

            countries();



            var ship_from_country = $("#ship_from_country");
            ship_from_country.wrap('<div class="position-relative"></div>');
            ship_from_country.on('change', function() {
                $("#ship_from_city").empty()
                $('#ship_from_city').html('<option value="">Select City</option>');

                var _token = '{{ csrf_token() }}';
                let url =
                    "{{ route('ajax-get-country-cities', ['countryId' => ':countryId']) }}"
                    .replace(':countryId', $(this).val());
                if ($(this).val() > 0) {
                    // showBlockUI();
                    $.ajax({
                        url: url,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'countryId': $(this).val(),
                            '_token': _token
                        },
                        success: function(response) {
                            if (response.success) {
                                $.each(response.cities, function(key, value) {
                                    $("#ship_from_city").append('<option value="' +
                                        value
                                        .id + '">' + value.name + '</option>');
                                });

                                $("#ship_from_city").trigger('change');
                                // hideBlockUI();
                                @if (!is_null(old('residential.city')))
                                    $('#ship_from_city').val({{ old('residential.city') }});
                                    $('#ship_from_city').trigger('change')
                                @endif
                            } else {
                                // hideBlockUI();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                    title: 'Are You Sure',
                                });
                            }
                        },
                        error: function(error) {
                            console.log(error);
                            // hideBlockUI();
                        }
                    });
                } else {
                    // hideBlockUI();
                }
                // hideBlockUI();

            });


        })

        var ship_to_country = $("#ship_to_country");
        ship_to_country.wrap('<div class="position-relative"></div>');
        ship_to_country.on('change', function() {
            $("#ship_to_city").empty()
            $('#ship_to_city').html('<option value="">Select City</option>');
            var _token = '{{ csrf_token() }}';
            let url =
                "{{ route('ajax-get-country-cities', ['countryId' => ':countryId']) }}"
                .replace(':countryId', $(this).val());
            if ($(this).val() > 0) {
                // showBlockUI();
                $.ajax({
                    url: url,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'countryId': $(this).val(),
                        '_token': _token
                    },
                    success: function(response) {
                        if (response.success) {
                            $.each(response.cities, function(key, value) {
                                $("#ship_to_city").append('<option value="' +
                                    value
                                    .id + '">' + value.name + '</option>');
                            });
                            $("#ship_to_city").trigger('change');
                            @if (!is_null(old('residential.city')))
                                $('#ship_to_city').val({{ old('residential.city') }});
                                $('#ship_to_city').trigger('change')
                            @endif
                        } else {
                            // hideBlockUI();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                title: 'Are You Sure',
                            });
                        }
                    },
                    error: function(error) {
                        console.log(error);
                        // hideBlockUI();
                    }
                });
            } else {
                // hideBlockUI();
            }
            // hideBlockUI();

        });


        function countries() {

            $('#ship_from_country').html('<option value="">Select Country</option>');
            $('#ship_to_country').html('<option value="">Select Country</option>');
            $('#customer_country_id').html('<option value="">Select Country</option>');
            var _token = '{{ csrf_token() }}';
            let url = "{{ route('ajax-get-countries') }}";
            $.ajax({
                url: url,
                type: 'get',
                dataType: 'json',
                data: {
                    '_token': _token
                },
                success: function(response) {
                    if (response.success) {
                        $.each(response.countries, function(key, value) {
                            $("#ship_from_country").append('<option value="' + value.id +
                                '">' + value.name + '</option>');
                            $("#ship_to_country").append('<option value="' + value.id +
                                '">' + value.name + '</option>');
                            $("#customer_country_id").append('<option value="' + value.id + '">' + value
                                .name + '</option>')
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                        });
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }



        $(document).ready(function() {
            $('#origin').autocomplete({
                source: "{{ route('locations.search') }}",
                minLength: 1,
                select: function(event, ui) {
                    // Update the hidden input field with the selected value
                    $('#origin_id').val(ui.item.value);

                    // Update the visible input field with the label
                    $('#origin').val(ui.item.label);

                    // Prevent the default behavior of filling the input with the label
                    event.preventDefault();
                }
            });

            $('#dest').autocomplete({
                source: "{{ route('locations.search') }}",
                minLength: 1,
                select: function(event, ui) {
                    // Update the hidden input field with the selected value
                    $('#dest_id').val(ui.item.value);

                    // Update the visible input field with the label
                    $('#dest').val(ui.item.label);

                    // Prevent the default behavior of filling the input with the label
                    event.preventDefault();
                }
            });




        });







        function verifyTaxCode() {
            let tax_code = $('#tax_code').val();

            if (tax_code != '') {
                $.ajax({
                    url: "{{ route('tax.fetch') }}",
                    type: "GET",
                    data: {
                        tax_code: tax_code,
                    },
                    success: function(data) {
                        data = JSON.parse(data);
                        if (data.isValid == true) {
                            // Populate the "Gender" and "D.O.B" fields
                            $('#gender').val(data.gender);
                            $('#dob').val(data.dob);
                            $('#surname').val(data.surname);
                            $('#name').val(data.name);
                            $('#doc_num').val(data.doc_num);
                            $('#sender_email').val(data.email);
                            $('#sender_phone').val(data.phone);
                            $('#sender_address').val(data.address);
                            $('#doc_type').val(data.doc_type);
                            $('#doc_front_img').attr('src', data.doc_front_img);
                            $('#doc_back_img').attr('src', data.doc_back_img);

                            $('#tax_code_status_text').addClass('text-success');
                            $('#tax_code_status_text').removeClass('text-danger');
                            $('#tax_code_status_text').text('Customer No / Tax Code Is Valid');

                            if (data.kyc_status == 'pending') {
                                $('#kyc_status_text').text('KYC is pending');
                            } else if (data.kyc_status == 'approved') {
                                $('#kyc_status_text').text('KYC Is Approved');
                            } else if (data.kyc_status == 'rejected') {
                                $('#kyc_status_text').text('KYC Status Is Denied');
                            } else {
                                $('#kyc_status_text').text('KYC Status Is Not Yet Set');
                            }
                        } else {
                            $('#tax_code_status_text').text(
                                'Customer No / Tax Code Is Invalid, Please fill data accordingly');
                            $('#kyc_status_text').addClass('text-danger');
                            $('#kyc_status_text').removeClass('text-success');

                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", status, error);
                        alert("An error occurred while processing your request. Please try again later.");
                    }
                });
            }
        }


        // Function to set the source of the doc_front_img when a file is selected
        function setDocFrontSrc() {
            var fileInput = document.getElementById('doc_front');
            var img = document.getElementById('doc_front_img');

            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    img.src = e.target.result;
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        }

        // Function to set the source of the doc_back_img when a file is selected
        function setDocBackSrc() {
            var fileInput = document.getElementById('doc_back');
            var img = document.getElementById('doc_back_img');

            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    img.src = e.target.result;
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        }

        // Attach the change event handlers to the file input elements
        $('#doc_front').change(setDocFrontSrc);
        $('#doc_back').change(setDocBackSrc);

        function getRates() {

            let ship_from_country = $('#ship_from_country').val();
            let ship_from_city = $('#ship_from_city').val();
            let ship_to_country = $('#customer_country_id').val();
            let ship_to_city = $('#customer_city_id').val();
            $('#ship_to_country1').val(ship_to_country);
            $('#ship_to_city1').val(ship_to_city);
            let weightTotal = $('#weight_tot').val();
            let widthTotal = $('#width_tot').val();
            let heightTotal = $('#len_tot').val();
            let lengthTotal = $('#height_tot').val();
            let valueTotal = $('#itemvalue_tot').val();
            let countTotal = $('#count_tot').val();



            $.ajax({
                url: "{{ route('rates.fetch') }}",
                type: "GET",
                data: {
                    ship_from_country: ship_from_country,
                    ship_from_city: ship_from_city,
                    ship_to_country: ship_to_country,
                    ship_to_city: ship_to_city,
                    origin: origin,
                    weightTotal: weightTotal,
                    heightTotal: heightTotal,
                    widthTotal: widthTotal,
                    lengthTotal: lengthTotal,
                    valueTotal: valueTotal,
                    countTotal: countTotal,
                    location_rates_only: 1
                },
                beforeSend: function () {
                    $('#ajaxLoader').show();
                    $('#fetchRatesBtn').prop('disabled', true);
                },
                success: function(data) {
                    if (data.success) {
                        //console.log(data);
                        let result = data.data;

                        siopay = data.siopay;
                        let fedex = data.fedex;
                        let mar = '';

                        // $('#shipping_rate_list').html('');

                        result.forEach(result => {
                            mar += `
                                    <tr>
                                        <td>
                                            <input type="radio" name="rdShippingcarrier" class="shipping-option" value="${result.carrier}" data-cost="${ result.total }">
                                        </td>
                                        <td>
                                           ${result.ship_from}
                                        </td>
                                        <td>
                                            ${result.ship_to}
                                        </td>
                                        <td>
                                            ${result.total_weight}
                                        </td>
                                        <td>
                                            ${ result.shipping_cost }
                                        </td>
                                        <td>
                                            ${ result.total }
                                        </td>
                                        <td>
                                            ${ result.carrier }
                                        </td>
                                    </tr>
                                `;
                        });

                        // mar += `
                        //             <tr>
                        //                 <td>
                        //                    ${result.ship_from}
                        //                 </td>
                        //                 <td>
                        //                     ${result.ship_to}
                        //                 </td>
                        //                 <td>
                        //                     ${result.total_weight}
                        //                 </td>
                        //                 <td>
                        //                     ${ result.shipping_cost }
                        //                 </td>
                        //                 <td>
                        //                     ${ result.total }
                        //                 </td>
                        //             </tr>
                        //         `;
                        $('#shipping_rate_list').html(mar);
                        $('.proccess_btn').attr('disabled', false);
                        //show second stage of form
                    } else {
                        toastr.error(data.message);
                    }


                },
                complete: function () {
                    $('#ajaxLoader').hide();
                    $('#fetchRatesBtn').prop('disabled', false);
                },
                error: function(data) {
                    console.log(data);
                    toastr.error('Something went wrong');

                }
            });

            document.addEventListener('change', function (e) {
                if (e.target.classList.contains('shipping-option')) {
                    document.getElementById('shipping_cost').value = e.target.dataset.cost;
                }
            });



        }
    </script>
@endsection
