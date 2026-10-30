@extends('admin.app')
@section('page_title', 'New Shipping Order')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">New Order</h5>
                @include('admin.partials.notification')
                <hr>
                <span>All fields marked <i class="text-danger">*</i> are required</span>
                <hr>
                <form action="{{ route('orders.store') }}" method="post" id="order-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="ui-widget">
                                <label for="origin">Shipping from <i class="text-danger">*</i> : </label>
                                <input type="text" id="origin" name="origin_" value="{{ old('origin_') }}"
                                    class="form-control" autocomplete="off">
                                <input type="hidden" name="origin_id" value="{{ old('origin_id') }}" id="origin_id">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="ui-widget">
                                <label for="dest">Shipping To <i class="text-danger">*</i> : </label>
                                <input type="text" id="dest" name="dest_" value="{{ old('dest_') }}"
                                    class="form-control" autocomplete="off">
                                <input type="hidden" name="dest_id" value="{{ old('dest_id') }}" id="dest_id">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <th>Package Type <i class="text-danger">*</i></th>
                                <th>length(cm) <i class="text-danger">*</i></th>
                                <th>Width(cm) <i class="text-danger">*</i></th>
                                <th>Height(cm) <i class="text-danger">*</i></th>
                                <th>Weight(Kg) <i class="text-danger">*</i></th>
                                <th>Count/qty <i class="text-danger">*</i></th>
                                <th><button class="btn btn-primary btn-sm" type="button" onclick="addRow()"><i
                                            class="fa fa-plus"></i> Add</button></th>
                            </thead>
                            <tbody id="items_list">
                                <tr>
                                    <td>
                                        <select name="type[]" class="form-control type"
                                            onchange="calculateTotItems('type')" required>
                                            <option value="">--select package type--</option>
                                            <option value="percel">Percel</option>
                                            <option value="doc">Document</option>
                                            <option value="pallet">Pallet</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" value="0"
                                            class="form-control len" onkeyup="calculateTot('len')" name="len[]" required>
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" value="0"
                                            class="form-control width" onkeyup="calculateTot('width')" name="width[]"
                                            required>
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" value="0"
                                            class="form-control height" onkeyup="calculateTot('height')" name="height[]"
                                            required>
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" value="0"
                                            class="form-control weight" onkeyup="calculateTot('weight')" name="weight[]"
                                            required>
                                    </td>
                                    <td>
                                        <input type="number" step="1" min="1" value="1"
                                            class="form-control count" onkeyup="calculateTot('count')" name="count[]"
                                            required>
                                    </td>
                                    <td>
                                        {{-- <button class="btn btn-danger" type="button" onclick="removeRow(this)"><i
                                                class="fa fa-trash"></i></button> --}}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <th>
                                    Packages: <span id="type-tot"></span>
                                </th>
                                <th>
                                    Total length: <span id="len-tot"></span>
                                    <input type="hidden" name="len_tot" id="len_tot">
                                </th>
                                <th>
                                    Total Width: <span id="width-tot"></span>
                                    <input type="hidden" name="width_tot" id="width_tot">
                                </th>
                                <th>
                                    Total Height: <span id="height-tot"></span>
                                    <input type="hidden" name="height_tot" id="height_tot">
                                </th>
                                <th>
                                    Total Weight: <span id="weight-tot"></span>
                                    <input type="hidden" name="weight_tot" id="weight_tot">
                                </th>
                                <th>
                                    Total Quantity: <span id="count-tot"></span>
                                    <input type="hidden" name="count_tot" id="count_tot">
                                </th>
                            </tfoot>
                        </table>
                    </div>
                    <button type="button" onclick="getRates()" class="btn btn-primary">Get Available Rates</button>
                    <hr>
                    <h5>Shipping Rates</h5>
                    <div class="table-responsive">
                        <table id="locations_tbl" class="table table-sm  table-bordered table-striped display">
                            <thead>
                                <tr>
                                    <th>Select <i class="text-danger">*</i></th>
                                    <th>Name</th>
                                    <th>Locations</th>
                                    <th>Weights(Kg)</th>
                                    <th>Price(&euro;)</th>
                                    <th>Dimensions(cm)</th>
                                </tr>
                            </thead>
                            <tbody id="shipping_rate_list">

                            </tbody>

                        </table>
                    </div>
                    <div class="stage_2" style="display: none;">
                        <hr>
                        <h5>Addresses</h5>
                        <hr>
                        <div class="add_set">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Sender Address</h6>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="s_name">Sender Name <i class="text-danger">*</i></label>
                                            <input type="text" name="s_name" class="form-control"
                                                value="{{ auth()->user()->name }}" required>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="s_email">Sender Email <i class="text-danger">*</i></label>
                                            <input type="email" name="s_email" class="form-control"
                                                value="{{ auth()->user()->email }}" required>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="s_phone">Sender Phone <i class="text-danger">*</i></label>
                                            <input type="text" value="{{ old('s_phone') }}" name="s_phone"
                                                class="form-control" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="s_phone_alt">Sender Phone Alt. (optional)</label>
                                            <input type="text" value="{{ old('s_phone_alt') }}" name="s_phone_alt"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="s_aadress1">Address1 <i class="text-danger">*</i></label>
                                            <textarea name="s_address1" class="form-control" required>{{ old('s_address1') }}</textarea>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="s_aadress2">Address 2(additional data-optional)</label>
                                            <textarea name="s_address2" class="form-control">{{ old('s_address2') }}</textarea>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="s_zip">Sender zip <i class="text-danger">*</i></label>
                                            <input type="text" name="s_zip" value="{{ old('s_zip') }}"
                                                class="form-control" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="s_city">Sender city <i class="text-danger">*</i></label>
                                            <input type="text" name="s_city" value="{{ old('s_city') }}"
                                                class="form-control" required>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="s_state">Sender State/Province <i
                                                    class="text-danger">*</i></label>
                                            <input type="text" name="s_state" value="{{ old('s_state') }}"
                                                class="form-control" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="s_country">Sender Country/ Region <i
                                                    class="text-danger">*</i></label>
                                            <input type="text" name="s_country" value="{{ old('s_country') }}"
                                                class="form-control" id="s_country" autocomplete="off" required>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="r_date">Return Date <i class="text-danger">*</i></label>
                                            <input type="date" name="r_date" value="{{ old('r_date') }}"
                                                class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Receiver Address</h6>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="rx_name">Receiver Name <i class="text-danger">*</i></label>
                                            <input type="text" name="rx_name" value="{{ old('rx_name') }}"
                                                class="form-control" required>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="rx_email">Receiver Email <i class="text-danger">*</i></label>
                                            <input type="email" name="rx_email" value="{{ old('rx_email') }}"
                                                class="form-control" required>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="rx_phone">Receiver Phone <i class="text-danger">*</i></label>
                                            <input type="text" name="rx_phone" value="{{ old('rx_phone') }}"
                                                class="form-control" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="rx_phone_alt">Receiver Phone Alt. (optional)</label>
                                            <input type="text" name="rx_phone_alt" value="{{ old('rx_phone_alt') }}"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="rx_aadress1">Address1 <i class="text-danger">*</i></label>
                                            <textarea name="rx_address1" class="form-control" required>{{ old('rx_address1') }}</textarea>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="rx_aadress2">Address 2(additional data-optional) <i
                                                    class="text-danger">*</i></label>
                                            <textarea name="rx_address2" value="{{ old('rx_address2') }}" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="rx_zip">Receiver zip <i class="text-danger">*</i></label>
                                            <input type="text" name="rx_zip" value="{{ old('rx_zip') }}"
                                                class="form-control" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="rx_city">Receiver city <i class="text-danger">*</i></label>
                                            <input type="text" name="rx_city" value="{{ old('rx_city') }}"
                                                class="form-control" required>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="rx_state">Receiver State/Province <i
                                                    class="text-danger">*</i></label>
                                            <input type="text" name="rx_state" value="{{ old('rx_state') }}"
                                                class="form-control" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="rx_country">Receiver Country/ Region <i
                                                    class="text-danger">*</i></label>
                                            <input type="text" name="rx_country" value="{{ old('rx_country') }}"
                                                class="form-control" id="rx_country" autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="form-group">
                                <label for="cond_of_goods">Condition Of goods</label>
                                <input type="text" name="cond_of_goods" value="{{ old('cond_of_goods') }}"
                                    class="form-control" id="cond_of_goods">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="val_of_goods">Value Of goods</label>
                                <input type="number" name="val_of_goods" value="{{ old('val_of_goods') }}"
                                    class="form-control" id="val_of_goods">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="val_cur">Curency Of Value</label>
                                <input type="text" name="val_cur" value="{{ old('val_cur') }}" class="form-control"
                                    id="val_cur">
                            </div>
                        </div>
                        <hr>
                        <p>By clicking Proceed bellow, you attest that you have read and understood our terms.</p>
                        <button type="button" class="btn btn-primary" onclick="submit_form()"><i
                                class="fa fa-save"></i>
                            Proceed</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function submit_form() {
            if (!$('#order-form')[0].checkValidity()) {
                alert('please fill all required fields, all fileds marked * are required')
                $('#order-form')[0].reportValidity()
            } else {
                if (confirm('Are you sure you sure you wish to proceed?')) {
                    $('#order-form').submit();
                }
            }
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

            let countries = [
                "Afghanistan",
                "Albania",
                "Algeria",
                "Andorra",
                "Angola",
                "Antigua and Barbuda",
                "Argentina",
                "Armenia",
                "Australia",
                "Austria",
                "Azerbaijan",
                "Bahamas",
                "Bahrain",
                "Bangladesh",
                "Barbados",
                "Belarus",
                "Belgium",
                "Belize",
                "Benin",
                "Bhutan",
                "Bolivia",
                "Bosnia and Herzegovina",
                "Botswana",
                "Brazil",
                "Brunei",
                "Bulgaria",
                "Burkina Faso",
                "Burundi",
                "Cabo Verde",
                "Cambodia",
                "Cameroon",
                "Canada",
                "Central African Republic",
                "Chad",
                "Chile",
                "China",
                "Colombia",
                "Comoros",
                "Congo (Congo-Brazzaville)",
                "Costa Rica",
                "Croatia",
                "Cuba",
                "Cyprus",
                "Czechia (Czech Republic)",
                "Democratic Republic of the Congo (Congo-Kinshasa)",
                "Denmark",
                "Djibouti",
                "Dominica",
                "Dominican Republic",
                "Ecuador",
                "Egypt",
                "El Salvador",
                "Equatorial Guinea",
                "Eritrea",
                "Estonia",
                "Eswatini (fmr. Swaziland)",
                "Ethiopia",
                "Fiji",
                "Finland",
                "France",
                "Gabon",
                "Gambia",
                "Georgia",
                "Germany",
                "Ghana",
                "Greece",
                "Grenada",
                "Guatemala",
                "Guinea",
                "Guinea-Bissau",
                "Guyana",
                "Haiti",
                "Holy See",
                "Honduras",
                "Hungary",
                "Iceland",
                "India",
                "Indonesia",
                "Iran",
                "Iraq",
                "Ireland",
                "Israel",
                "Italy",
                "Ivory Coast",
                "Jamaica",
                "Japan",
                "Jordan",
                "Kazakhstan",
                "Kenya",
                "Kiribati",
                "Kuwait",
                "Kyrgyzstan",
                "Laos",
                "Latvia",
                "Lebanon",
                "Lesotho",
                "Liberia",
                "Libya",
                "Liechtenstein",
                "Lithuania",
                "Luxembourg",
                "Madagascar",
                "Malawi",
                "Malaysia",
                "Maldives",
                "Mali",
                "Malta",
                "Marshall Islands",
                "Mauritania",
                "Mauritius",
                "Mexico",
                "Micronesia",
                "Moldova",
                "Monaco",
                "Mongolia",
                "Montenegro",
                "Morocco",
                "Mozambique",
                "Myanmar (formerly Burma)",
                "Namibia",
                "Nauru",
                "Nepal",
                "Netherlands",
                "New Zealand",
                "Nicaragua",
                "Niger",
                "Nigeria",
                "North Korea",
                "North Macedonia (formerly Macedonia)",
                "Norway",
                "Oman",
                "Pakistan",
                "Palau",
                "Palestine State",
                "Panama",
                "Papua New Guinea",
                "Paraguay",
                "Peru",
                "Philippines",
                "Poland",
                "Portugal",
                "Qatar",
                "Romania",
                "Russia",
                "Rwanda",
                "Saint Kitts and Nevis",
                "Saint Lucia",
                "Saint Vincent and the Grenadines",
                "Samoa",
                "San Marino",
                "Sao Tome and Principe",
                "Saudi Arabia",
                "Senegal",
                "Serbia",
                "Seychelles",
                "Sierra Leone",
                "Singapore",
                "Slovakia",
                "Slovenia",
                "Solomon Islands",
                "Somalia",
                "South Africa",
                "South Korea",
                "South Sudan",
                "Spain",
                "Sri Lanka",
                "Sudan",
                "Suriname",
                "Sweden",
                "Switzerland",
                "Syria",
                "Tajikistan",
                "Tanzania",
                "Thailand",
                "Timor-Leste",
                "Togo",
                "Tonga",
                "Trinidad and Tobago",
                "Tunisia",
                "Turkey",
                "Turkmenistan",
                "Tuvalu",
                "Uganda",
                "Ukraine",
                "United Arab Emirates",
                "United Kingdom",
                "United States of America",
                "Uruguay",
                "Uzbekistan",
                "Vanuatu",
                "Venezuela",
                "Vietnam",
                "Yemen",
                "Zambia",
                "Zimbabwe",
            ];

            // Initialize autocomplete
            $("#s_country").autocomplete({
                source: countries,
                minLength: 1 // Minimum number of characters before triggering autocomplete
            });

            // Initialize autocomplete
            $("#rx_country").autocomplete({
                source: countries,
                minLength: 1 // Minimum number of characters before triggering autocomplete
            });

            var currencies = [
                'AED - United Arab Emirates Dirham',
                'AFN - Afghan Afghani',
                'ALL - Albanian Lek',
                'AMD - Armenian Dram',
                'ANG - Netherlands Antillean Guilder',
                'AOA - Angolan Kwanza',
                'ARS - Argentine Peso',
                'AUD - Australian Dollar',
                'AWG - Aruban Florin',
                'AZN - Azerbaijani Manat',
                'BAM - Bosnia-Herzegovina Convertible Mark',
                'BBD - Barbadian Dollar',
                'BDT - Bangladeshi Taka',
                'BGN - Bulgarian Lev',
                'BHD - Bahraini Dinar',
                'BIF - Burundian Franc',
                'BMD - Bermudian Dollar',
                'BND - Brunei Dollar',
                'BOB - Bolivian Boliviano',
                'BRL - Brazilian Real',
                'BSD - Bahamian Dollar',
                'BTN - Bhutanese Ngultrum',
                'BWP - Botswanan Pula',
                'BYN - Belarusian Ruble',
                'BZD - Belize Dollar',
                'CAD - Canadian Dollar',
                'CDF - Congolese Franc',
                'CHF - Swiss Franc',
                'CLP - Chilean Peso',
                'CNY - Chinese Yuan',
                'COP - Colombian Peso',
                'CRC - Costa Rican Colón',
                'CUP - Cuban Peso',
                'CVE - Cape Verdean Escudo',
                'CZK - Czech Republic Koruna',
                'DJF - Djiboutian Franc',
                'DKK - Danish Krone',
                'DOP - Dominican Peso',
                'DZD - Algerian Dinar',
                'EGP - Egyptian Pound',
                'ERN - Eritrean Nakfa',
                'ETB - Ethiopian Birr',
                'EUR - Euro',
                'FJD - Fijian Dollar',
                'FKP - Falkland Islands Pound',
                'FKP - Falkland Islands Pound',
                'FJD - Fijian Dollar',
                'FKP - Falkland Islands Pound',
                'FJD - Fijian Dollar',
                'FKP - Falkland Islands Pound',
                'FJD - Fijian Dollar',
                'FKP - Falkland Islands Pound',
                'FJD - Fijian Dollar',
                'FKP - Falkland Islands Pound',
                'GEL - Georgian Lari',
                'GGP - Guernsey Pound',
                'GHS - Ghanaian Cedi',
                'GIP - Gibraltar Pound',
                'GMD - Gambian Dalasi',
                'GNF - Guinean Franc',
                'GTQ - Guatemalan Quetzal',
                'GYD - Guyanaese Dollar',
                'HKD - Hong Kong Dollar',
                'HNL - Honduran Lempira',
                'HRK - Croatian Kuna',
                'HTG - Haitian Gourde',
                'HUF - Hungarian Forint',
                'IDR - Indonesian Rupiah',
                'ILS - Israeli New Shekel',
                'IMP - Isle of Man Pound',
                'INR - Indian Rupee',
                'IQD - Iraqi Dinar',
                'IRR - Iranian Rial',
                'ISK - Icelandic Króna',
                'JEP - Jersey Pound',
                'JMD - Jamaican Dollar',
                'JOD - Jordanian Dinar',
                'JPY - Japanese Yen',
                'KES - Kenyan Shilling',
                'KGS - Kyrgystani Som',
                'KHR - Cambodian Riel',
                'KID - Kiribati Dollar',
                'KRW - South Korean Won',
                'KWD - Kuwaiti Dinar',
                'KYD - Cayman Islands Dollar',
                'KZT - Kazakhstani Tenge',
                'LAK - Laotian Kip',
                'LBP - Lebanese Pound',
                'LKR - Sri Lankan Rupee',
                'LRD - Liberian Dollar',
                'LSL - Lesotho Loti',
                'LYD - Libyan Dinar',
                'MAD - Moroccan Dirham',
                'MDL - Moldovan Leu',
                'MGA - Malagasy Ariary',
                'MKD - Macedonian Denar',
                'MMK - Myanma Kyat',
                'MNT - Mongolian Tugrik',
                'MOP - Macanese Pataca',
                'MRU - Mauritanian Ouguiya',
                'MUR - Mauritian Rupee',
                'MVR - Maldivian Rufiyaa',
                'MWK - Malawian Kwacha',
                'MXN - Mexican Peso',
                'MYR - Malaysian Ringgit',
                'MZN - Mozambican Metical',
                'NAD - Namibian Dollar',
                'NGN - Nigerian Naira',
                'NIO - Nicaraguan Córdoba',
                'NOK - Norwegian Krone',
                'NPR - Nepalese Rupee',
                'NZD - New Zealand Dollar',
                'OMR - Omani Rial',
                'PAB - Panamanian Balboa',
                'PEN - Peruvian Nuevo Sol',
                'PGK - Papua New Guinean Kina',
                'PHP - Philippine Peso',
                'PKR - Pakistani Rupee',
                'PLN - Polish Złoty',
                'PYG - Paraguayan Guarani',
                'QAR - Qatari Rial',
                'RON - Romanian Leu',
                'RSD - Serbian Dinar',
                'RUB - Russian Ruble',
                'RWF - Rwandan Franc',
                'SAR - Saudi Riyal',
                'SBD - Solomon Islands Dollar',
                'SCR - Seychellois Rupee',
                'SDG - Sudanese Pound',
                'SEK - Swedish Krona',
                'SGD - Singapore Dollar',
                'SHP - Saint Helena Pound',
                'SLL - Sierra Leonean Leone',
                'SOS - Somali Shilling',
                'SRD - Surinamese Dollar',
                'SSP - South Sudanese Pound',
                'STN - São Tomé and Príncipe Dobra',
                'SVC - Salvadoran Colón',
                'SYP - Syrian Pound',
                'SZL - Swazi Lilangeni',
                'THB - Thai Baht',
                'TJS - Tajikistani Somoni',
                'TMT - Turkmenistani Manat',
                'TND - Tunisian Dinar',
                'TOP - Tongan Pa\'anga',
                'TRY - Turkish Lira',
                'TTD - Trinidad and Tobago Dollar',
                'TWD - New Taiwan Dollar',
                'TZS - Tanzanian Shilling',
                'UAH - Ukrainian Hryvnia',
                'UGX - Ugandan Shilling',
                'USD - United States Dollar',
                'UYU - Uruguayan Peso',
                'UZS - Uzbekistan Som',
                'VES - Venezuelan Bolívar',
                'VND - Vietnamese Đồng',
                'VUV - Vanuatu Vatu',
                'WST - Samoan Tala',
                'XAF - Central African CFA Franc',
                'XCD - East Caribbean Dollar',
                'XOF - West African CFA Franc',
                'XPF - CFP Franc',
                'YER - Yemeni Rial',
                'ZAR - South African Rand',
                'ZMW - Zambian Kwacha',
                'ZWL - Zimbabwean Dollar',
            ];
            // Initialize autocomplete
            $("#val_cur").autocomplete({
                source: currencies,
                minLength: 1 // Minimum number of characters before triggering autocomplete
            });

        });

        function addRow() {
            let items_list = $('#items_list');
            let mar = `
            <tr>
                <td>
                    <select name="type[]" class="form-control type" onchange="calculateTotItems('type')" required>
                        <option value="">--select package type--</option>
                        <option value="percel">Percel</option>
                        <option value="doc">Document</option>
                        <option value="pallet">Pallet</option>
                    </select>
                </td>
                <td>
                    <input type="number" step="any" min="0" value="0" class="form-control len" onkeyup="calculateTot()" name="len[]">
                </td>
                <td>
                    <input type="number" step="any" min="0" value="0" class="form-control width" onkeyup="calculateTot()" name="width[]">
                </td>
                <td>
                    <input type="number" step="any" min="0" value="0" class="form-control height" onkeyup="calculateTot()" name="height[]">
                </td>
                <td>
                    <input type="number" step="any" min="0" value="0" class="form-control weight" onkeyup="calculateTot()" name="weight[]">
                </td>
                <td>
                    <input type="number" step="1" min="1" value="1" class="form-control count" onkeyup="calculateTot()" name="count[]">
                </td>
                <td>
                    <button class="btn btn-danger" type="button" onclick="removeRow(this)"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
        `;

            items_list.append(mar);
        }

        function removeRow(obj) {
            $(obj).closest('tr').remove();
        }

        function calculateTot() {
            let the_classes = ['len', 'width', 'height', 'weight', 'count']
            for (let i = 0; i < the_classes.length; i++) {
                let total = 0;
                $('.' + the_classes[i]).each(function(o, e) {
                    if ($(this).val() != '') {
                        let c = (!$(this).hasClass('count')) ? $(this).parent().parent().find('.count').val() :
                            1; //find the count/qty on that row except for the count col
                        total = total + (parseFloat($(this).val()) * parseFloat(c));
                    }
                })
                $('#' + the_classes[i] + '-tot').text(total);
                $('#' + the_classes[i] + '_tot').val(total)
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
        }

        function getRates() {
            let origin = $('#origin_id').val();
            let dest = $('#dest_id').val();
            let width = $('#width_tot').val();
            let height = $('#height_tot').val();
            let weight = $('#weight_tot').val();
            let length = $('#len_tot').val();
            let count = $('#count_tot').val();

            if (origin != '' && dest != '' && width != "" && height != "" && weight != "" && length != "" && count != "") {
                $.ajax({
                    url: "{{ route('rates.fetch') }}",
                    type: "GET",
                    data: {
                        origin: origin,
                        dest: dest,
                        width: width,
                        height: height,
                        weight: weight,
                        length: length,
                        count: count
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.length > 0) {
                            console.log(data);
                            let mar = '';
                            for (let j = 0; j < data.length; j++) {
                                mar += `
                                    <tr>
                                        <td>
                                            <input type = 'radio' value = ${data[j].id} name = 'rate' id = "rate_${data[j].id}" required>
                                        </td>
                                        <td>
                                            ${data[j].name}
                                        </td>
                                        <td>
                                            Origin: ${data[j].origin.name}
                                            Destination: ${data[j].destination.name}
                                        </td>
                                        <td>
                                            Min: ${data[j].weight_start}
                                            Max: ${data[j].weight_end}
                                        </td>
                                        <td>
                                            ${data[j].price}
                                        </td>
                                        <td>
                                            Width: ${data[j].width}
                                            Height: ${data[j].height}
                                            Length: ${data[j].length}
                                        </td>
                                    </tr>

                                `;
                            }
                            $('#shipping_rate_list').html(mar);
                            //show second stage of form
                            $('.stage_2').show();
                        } else {
                            console.log('No results');
                            ($('#shipping_rate_list').html('<tr><td colspan="5">No results found</td></tr>'));
                            //hide second stage of form
                            $('.stage_2').hide();
                        }
                    }
                });
            } else {
                alert('Please fill out all fields accordingly in order to get rates ');
            }

        }
    </script>
@endsection
