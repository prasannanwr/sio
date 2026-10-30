@extends('admin.app')
@section('page_title', 'Create Funds Transfer Rate')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Settings</h5>
                @include('admin.settings.nav')
                <hr>
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route('eu_fund_rates.index') }}" class="btn btn-danger float-right"><i
                                class="fa fa-times"></i>Exit</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        @include('admin.partials.notification')
                        <h5>Create New EU Funds Transfer Rate</h5>
                        <form action="{{ route('eu_fund_rates.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ old('name') }}" required>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <div class="ui-widget">
                                        <label for="s_country_eu">Origin Country</label>
                                        <input style="width: 100%" type="text" name="s_country_eu"
                                            value="{{ old('s_country_eu') }}" class="form-control" id="s_country_eu"
                                            autocomplete="off" placeholder="Sender Country" required>
                                    </div>
                                    {{-- <select name="origin" id="origin" class="form-control">
                                        <option value="">--select origin--</option>
                                        @foreach ($locations as $loc)
                                            <option value="{{ $loc->id }}">{{ $loc->name }}[Lat:
                                                {{ $loc->latitude }}, Long: {{ $loc->longitude }}]</option>
                                        @endforeach
                                    </select> --}}
                                </div>
                                <div class="form-group col-md-6">

                                    <div class="ui-widget">
                                        <label for="rx_country_eu">Destination Country</label>
                                        <input style="width: 100%" type="text" name="rx_country_eu"
                                            value="{{ old('rx_country_eu') }}" class="form-control" id="rx_country_eu"
                                            placeholder="Receiver Country" autocomplete="off" required>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="calc">Commision Calculation</label>
                                    <select name="calc" id="calc" class="form-control" required>
                                        <option value="perc">Percentage</option>
                                        <option value="fixed">Fixed Amount</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="commision">Commision Amount Or Percentage</label>
                                    <input step="any" min="0" max="100" class="form-control" type="number"
                                        name="commision" id="commision" value="{{ old('commision') }}" required>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="min_amt">Minimum Amount supported</label>
                                    <input type="number" name="min_amt" id="min_amt" min="0" step="any" class="form-control">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="max_amt">Maximum Amount supported</label>
                                    <input type="number" name="max_amt" id="max_amt" min="0" step="any" class="form-control">
                                </div>
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
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
    </script>
@endsection
