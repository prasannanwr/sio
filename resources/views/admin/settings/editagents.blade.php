@php
    use App\Models\BankDetail;
    use App\Models\PaymentRequest;
    $bank_details = BankDetail::select('id', 'bank_name', 'iban')->get();
@endphp
@extends('admin.app')
@section('page_title', 'Agent Profile')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="container-fluid">
        @include('admin.partials.notification')
        <div class="row mb-4">
            <div class="col-md-4 mb-4">
                <div class="card mb-3 h-100">
                    <div class="card-header">
                        <h5 class="">Agent Details</h5>
                    </div>
                    <div class="card-body gap-4">
                        <div>
                            <!-- Button trigger modal -->
                            <p><b>Name: {{ $agent->name }}</b></p>
                            <p><b>Email: {{ $agent->email }}</b></p>
                            <p><b>Agency Type: {{ $agent->agent->agency_type }}</b></p>
                        </div>
                        <div>
                            <a type="button" class="" data-bs-toggle="modal"
                                data-bs-target="#registrationDocumentModal">
                                Check Registration Document
                            </a>
                            <br>
                            <a type="button" class="mt-3" data-bs-toggle="modal" data-bs-target="#fullDocumentModal">
                                Check Full Document
                            </a>
                        </div>
                        {{-- </div> --}}
                        <!-- Modal -->
                        <div class="modal fade" id="registrationDocumentModal" tabindex="-1"
                            aria-labelledby="registrationDocumentModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="registrationDocumentModalLabel">Agnet Registration
                                            Document
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <iframe class="pdf" src="{{ asset($agent->agent->front_attachment) }}"
                                            width="100%" height="650"></iframe>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal -->
                        <div class="modal fade" id="fullDocumentModal" tabindex="-1"
                            aria-labelledby="fullDocumentModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="fullDocumentModalLabel">Agent Full Document</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <iframe class="pdf" src="{{ asset($agent->agent->attachment_path) }}"
                                            width="100%" height="650"></iframe>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card mb-3 h-100">
                    <div class="card-header">
                        <h5 class="">Currency Details</h5>
                    </div>
                    <div class="card-body d-flex gap-4">
                        <form class="w-100" action="{{ route('assignCurrency', $agent->id) }}" method="post">
                            @csrf
                            @method('POST')
                            <div class="row">
                                <div class="form-group col-md-12 mt-4">
                                    {{ auth()->user()->currency_id }}
                                    <label for="currency_id">Select Currency<i class="text-danger">*</i></label>
                                    <select class="form-control" id="currency_id" name="currency_id">
                                        <option value="" selected>Select Currency</option>
                                        @foreach ($currency_exchange_rates as $currency_exchange_rate)
                                            <option value="{{ $currency_exchange_rate->id }}"
                                                @if ($currency_exchange_rate->id == $agent->currency_id) selected @endif>
                                                {{ $currency_exchange_rate->country->name }}-
                                                <b>{{ $currency_exchange_rate->country->currency_symbol }}</b>
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('currency_id')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group col-md-12 mt-4">
                                    <label for="currency_id">Add Pick-Up Percentage<i class="text-danger">*</i></label>
                                    <input type="number" class="form-control" min="1" max="100" name="pickup_comission_percentage" id="pickup_commission_percentage">
                                    @error('pickup_comission_percentage')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <div class="text-end">
                                <button class="btn btn-primary btn-sm" type="submit">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card mb-3 h-100">
                    <div class="card-header">
                        <h5>Add Payment Request</h5>
                    </div>
                    <div class="card-body">
                        <form class="" id="paymentRequest" action="{{ route('assignBankDetails', $agent->id) }}" method="post">
                            @csrf
                            @method('POST')
                            <div class="row">
                                <div class="col-12">
                                    <label for="bank_name">Name of Bank<i class="text-danger">*</i>:</label>
                                    <select name="bank_detail_id" id="bank_detail_id" class="form-control">
                                        <option value="">Select Bank</option>
                                        @foreach ($bank_details as $bank_detail)
                                            <option value="{{ $bank_detail->id }}" @if ($bank_detail->id == $agent->bank_detail_id)
                                                selected
                                            @else
                                                
                                            @endif>{{ $bank_detail->bank_name }}-
                                                {{ $bank_detail->iban }}</option>
                                        @endforeach
                                    </select>
                                    @error('bank_detail_id')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <br>    
                            {{-- <div class="row">
                                <div class="col-12">
                                    <label for="file">Receipt Attachment<i class="text-danger">*</i>:</label>
                                    <input type="file" name="reciept_attachement" id="reciept_attachement"
                                        class="form-control">
                                    @error('reciept_attachement')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-12">
                                    <label for="amount">Amount<i class="text-danger">*</i>:</label>
                                    <input type="number" name="amount" id="amount" class="form-control">
                                    @error('amount')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div> --}}
                            {{-- <br> --}}
                            <div class="text-end">
                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title fw-semibold">Agent Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                    
                    <div class="d-flex">
                        <form action="{{route('blockUser')}}" method="post">
                            @csrf
                            @method("POST")
                            <input type="hidden" value="{{$agent->id}}" name="user_id">
                            <button type="submit" class="btn btn-sm btn-danger" > Reject</button>
                        </form>
                        <form class="ms-2" action="{{route('unblockUser')}}" method="post">
                            @csrf
                            @method("POST")
                            <input type="hidden" value="{{$agent->id}}" name="user_id">
                            <button type="submit" class="btn btn-sm btn-primary" >Approve</button>
                        </form>
                    </div>
                </div>
                    <hr>
                    <form action="{{ route('agents.update', $agent->agent) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group  col-md-6">
                                <label for="name">Name/ Business Name</label>
                                <input type="text" name="name" class="form-control" id="name"
                                    value="{{ $agent->name }}" required>
                            </div>
                            <div class="form-group  col-md-6">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control" id="email"
                                    value="{{ $agent->name }}" required readonly>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" class="form-control" id="phone"
                                    value="{{ $agent->agent->phone }}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="phone_alt">Phone Alt</label>
                                <input type="text" name="phone_alt" class="form-control" id="phone_alt"
                                    value="{{ $agent->agent->phone_alt }}">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="agency-type" class="col-md-4 col-form-label">{{ __('Agency Type') }} <i
                                        class="text-danger">*</i></label>
                                <select name="agency_type" id="agency_type" class="form-control">
                                    <option value="person" {{ $agent->agent->agency_type == 'person' ? 'selected' : '' }}>
                                        Personal</option>
                                    <option value="agency" {{ $agent->agent->agency_type == 'agency' ? 'selected' : '' }}>
                                        Agency</option>
                                    <option value="public_admin"
                                        {{ $agent->agent->agency_type == 'public_admin' ? 'selected' : '' }}>Public
                                        Adminiatration</option>
                                </select>
                                @error('agency_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


                            <div class="col-md-6">
                                <label for="business-name" class="col-md-4 col-form-label">Business Name</label>
                                <input id="business-name" type="text" class="form-control"
                                    value="{{ $agent->agent->business_name }}" name="business_name">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="tax-id-code" class="col-md-4 col-form-label">Tax ID Code <i
                                        class="text-danger">*</i></label>
                                <input id="tax-id-code" type="text" class="form-control" name="tax_id_code"
                                    value="{{ $agent->agent->tax_id_code }}" required>
                                @error('tax_id_code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="vat-no" class="col-md-4 col-form-label">VAT Number</label>
                                <input id="vat-no" type="text" class="form-control"
                                    value="{{ $agent->agent->vat_no }}" name="vat_no">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="pec" class="col-md-4 col-form-label">PEC</label>
                                <input id="pec" type="text" class="form-control"
                                    value="{{ $agent->agent->pec }}" name="pec">
                            </div>
                            <div class="col-md-6">
                                <label for="sdi" class="col-md-4 col-form-label">Unique SDI Code</label>
                                <input id="sdi" type="text" class="form-control"
                                    value="{{ $agent->agent->sdi }}" name="sdi">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <label for="address1">Address 1</label>
                                <textarea name="address1" class="form-control" id="address1">{{ $agent->agent->address2 }}</textarea>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <label for="address2">Address 2(additional decriptions)</label>
                                <textarea name="address2" class="form-control" id="address2">{{ $agent->agent->address1 }}</textarea>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="zip">zip</label>
                                <input type="text" name="zip" class="form-control" id="zip"
                                    value="{{ $agent->agent->zip }}" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="residential_country">Country<i class="text-danger">*</i></label>
                                <select class="form-control" id="residential_country" name="residential_country"
                                    required>
                                    <option value="" selected>Select Country</option>
                                </select>
                                <small class="text-muted">Select Country</small>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="residential_state">State<i class="text-danger">*</i></label>
                                <select class="select2 form-control" id="residential_state" name="residential_state"
                                    required>
                                </select>
                                <small class="text-muted">Select State</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="residential_city">City<i class="text-danger">*</i></label>
                                <select class="form-control" id="residential_city" name="residential_city">
                                </select>
                                <small class="text-muted">Select City</small>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                        <div class="col-md-6">
                            <label for="attachment">{{ __('Registration Document') }} <i
                                    class="text-danger">*</i></label>
                            <input type="file" class="form-control" name="front_attachment" accept="application/pdf">
                            @error('attachment')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="attachment">{{ __('Full Document') }} <i class="text-danger">*</i></label>
                            <input type="file" class="form-control" name="attachment" accept="application/pdf">
                            @error('attachment')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                        <br>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </form>
                    
                </div>
            </div>
        </div>
    @section('scripts')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#residential_country').select2();
                $('#residential_city').select2();
                $('#residential_state').select2();
                countries();

                var residential_country = $("#residential_country");
                residential_country.wrap('<div class="position-relative"></div>');
                residential_country.on('change', function() {
                    $("#residential_city").empty();
                    $('#residential_state').empty();
                    $('#residential_state').html('<option value="">Select State</option>');
                    $('#residential_city').html('<option value="">Select City</option>');
                    var _token = '{{ csrf_token() }}';
                    let url = "{{ route('ajax-get-states', ['countryId' => ':countryId']) }}".replace(
                        ':countryId', $(this).val());
                    if ($(this).val() > 0) {
                        // showBlockUI();
                        $.ajax({
                            url: url,
                            type: 'post',
                            dataType: 'json',
                            data: {
                                'stateId': $(this).val(),
                                '_token': _token
                            },
                            success: function(response) {
                                if (response.success) {
                                    $.each(response.states, function(key, value) {
                                        $("#residential_state").append(
                                            '<option value="' + value.id + '">' + value
                                            .name + '</option>'
                                        );
                                    });
                                    // hideBlockUI();

                                    $('#residential_state').trigger('change');

                                    @if (!is_null($agent->agent->state_id))
                                        $('#residential_state').val({{ $agent->agent->state_id }});
                                        $('#residential_state').trigger('change')
                                    @endif
                                } else {
                                    // hideBlockUI();
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                    });
                                }
                            },
                            error: function(error) {
                                console.log(error);
                                // hideBlockUI();
                            }
                        });
                    }
                });


                var residential_country = $("#residential_country");
                residential_country.wrap('<div class="position-relative"></div>');
                residential_country.on('change', function() {
                    $("#residential_city").empty()
                    $('#residential_city').html('<option value="">Select City</option>');
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
                                        $("#residential_city").append('<option value="' +
                                            value
                                            .id + '">' + value.name + '</option>');
                                    });
                                    $('#residential_city').trigger('change');
                                    // hideBlockUI();
                                    @if (!is_null($agent->agent->city_id))
                                        $('#residential_city').val({{ $agent->agent->city_id }});
                                        $('#residential_city').trigger('change')
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


            function countries() {

                $('#residential_country').html('<option value="">Select Country</option>');
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
                                $("#residential_country").append('<option value="' + value.id +
                                    '">' + value.name + '</option>');
                            });
                            $('#residential_country').trigger('change');
                            @if (!is_null($agent->agent->country_id))
                                $('#residential_country').val({{ $agent->agent->country_id }});
                                $('#residential_country').trigger('change');
                            @endif
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
        </script>
    @endsection
@endsection
