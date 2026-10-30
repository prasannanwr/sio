@extends('admin.app')
@section('page_title', 'Edit Location')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Settings</h5>
                @include('admin.settings.nav')
                <hr>
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route('locations.index') }}" class="btn btn-danger float-right"><i
                                class="fa fa-times"></i>Exit</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        @include('admin.partials.notification')
                        <h5>Edit Location</h5>
                        <form action="{{ route('locations.update', $location->id) }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name", id="name"
                                        value="{{ $location->name ?? '' }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="country_code">Country</label>
                                    <select name="country_code" id="country_code" class="form-control" required>
                                        <option value="">Select a country...</option>
                                        <option value="AF" {{ $location->country_code == 'AF' ? 'selected' : '' }}>
                                            Afghanistan</option>
                                        <option value="AX" {{ $location->country_code == 'AX' ? 'selected' : '' }}>
                                            Åland Islands</option>
                                        <option value="AL" {{ $location->country_code == 'AL' ? 'selected' : '' }}>
                                            Albania</option>
                                        <option value="DZ" {{ $location->country_code == 'DZ' ? 'selected' : '' }}>
                                            Algeria</option>
                                        <option value="AS" {{ $location->country_code == 'AS' ? 'selected' : '' }}>
                                            American Samoa</option>
                                        <option value="AD" {{ $location->country_code == 'AD' ? 'selected' : '' }}>
                                            Andorra</option>
                                        <option value="AO" {{ $location->country_code == 'AO' ? 'selected' : '' }}>
                                            Angola</option>
                                        <option value="AI" {{ $location->country_code == 'AI' ? 'selected' : '' }}>
                                            Anguilla</option>
                                        <option value="AQ" {{ $location->country_code == 'AQ' ? 'selected' : '' }}>
                                            Antarctica</option>
                                        <option value="AG" {{ $location->country_code == 'AG' ? 'selected' : '' }}>
                                            Antigua and Barbuda</option>
                                        <option value="AR" {{ $location->country_code == 'AR' ? 'selected' : '' }}>
                                            Argentina</option>
                                        <option value="AM" {{ $location->country_code == 'AM' ? 'selected' : '' }}>
                                            Armenia</option>
                                        <option value="AW" {{ $location->country_code == 'AW' ? 'selected' : '' }}>
                                            Aruba</option>
                                        <option value="AU" {{ $location->country_code == 'AU' ? 'selected' : '' }}>
                                            Australia</option>
                                        <option value="AT" {{ $location->country_code == 'AT' ? 'selected' : '' }}>
                                            Austria</option>
                                        <option value="AZ" {{ $location->country_code == 'AZ' ? 'selected' : '' }}>
                                            Azerbaijan</option>
                                        <option value="BS" {{ $location->country_code == 'BS' ? 'selected' : '' }}>
                                            Bahamas</option>
                                        <option value="BH" {{ $location->country_code == 'BH' ? 'selected' : '' }}>
                                            Bahrain</option>
                                        <option value="BD" {{ $location->country_code == 'BD' ? 'selected' : '' }}>
                                            Bangladesh</option>
                                        <option value="BB" {{ $location->country_code == 'BB' ? 'selected' : '' }}>
                                            Barbados</option>
                                        <option value="BY" {{ $location->country_code == 'BY' ? 'selected' : '' }}>
                                            Belarus</option>
                                        <option value="BE" {{ $location->country_code == 'BE' ? 'selected' : '' }}>
                                            Belgium</option>
                                        <option value="BZ" {{ $location->country_code == 'BZ' ? 'selected' : '' }}>
                                            Belize</option>
                                        <option value="BJ" {{ $location->country_code == 'BJ' ? 'selected' : '' }}>
                                            Benin</option>
                                        <option value="BM" {{ $location->country_code == 'BM' ? 'selected' : '' }}>
                                            Bermuda</option>
                                        <option value="BT" {{ $location->country_code == 'BT' ? 'selected' : '' }}>
                                            Bhutan</option>
                                        <option value="BO" {{ $location->country_code == 'BO' ? 'selected' : '' }}>
                                            Bolivia, Plurinational State of</option>
                                        <option value="BQ" {{ $location->country_code == 'BQ' ? 'selected' : '' }}>
                                            Bonaire, Sint Eustatius and Saba</option>
                                        <option value="BA" {{ $location->country_code == 'BA' ? 'selected' : '' }}>
                                            Bosnia and Herzegovina</option>
                                        <option value="BW" {{ $location->country_code == 'BW' ? 'selected' : '' }}>
                                            Botswana</option>
                                        <option value="BV" {{ $location->country_code == 'BV' ? 'selected' : '' }}>
                                            Bouvet Island</option>
                                        <option value="BR" {{ $location->country_code == 'BR' ? 'selected' : '' }}>
                                            Brazil</option>
                                        <option value="IO" {{ $location->country_code == 'IO' ? 'selected' : '' }}>
                                            British Indian Ocean Territory</option>
                                        <option value="BN" {{ $location->country_code == 'BN' ? 'selected' : '' }}>
                                            Brunei Darussalam</option>
                                        <option value="BG" {{ $location->country_code == 'BG' ? 'selected' : '' }}>
                                            Bulgaria</option>
                                        <option value="BF" {{ $location->country_code == 'BF' ? 'selected' : '' }}>
                                            Burkina Faso</option>
                                        <option value="BI" {{ $location->country_code == 'BI' ? 'selected' : '' }}>
                                            Burundi</option>
                                        <option value="KH" {{ $location->country_code == 'KH' ? 'selected' : '' }}>
                                            Cambodia</option>
                                        <option value="CM" {{ $location->country_code == 'CM' ? 'selected' : '' }}>
                                            Cameroon</option>
                                        <option value="CA" {{ $location->country_code == 'CA' ? 'selected' : '' }}>
                                            Canada</option>
                                        <option value="CV" {{ $location->country_code == 'CV' ? 'selected' : '' }}>
                                            Cape Verde</option>
                                        <option value="KY" {{ $location->country_code == 'KY' ? 'selected' : '' }}>
                                            Cayman Islands</option>
                                        <option value="CF" {{ $location->country_code == 'CF' ? 'selected' : '' }}>
                                            Central African Republic</option>
                                        <option value="TD" {{ $location->country_code == 'TD' ? 'selected' : '' }}>
                                            Chad</option>
                                        <option value="CL" {{ $location->country_code == 'CL' ? 'selected' : '' }}>
                                            Chile</option>
                                        <option value="CN" {{ $location->country_code == 'CN' ? 'selected' : '' }}>
                                            China</option>
                                        <option value="CX" {{ $location->country_code == 'CX' ? 'selected' : '' }}>
                                            Christmas Island</option>
                                        <option value="CC" {{ $location->country_code == 'CC' ? 'selected' : '' }}>
                                            Cocos (Keeling) Islands</option>
                                        <option value="CO" {{ $location->country_code == 'CO' ? 'selected' : '' }}>
                                            Colombia</option>
                                        <option value="KM" {{ $location->country_code == 'KM' ? 'selected' : '' }}>
                                            Comoros</option>
                                        <option value="CG" {{ $location->country_code == 'CG' ? 'selected' : '' }}>
                                            Congo</option>
                                        <option value="CD" {{ $location->country_code == 'CD' ? 'selected' : '' }}>
                                            Congo, the Democratic Republic of the</option>
                                        <option value="CK" {{ $location->country_code == 'CK' ? 'selected' : '' }}>
                                            Cook Islands</option>
                                        <option value="CR" {{ $location->country_code == 'CR' ? 'selected' : '' }}>
                                            Costa Rica</option>
                                        <option value="CI" {{ $location->country_code == 'CI' ? 'selected' : '' }}>
                                            Côte d'Ivoire</option>
                                        <option value="HR" {{ $location->country_code == 'HR' ? 'selected' : '' }}>
                                            Croatia</option>
                                        <option value="CU" {{ $location->country_code == 'CU' ? 'selected' : '' }}>
                                            Cuba</option>
                                        <option value="CW" {{ $location->country_code == 'CW' ? 'selected' : '' }}>
                                            Curaçao</option>
                                        <option value="CY" {{ $location->country_code == 'CY' ? 'selected' : '' }}>
                                            Cyprus</option>
                                        <option value="CZ" {{ $location->country_code == 'CZ' ? 'selected' : '' }}>
                                            Czech Republic</option>
                                        <option value="DK" {{ $location->country_code == 'DK' ? 'selected' : '' }}>
                                            Denmark</option>
                                        <option value="DJ" {{ $location->country_code == 'DJ' ? 'selected' : '' }}>
                                            Djibouti</option>
                                        <option value="DM" {{ $location->country_code == 'DM' ? 'selected' : '' }}>
                                            Dominica</option>
                                        <option value="DO" {{ $location->country_code == 'DO' ? 'selected' : '' }}>
                                            Dominican Republic</option>
                                        <option value="EC" {{ $location->country_code == 'EC' ? 'selected' : '' }}>
                                            Ecuador</option>
                                        <option value="EG" {{ $location->country_code == 'EG' ? 'selected' : '' }}>
                                            Egypt</option>
                                        <option value="SV" {{ $location->country_code == 'SV' ? 'selected' : '' }}>
                                            El Salvador</option>
                                        <option value="GQ" {{ $location->country_code == 'GQ' ? 'selected' : '' }}>
                                            Equatorial Guinea</option>
                                        <option value="ER" {{ $location->country_code == 'ER' ? 'selected' : '' }}>
                                            Eritrea</option>
                                        <option value="EE" {{ $location->country_code == 'EE' ? 'selected' : '' }}>
                                            Estonia</option>
                                        <option value="ET" {{ $location->country_code == 'ET' ? 'selected' : '' }}>
                                            Ethiopia</option>
                                        <option value="FK" {{ $location->country_code == 'FK' ? 'selected' : '' }}>
                                            Falkland Islands (Malvinas)</option>
                                        <option value="FO" {{ $location->country_code == 'FO' ? 'selected' : '' }}>
                                            Faroe Islands</option>
                                        <option value="FJ" {{ $location->country_code == 'FJ' ? 'selected' : '' }}>
                                            Fiji</option>
                                        <option value="FI" {{ $location->country_code == 'FI' ? 'selected' : '' }}>
                                            Finland</option>
                                        <option value="FR" {{ $location->country_code == 'FR' ? 'selected' : '' }}>
                                            France</option>
                                        <option value="GF" {{ $location->country_code == 'GF' ? 'selected' : '' }}>
                                            French Guiana
                                        </option>
                                        <option value="PF" {{ $location->country_code == 'PF' ? 'selected' : '' }}>
                                            French Polynesia
                                        </option>
                                        <option value="TF" {{ $location->country_code == 'TF' ? 'selected' : '' }}>
                                            French Southern
                                            Territories</option>
                                        <option value="GA" {{ $location->country_code == 'GA' ? 'selected' : '' }}>
                                            Gabon</option>
                                        <option value="GM" {{ $location->country_code == 'GM' ? 'selected' : '' }}>
                                            Gambia</option>
                                        <option value="GE" {{ $location->country_code == 'GE' ? 'selected' : '' }}>
                                            Georgia</option>
                                        <option value="DE" {{ $location->country_code == 'DE' ? 'selected' : '' }}>
                                            Germany</option>
                                        <option value="GH" {{ $location->country_code == 'GH' ? 'selected' : '' }}>
                                            Ghana</option>
                                        <option value="GI" {{ $location->country_code == 'GI' ? 'selected' : '' }}>
                                            Gibraltar</option>
                                        <option value="GR" {{ $location->country_code == 'GR' ? 'selected' : '' }}>
                                            Greece</option>
                                        <option value="GL" {{ $location->country_code == 'GL' ? 'selected' : '' }}>
                                            Greenland</option>
                                        <option value="GD" {{ $location->country_code == 'GD' ? 'selected' : '' }}>
                                            Grenada</option>
                                        <option value="GP" {{ $location->country_code == 'GP' ? 'selected' : '' }}>
                                            Guadeloupe</option>
                                        <option value="GU" {{ $location->country_code == 'GU' ? 'selected' : '' }}>
                                            Guam</option>
                                        <option value="GT" {{ $location->country_code == 'GT' ? 'selected' : '' }}>
                                            Guatemala</option>
                                        <option value="GG" {{ $location->country_code == 'GG' ? 'selected' : '' }}>
                                            Guernsey</option>
                                        <option value="GN" {{ $location->country_code == 'GN' ? 'selected' : '' }}>
                                            Guinea</option>
                                        <option value="GW" {{ $location->country_code == 'GW' ? 'selected' : '' }}>
                                            Guinea-Bissau
                                        </option>
                                        <option value="GY" {{ $location->country_code == 'GY' ? 'selected' : '' }}>
                                            Guyana</option>
                                        <option value="HT" {{ $location->country_code == 'HT' ? 'selected' : '' }}>
                                            Haiti</option>
                                        <option value="HM" {{ $location->country_code == 'HM' ? 'selected' : '' }}>
                                            Heard Island and
                                            McDonald Islands</option>
                                        <option value="VA" {{ $location->country_code == 'VA' ? 'selected' : '' }}>
                                            Holy See (Vatican
                                            City State)</option>
                                        <option value="HN" {{ $location->country_code == 'HN' ? 'selected' : '' }}>
                                            Honduras</option>
                                        <option value="HK" {{ $location->country_code == 'HK' ? 'selected' : '' }}>
                                            Hong Kong</option>
                                        <option value="HU" {{ $location->country_code == 'HU' ? 'selected' : '' }}>
                                            Hungary</option>
                                        <option value="IS" {{ $location->country_code == 'IS' ? 'selected' : '' }}>
                                            Iceland</option>
                                        <option value="IN" {{ $location->country_code == 'IN' ? 'selected' : '' }}>
                                            India</option>
                                        <option value="ID" {{ $location->country_code == 'ID' ? 'selected' : '' }}>
                                            Indonesia</option>
                                        <option value="IR" {{ $location->country_code == 'IR' ? 'selected' : '' }}>
                                            Iran, Islamic
                                            Republic of</option>
                                        <option value="IQ" {{ $location->country_code == 'IQ' ? 'selected' : '' }}>
                                            Iraq</option>
                                        <option value="IE" {{ $location->country_code == 'IE' ? 'selected' : '' }}>
                                            Ireland</option>
                                        <option value="IM" {{ $location->country_code == 'IM' ? 'selected' : '' }}>
                                            Isle of Man
                                        </option>
                                        <option value="IL" {{ $location->country_code == 'IL' ? 'selected' : '' }}>
                                            Israel</option>
                                        <option value="IT" {{ $location->country_code == 'IT' ? 'selected' : '' }}>
                                            Italy</option>
                                        <option value="JM" {{ $location->country_code == 'JM' ? 'selected' : '' }}>
                                            Jamaica</option>
                                        <option value="JP" {{ $location->country_code == 'JP' ? 'selected' : '' }}>
                                            Japan</option>
                                        <option value="JE" {{ $location->country_code == 'JE' ? 'selected' : '' }}>
                                            Jersey</option>
                                        <option value="JO" {{ $location->country_code == 'JO' ? 'selected' : '' }}>
                                            Jordan</option>
                                        <option value="KZ" {{ $location->country_code == 'KZ' ? 'selected' : '' }}>
                                            Kazakhstan
                                        </option>
                                        <option value="KE" {{ $location->country_code == 'KE' ? 'selected' : '' }}>
                                            Kenya</option>
                                        <option value="KI"
                                            {{ $location->country_code == 'KI' ? 'selected' : '' }}>Kiribati</option>
                                        <option value="KP"
                                            {{ $location->country_code == 'KP' ? 'selected' : '' }}>Korea, Democratic
                                            People's Republic of</option>
                                        <option value="KR"
                                            {{ $location->country_code == 'KR' ? 'selected' : '' }}>Korea, Republic of
                                        </option>
                                        <option value="KW"
                                            {{ $location->country_code == 'KW' ? 'selected' : '' }}>Kuwait</option>
                                        <option value="KG"
                                            {{ $location->country_code == 'KG' ? 'selected' : '' }}>Kyrgyzstan
                                        </option>
                                        <option value="LA"
                                            {{ $location->country_code == 'LA' ? 'selected' : '' }}>Lao People's
                                            Democratic Republic</option>
                                        <option value="LV"
                                            {{ $location->country_code == 'LV' ? 'selected' : '' }}>Latvia</option>
                                        <option value="LB"
                                            {{ $location->country_code == 'LB' ? 'selected' : '' }}>Lebanon</option>
                                        <option value="LS"
                                            {{ $location->country_code == 'LS' ? 'selected' : '' }}>Lesotho</option>
                                        <option value="LR"
                                            {{ $location->country_code == 'LR' ? 'selected' : '' }}>Liberia</option>
                                        <option value="LY"
                                            {{ $location->country_code == 'LY' ? 'selected' : '' }}>Libya</option>
                                        <option value="LI"
                                            {{ $location->country_code == 'LI' ? 'selected' : '' }}>Liechtenstein
                                        </option>
                                        <option value="LT"
                                            {{ $location->country_code == 'LT' ? 'selected' : '' }}>Lithuania</option>
                                        <option value="LU"
                                            {{ $location->country_code == 'LU' ? 'selected' : '' }}>Luxembourg
                                        </option>
                                        <option value="MO"
                                            {{ $location->country_code == 'MO' ? 'selected' : '' }}>Macao</option>
                                        <option value="MK"
                                            {{ $location->country_code == 'MK' ? 'selected' : '' }}>Macedonia, the
                                            former Yugoslav Republic of</option>
                                        <option value="MG"
                                            {{ $location->country_code == 'MG' ? 'selected' : '' }}>Madagascar
                                        </option>
                                        <option value="MW"
                                            {{ $location->country_code == 'MW' ? 'selected' : '' }}>Malawi</option>
                                        <option value="MY"
                                            {{ $location->country_code == 'MY' ? 'selected' : '' }}>Malaysia</option>
                                        <option value="MV"
                                            {{ $location->country_code == 'MV' ? 'selected' : '' }}>Maldives</option>
                                        <option value="ML"
                                            {{ $location->country_code == 'ML' ? 'selected' : '' }}>Mali</option>
                                        <option value="MT"
                                            {{ $location->country_code == 'MT' ? 'selected' : '' }}>Malta</option>
                                        <option value="MH"
                                            {{ $location->country_code == 'MH' ? 'selected' : '' }}>Marshall Islands
                                        </option>
                                        <option value="MQ"
                                            {{ $location->country_code == 'MQ' ? 'selected' : '' }}>Martinique
                                        </option>
                                        <option value="MR"
                                            {{ $location->country_code == 'MR' ? 'selected' : '' }}>Mauritania
                                        </option>
                                        <option value="MU"
                                            {{ $location->country_code == 'MU' ? 'selected' : '' }}>Mauritius</option>
                                        <option value="YT"
                                            {{ $location->country_code == 'YT' ? 'selected' : '' }}>Mayotte</option>
                                        <option value="MX"
                                            {{ $location->country_code == 'MX' ? 'selected' : '' }}>Mexico</option>
                                        <option value="FM"
                                            {{ $location->country_code == 'FM' ? 'selected' : '' }}>Micronesia,
                                            Federated States of</option>
                                        <option value="MD"
                                            {{ $location->country_code == 'MD' ? 'selected' : '' }}>Moldova, Republic
                                            of</option>
                                        <option value="MC"
                                            {{ $location->country_code == 'MC' ? 'selected' : '' }}>Monaco</option>
                                        <option value="MN"
                                            {{ $location->country_code == 'MN' ? 'selected' : '' }}>Mongolia</option>
                                        <option value="ME"
                                            {{ $location->country_code == 'ME' ? 'selected' : '' }}>Montenegro
                                        </option>
                                        <option value="MS"
                                            {{ $location->country_code == 'MS' ? 'selected' : '' }}>Montserrat
                                        </option>
                                        <option value="MA"
                                            {{ $location->country_code == 'MA' ? 'selected' : '' }}>Morocco</option>
                                        <option value="MZ"
                                            {{ $location->country_code == 'MZ' ? 'selected' : '' }}>Mozambique
                                        </option>
                                        <option value="MM"
                                            {{ $location->country_code == 'MM' ? 'selected' : '' }}>Myanmar</option>
                                        <option value="NA"
                                            {{ $location->country_code == 'NA' ? 'selected' : '' }}>Namibia</option>
                                        <option value="NR"
                                            {{ $location->country_code == 'NR' ? 'selected' : '' }}>Nauru</option>
                                        <option value="NP"
                                            {{ $location->country_code == 'NP' ? 'selected' : '' }}>Nepal</option>
                                        <option value="NL"
                                            {{ $location->country_code == 'NL' ? 'selected' : '' }}>Netherlands
                                        </option>
                                        <option value="NC"
                                            {{ $location->country_code == 'NC' ? 'selected' : '' }}>New Caledonia
                                        </option>
                                        <option value="NZ"
                                            {{ $location->country_code == 'NZ' ? 'selected' : '' }}>New Zealand
                                        </option>
                                        <option value="NI"
                                            {{ $location->country_code == 'NI' ? 'selected' : '' }}>Nicaragua</option>
                                        <option value="NE"
                                            {{ $location->country_code == 'NE' ? 'selected' : '' }}>Niger</option>
                                        <option value="NG"
                                            {{ $location->country_code == 'NG' ? 'selected' : '' }}>Nigeria</option>
                                        <option value="NU"
                                            {{ $location->country_code == 'NU' ? 'selected' : '' }}>Niue</option>
                                        <option value="NF"
                                            {{ $location->country_code == 'NF' ? 'selected' : '' }}>Norfolk Island
                                        </option>
                                        <option value="MP"
                                            {{ $location->country_code == 'MP' ? 'selected' : '' }}>Northern Mariana
                                            Islands</option>
                                        <option value="NO"
                                            {{ $location->country_code == 'NO' ? 'selected' : '' }}>Norway</option>
                                        <option value="OM"
                                            {{ $location->country_code == 'OM' ? 'selected' : '' }}>Oman</option>
                                        <option value="PK"
                                            {{ $location->country_code == 'PK' ? 'selected' : '' }}>Pakistan</option>
                                        <option value="PW"
                                            {{ $location->country_code == 'PW' ? 'selected' : '' }}>Palau</option>
                                        <option value="PS"
                                            {{ $location->country_code == 'PS' ? 'selected' : '' }}>Palestinian
                                            Territory, Occupied</option>
                                        <option value="PA"
                                            {{ $location->country_code == 'PA' ? 'selected' : '' }}>Panama</option>
                                        <option value="PG"
                                            {{ $location->country_code == 'PG' ? 'selected' : '' }}>Papua New Guinea
                                        </option>
                                        <option value="PY"
                                            {{ $location->country_code == 'PY' ? 'selected' : '' }}>Paraguay</option>
                                        <option value="PE"
                                            {{ $location->country_code == 'PE' ? 'selected' : '' }}>Peru</option>
                                        <option value="PH"
                                            {{ $location->country_code == 'PH' ? 'selected' : '' }}>Philippines
                                        </option>
                                        <option value="PN"
                                            {{ $location->country_code == 'PN' ? 'selected' : '' }}>Pitcairn</option>
                                        <option value="PL"
                                            {{ $location->country_code == 'PL' ? 'selected' : '' }}>Poland</option>
                                        <option value="PT"
                                            {{ $location->country_code == 'PT' ? 'selected' : '' }}>Portugal</option>
                                        <option value="PR"
                                            {{ $location->country_code == 'PR' ? 'selected' : '' }}>Puerto Rico
                                        </option>
                                        <option value="QA"
                                            {{ $location->country_code == 'QA' ? 'selected' : '' }}>Qatar</option>
                                        <option value="RE"
                                            {{ $location->country_code == 'RE' ? 'selected' : '' }}>Réunion</option>
                                        <option value="RO"
                                            {{ $location->country_code == 'RO' ? 'selected' : '' }}>Romania</option>
                                        <option value="RU"
                                            {{ $location->country_code == 'RU' ? 'selected' : '' }}>Russian Federation
                                        </option>
                                        <option value="RW"
                                            {{ $location->country_code == 'RW' ? 'selected' : '' }}>Rwanda</option>
                                        <option value="BL"
                                            {{ $location->country_code == 'BL' ? 'selected' : '' }}>Saint Barthélemy
                                        </option>
                                        <option value="SH"
                                            {{ $location->country_code == 'SH' ? 'selected' : '' }}>Saint Helena,
                                            Ascension and Tristan da Cunha</option>
                                        <option value="KN"
                                            {{ $location->country_code == 'KN' ? 'selected' : '' }}>Saint Kitts and
                                            Nevis</option>
                                        <option value="LC"
                                            {{ $location->country_code == 'LC' ? 'selected' : '' }}>Saint Lucia
                                        </option>
                                        <option value="MF"
                                            {{ $location->country_code == 'MF' ? 'selected' : '' }}>Saint Martin
                                            (French part)</option>
                                        <option value="PM"
                                            {{ $location->country_code == 'PM' ? 'selected' : '' }}>Saint Pierre and
                                            Miquelon</option>
                                        <option value="VC"
                                            {{ $location->country_code == 'VC' ? 'selected' : '' }}>Saint Vincent and
                                            the Grenadines</option>
                                        <option value="WS"
                                            {{ $location->country_code == 'WS' ? 'selected' : '' }}>Samoa</option>
                                        <option value="SM"
                                            {{ $location->country_code == 'SM' ? 'selected' : '' }}>San Marino
                                        </option>
                                        <option value="ST"
                                            {{ $location->country_code == 'ST' ? 'selected' : '' }}>Sao Tome and
                                            Principe</option>
                                        <option value="SA"
                                            {{ $location->country_code == 'SA' ? 'selected' : '' }}>Saudi Arabia
                                        </option>
                                        <option value="SN"
                                            {{ $location->country_code == 'SN' ? 'selected' : '' }}>Senegal</option>
                                        <option value="RS"
                                            {{ $location->country_code == 'RS' ? 'selected' : '' }}>Serbia</option>
                                        <option value="SC"
                                            {{ $location->country_code == 'SC' ? 'selected' : '' }}>Seychelles
                                        </option>
                                        <option value="SL"
                                            {{ $location->country_code == 'SL' ? 'selected' : '' }}>Sierra Leone
                                        </option>
                                        <option value="SG"
                                            {{ $location->country_code == 'SG' ? 'selected' : '' }}>Singapore</option>
                                        <option value="SX"
                                            {{ $location->country_code == 'SX' ? 'selected' : '' }}>Sint Maarten
                                            (Dutch part)</option>
                                        <option value="SK"
                                            {{ $location->country_code == 'SK' ? 'selected' : '' }}>Slovakia</option>
                                        <option value="SI"
                                            {{ $location->country_code == 'SI' ? 'selected' : '' }}>Slovenia</option>
                                        <option value="SB"
                                            {{ $location->country_code == 'SB' ? 'selected' : '' }}>Solomon Islands
                                        </option>
                                        <option value="SO"
                                            {{ $location->country_code == 'SO' ? 'selected' : '' }}>Somalia</option>
                                        <option value="ZA"
                                            {{ $location->country_code == 'ZA' ? 'selected' : '' }}>South Africa
                                        </option>
                                        <option value="GS"
                                            {{ $location->country_code == 'GS' ? 'selected' : '' }}>South Georgia and
                                            the South Sandwich Islands</option>
                                        <option value="SS"
                                            {{ $location->country_code == 'SS' ? 'selected' : '' }}>South Sudan
                                        </option>
                                        <option value="ES"
                                            {{ $location->country_code == 'ES' ? 'selected' : '' }}>Spain</option>
                                        <option value="LK"
                                            {{ $location->country_code == 'LK' ? 'selected' : '' }}>Sri Lanka</option>
                                        <option value="SD"
                                            {{ $location->country_code == 'SD' ? 'selected' : '' }}>Sudan</option>
                                        <option value="SR"
                                            {{ $location->country_code == 'SR' ? 'selected' : '' }}>Suriname</option>
                                        <option value="SJ"
                                            {{ $location->country_code == 'SJ' ? 'selected' : '' }}>Svalbard and Jan
                                            Mayen</option>
                                        <option value="SZ"
                                            {{ $location->country_code == 'SZ' ? 'selected' : '' }}>Swaziland</option>
                                        <option value="SE"
                                            {{ $location->country_code == 'SE' ? 'selected' : '' }}>Sweden</option>
                                        <option value="CH"
                                            {{ $location->country_code == 'CH' ? 'selected' : '' }}>Switzerland
                                        </option>
                                        <option value="SY"
                                            {{ $location->country_code == 'SY' ? 'selected' : '' }}>Syrian Arab
                                            Republic</option>
                                        <option value="TW"
                                            {{ $location->country_code == 'TW' ? 'selected' : '' }}>Taiwan, Province
                                            of China</option>
                                        <option value="TJ"
                                            {{ $location->country_code == 'TJ' ? 'selected' : '' }}>Tajikistan
                                        </option>
                                        <option value="TZ"
                                            {{ $location->country_code == 'TZ' ? 'selected' : '' }}>Tanzania, United
                                            Republic of</option>
                                        <option value="TH"
                                            {{ $location->country_code == 'TH' ? 'selected' : '' }}>Thailand</option>
                                        <option value="TL"
                                            {{ $location->country_code == 'TL' ? 'selected' : '' }}>Timor-Leste
                                        </option>
                                        <option value="TG"
                                            {{ $location->country_code == 'TG' ? 'selected' : '' }}>Togo</option>
                                        <option value="TK"
                                            {{ $location->country_code == 'TK' ? 'selected' : '' }}>Tokelau</option>
                                        <option value="TO"
                                            {{ $location->country_code == 'TO' ? 'selected' : '' }}>Tonga</option>
                                        <option value="TT"
                                            {{ $location->country_code == 'TT' ? 'selected' : '' }}>Trinidad and
                                            Tobago</option>
                                        <option value="TN"
                                            {{ $location->country_code == 'TN' ? 'selected' : '' }}>Tunisia</option>
                                        <option value="TR"
                                            {{ $location->country_code == 'TR' ? 'selected' : '' }}>Turkey</option>
                                        <option value="TM"
                                            {{ $location->country_code == 'TM' ? 'selected' : '' }}>Turkmenistan
                                        </option>
                                        <option value="TC"
                                            {{ $location->country_code == 'TC' ? 'selected' : '' }}>Turks and Caicos
                                            Islands</option>
                                        <option value="TV"
                                            {{ $location->country_code == 'TV' ? 'selected' : '' }}>Tuvalu</option>
                                        <option value="UG"
                                            {{ $location->country_code == 'UG' ? 'selected' : '' }}>Uganda</option>
                                        <option value="UA"
                                            {{ $location->country_code == 'UA' ? 'selected' : '' }}>Ukraine</option>
                                        <option value="AE"
                                            {{ $location->country_code == 'AE' ? 'selected' : '' }}>United Arab
                                            Emirates</option>
                                        <option value="GB"
                                            {{ $location->country_code == 'GB' ? 'selected' : '' }}>United Kingdom
                                        </option>
                                        <option value="US"
                                            {{ $location->country_code == 'US' ? 'selected' : '' }}>United States
                                        </option>
                                        <option value="UM"
                                            {{ $location->country_code == 'UM' ? 'selected' : '' }}>United States
                                            Minor Outlying Islands</option>
                                        <option value="UY"
                                            {{ $location->country_code == 'UY' ? 'selected' : '' }}>Uruguay</option>
                                        <option value="UZ"
                                            {{ $location->country_code == 'UZ' ? 'selected' : '' }}>Uzbekistan
                                        </option>
                                        <option value="VU"
                                            {{ $location->country_code == 'VU' ? 'selected' : '' }}>Vanuatu</option>
                                        <option value="VE"
                                            {{ $location->country_code == 'VE' ? 'selected' : '' }}>Venezuela,
                                            Bolivarian Republic of</option>
                                        <option value="VN"
                                            {{ $location->country_code == 'VN' ? 'selected' : '' }}>Viet Nam</option>
                                        <option value="VG"
                                            {{ $location->country_code == 'VG' ? 'selected' : '' }}>Virgin Islands,
                                            British</option>
                                        <option value="VI"
                                            {{ $location->country_code == 'VI' ? 'selected' : '' }}>Virgin Islands,
                                            U.S.</option>
                                        <option value="WF"
                                            {{ $location->country_code == 'WF' ? 'selected' : '' }}>Wallis and Futuna
                                        </option>
                                        <option value="EH"
                                            {{ $location->country_code == 'EH' ? 'selected' : '' }}>Western Sahara
                                        </option>
                                        <option value="YE"
                                            {{ $location->country_code == 'YE' ? 'selected' : '' }}>Yemen</option>
                                        <option value="ZM"
                                            {{ $location->country_code == 'ZM' ? 'selected' : '' }}>Zambia</option>
                                        <option value="ZW"
                                            {{ $location->country_code == 'ZW' ? 'selected' : '' }}>Zimbabwe</option>

                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="postcode">Postcode(Zip)</label>
                                    <input type="text" class="form-control" name="postcode", id="postcode"
                                        value="{{ $location->postcode ?? '' }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="lat">Latitude</label>
                                    <input type="text" class="form-control" name="lat", id="lat"
                                        value="{{ $location->latitude ?? '' }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="long">Longitude</label>
                                    <input type="text" class="form-control" name="long", id="long"
                                        value="{{ $location->longitude ?? '' }}">
                                </div>
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script></script>
@endsection
