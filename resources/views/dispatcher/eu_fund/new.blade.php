@extends('admin.app')
@section('page_title', 'New EU Fund Transfer')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">New EU Fund Transfer</h5>
                @include('admin.partials.notification')
                <hr>
                <span>All fields marked <i class="text-danger">*</i> are required</span>
                <hr>
                <h5>Customer Details</h5>
                <hr>
                <form action="{{ route('eu_fund_trasfer_order.store') }}" method="post" id="order-form"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="ui-widget">
                                <label for="tax_code">Customer No / Tax code <i class="text-danger">*</i> : </label>
                                <input type="text" id="tax_code" name="tax_code_" value="{{ old('tax_code_') }}"
                                    class="form-control" autocomplete="off" required>
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
                    <hr>
                    <div class="stage0" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="ui-widget">
                                    <label for="surname">Surname <i class="text-danger">*</i> : </label>
                                    <input type="text" id="surname" name="surname_" value="{{ old('surname_') }}"
                                        class="form-control" autocomplete="off" required>

                                    {{-- <input type="text" id="cus_id" name="cus_id"> --}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ui-widget">
                                    <label for="name">Firstname <i class="text-danger">*</i> : </label>
                                    <input type="text" id="name" name="name_" value="{{ old('name_') }}"
                                        class="form-control" autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="ui-widget">
                                    <label for="gender">Gender <i class="text-danger">*</i> : </label>
                                    <input type="text" id="gender" name="gender_" value="{{ old('gender_') }}"
                                        class="form-control" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ui-widget">
                                    <label for="dob">D.O.B <i class="text-danger">*</i> : </label>
                                    <input type="date" id="dob" name="dob_" value="{{ old('dob_') }}"
                                        class="form-control" autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="ui-widget">
                                    <label for="gender">Document Type <i class="text-danger">*</i> : </label>
                                    <select name="doc_type_" id="doc_type" class="form-control" required>
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
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ui-widget">
                                    <label for="doc_num">Document Number<i class="text-danger">*</i> : </label>
                                    <input type="text" id="doc_num" name="doc_num_" value="{{ old('doc_num_') }}"
                                        class="form-control" autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="ui-widget">
                                    <label for="doc_front">Document Front <i class="text-danger">*</i> : </label>
                                    <input type="file" id="doc_front" name="doc_front_" class="form-control">
                                    <br>
                                    <img src="" alt="doc_front" id="doc_front_img"
                                        style="width: 100%; border:1px solid gray">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ui-widget">
                                    <label for="doc_back">Document back <i class="text-danger">*</i> : </label>
                                    <input type="file" id="doc_back" name="doc_back_" class="form-control">
                                    <br>
                                    <img src="" alt="doc_back" id="doc_back_img"
                                        style="width: 100%; border:1px solid gray">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="ui-widget">
                                    <label for="email">Customer Email : </label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ui-widget">
                                    <label for="phone">Customer Phone <i class="text-danger">*</i> : </label>
                                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="ui-widget">
                                    <label for="address">Customer Address <i class="text-danger">*</i> : </label>
                                    <textarea name="address" id="address" class="form-control">{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h5>Funds Transfer details</h5>
                        <hr>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <input style="width: 100%" type="text" name="s_country_eu"
                                    value="{{ request()->get('s_country_eu') }}" class="form-control" id="s_country_eu"
                                    autocomplete="off" placeholder="Sender Country" required>
                            </div>


                            <div class="form-group col-md-3">
                                <input style="width: 100%" type="text" name="rx_country_eu"
                                    value="{{ request()->get('rx_country_eu') }}" class="form-control" id="rx_country_eu"
                                    placeholder="Receiver Country" autocomplete="off" required>

                            </div>
                            <div class="form-group col-md-3">
                                <input style="width: 100%" type="number" step="any" name="rx_amount_eu"
                                    value="{{ request()->get('rx_amount_eu') }}" class="form-control" id="rx_amount_eu"
                                    placeholder="Amount(&euro;) To Send" autocomplete="off" required>

                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary m-1" type="button"
                                    onclick="getEURates()">Proceed</button>
                                {{-- <a class="btn btn-danger m-1" href="/">Refresh</a> --}}
                            </div>
                        </div>
                        <br>
                        <div>
                            <table class="table">
                                <thead>
                                    <th>Countries</th>
                                    <th>Amount(&euro;)</th>
                                    <th>Commision(&euro;)</th>
                                </thead>
                                <tbody id="the_rates">

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="stage_2" style="display: none;">
                        <hr>
                        <h5>Account Details</h5>
                        <hr>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="rx_bank">Reciver Bank</label>
                                <input type="text" class="form-control" name="rx_bank" id="rx_bank" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="rx_bank_routing_code">Reciver Bank Routing Code</label>
                                <input type="text" class="form-control" name="rx_bank_routing_code"
                                    id="rx_bank_routing_code" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="rx_bank_account_no">Reciver Bank Account Number</label>
                                <input type="text" class="form-control" name="rx_bank_account_no"
                                    id="rx_bank_account_no" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="rx_bank_account_name">Reciver Bank Account Name</label>
                                <input type="text" class="form-control" name="rx_bank_account_name"
                                    id="rx_bank_account_name" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="rx_phone">Reciver Phone Number</label>
                                <input type="text" class="form-control" name="rx_phone"
                                    id="rx_phone" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="rx_email">Reciver Email</label>
                                <input type="email" class="form-control" name="rx_email"
                                    id="rx_email">
                            </div>
                        </div>
                        <hr>
                        <p>By clicking Proceed bellow, you attest that you have read and understood our terms.</p>
                        <button type="button" class="btn btn-primary" onclick="submit_form()"><i
                                class="fa fa-save"></i>
                            Proceed</button>

                        <button class="btn btn-warning" type="button" onclick="PrintElem('order-form')">Print Pre-reciept</button>
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
            const europeanTerritories = [
                // Recognized European Countries
                "Albania",
                "Andorra",
                "Austria",
                "Belarus",
                "Belgium",
                "Bosnia and Herzegovina",
                "Bulgaria",
                "Croatia",
                "Cyprus",
                "Czech Republic",
                "Denmark",
                "Estonia",
                "Faroe Islands",
                "Finland",
                "France",
                "Germany",
                "Gibraltar",
                "Greece",
                "Guernsey",
                "Hungary",
                "Iceland",
                "Ireland",
                "Isle of Man",
                "Italy",
                "Jersey",
                "Kosovo",
                "Latvia",
                "Liechtenstein",
                "Lithuania",
                "Luxembourg",
                "Malta",
                "Moldova",
                "Monaco",
                "Montenegro",
                "Netherlands",
                "North Macedonia",
                "Norway",
                "Poland",
                "Portugal",
                "Romania",
                "Russia",
                "San Marino",
                "Serbia",
                "Slovakia",
                "Slovenia",
                "Spain",
                "Svalbard and Jan Mayen",
                "Sweden",
                "Switzerland",
                "Ukraine",
                "United Kingdom",
                "Vatican City",

                // Dependent Territories and Special Regions
                "Aland Islands",
                "Akrotiri and Dhekelia",
                "Åland Islands",
                "Azores",
                "Balearic Islands",
                "Canary Islands",
                "Ceuta and Melilla",
                "Channel Islands",
                "Crimea",
                "Curaçao",
                "French Guiana",
                "Gagauzia",
                "Gottland",
                "Greenland",
                "Guadeloupe",
                "Madeira",
                "Man, Isle of",
                "Nagorno-Karabakh",
                "Northern Cyprus",
                "Republika Srpska",
                "Sark",
                "Sealand",
                "Transnistria",
            ];

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
            $("#s_country_eu").autocomplete({
                source: europeanTerritories,
                minLength: 1 // Minimum number of characters before triggering autocomplete
            });

            // Initialize autocomplete
            $("#rx_country_eu").autocomplete({
                source: europeanTerritories,
                minLength: 1 // Minimum number of characters before triggering autocomplete
            });

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
            $("#rx_currency").autocomplete({
                source: currencies,
                minLength: 1 // Minimum number of characters before triggering autocomplete
            });
            // Initialize autocomplete
            $("#s_currency").autocomplete({
                source: currencies,
                minLength: 1 // Minimum number of characters before triggering autocomplete
            });

        });

        function getEURates() {
            let s_country_eu = $('#s_country_eu').val();
            let rx_country_eu = $('#rx_country_eu').val();
            let rx_amount_eu = $('#rx_amount_eu').val();

            if (tax_code != '') {
                $.ajax({
                    url: "{{ route('eu_rates.fetch') }}",
                    type: "GET",
                    data: {
                        s_country_eu: s_country_eu,
                        rx_country_eu: rx_country_eu,
                        rx_amount_eu: rx_amount_eu
                    },
                    success: function(data) {
                        console.log(data);
                        eu_rate = JSON.parse(data);
                        if (1) {
                            $('#the_rates').html('');
                            let mar = `
                            <tr>
                                <td>
                                    ${eu_rate.req.s_country_eu}
                                    <br>
                                    To
                                    <br>
                                    ${eu_rate.req.rx_country_eu}
                                    <input type='hidden' name='rate_id' value="${eu_rate.id}">
                                </td>
                                <td>
                                    Amount(&euro;) : ${eu_rate.req.rx_amount_eu}
                                </td>
                                <td>
                                    Commision(&euro;):
                                    ${(eu_rate.calc == 'perc') ? (parseFloat(eu_rate.req.rx_amount_eu) * (parseFloat(eu_rate.commision)/100)) : eu_rate.commision}
                                </td>
                            </tr>`;
                            $('#the_rates').html(mar);
                            console.log(mar);
                            $('.stage_2').show();
                        } else {
                            $('#the_rates').html(
                                '<tr><td colspan="3">No Reates available for selected countries</td></tr>');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle AJAX errors
                        console.error("Error:", status, error);
                        alert("An error occurred while processing your request. Please try again later.");
                    }
                });
            }
        }

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
                        console.log(data);
                        data = JSON.parse(data);
                        if (data.isValid == true) {
                            // Populate the "Gender" and "D.O.B" fields
                            $('#gender').val(data.gender);
                            // $('#dob').val(data.dob.date.split(' ')[0]); // Extract and set the date part
                            $('#dob').val((data.dob.date && data.dob.date.includes(' ')) ? data.dob.date.split(' ')[0] : data.dob);
                            $('#surname').val(data.surname);
                            $('#name').val(data.name);
                            $('#doc_num').val(data.doc_num);
                            $('#doc_type').val(data.doc_type);
                            $('#phone').val(data.phone);
                            $('#email').val(data.email);
                            $('#address').text(data.address);

                            $('.stage0').show();
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
                            // Handle invalid tax code
                            // alert("Invalid tax code");
                            $('#tax_code_status_text').text('Customer No / Tax Code Is Invalid, Please fill data accordingly');
                            $('#kyc_status_text').addClass('text-danger');
                            $('#kyc_status_text').removeClass('text-success');
                            $('.stage0').show();
                        }

                    },
                    error: function(xhr, status, error) {
                        // Handle AJAX errors
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

        function PrintElem(elem) {
            // Clone the original content
            var originalContent = document.getElementById(elem).cloneNode(true);

            // Hide all buttons in the clone
            var buttons = originalContent.querySelectorAll('button');
            for (var i = 0; i < buttons.length; i++) {
                buttons[i].style.display = 'none';
            }

            // Replace input fields with spans
            var inputs = originalContent.querySelectorAll('input');
            for (var i = 0; i < inputs.length; i++) {
                var input = inputs[i];
                var span = document.createElement('span');
                span.textContent = input.value;
                input.parentNode.replaceChild(span, input);
            }

            // Replace selet fields with spans
            var selects = originalContent.querySelectorAll('select');
            for (var i = 0; i < selects.length; i++) {
                var select = selects[i];
                var span = document.createElement('span');
                span.textContent = select.value;
                select.parentNode.replaceChild(span, select);
            }

            // Replace textarea fields with spans
            var textareas = originalContent.querySelectorAll('textarea');
            for (var i = 0; i < textareas.length; i++) {
                var select = textareas[i];
                var span = document.createElement('span');
                span.textContent = select.innerText;
                select.parentNode.replaceChild(span, select);
            }

            // Create a new window and write the modified content
            var mywindow = window.open('', 'PRINT', 'height=400,width=600');
            mywindow.document.write('<html><head><title>' + document.title + '</title>');
            mywindow.document.write('<link rel="stylesheet" href="{{ asset('admin_assets/assets/css/styles.min.css')}}" />');
            mywindow.document.write('</head><body>');
            mywindow.document.write(originalContent.innerHTML);
            mywindow.document.write('</body></html');
            mywindow.document.close(); // IE >= 10
            mywindow.focus(); // IE >= 10
            mywindow.print();

            return true;
        }
    </script>
@endsection
