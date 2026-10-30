<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Countries;
use App\Models\IntlFundsTransferRates;
use Illuminate\Http\Request;

class CountriesController extends Controller
{
    public function index()
    {
        $countries = Countries::orderBy('country_name', 'ASC')->get();
        return response()->json(['status' => true, 'message' => 'Countries retrieved successfully', 'data' => $countries]);
    }

    public function receiving()
    {
        $countries = Countries::where('receiving', 'yes')
            ->orderBy('country_name', 'ASC')
            ->get();

        return response()->json(['status' => true, 'message' => 'Countries retrieved successfully', 'data' => $countries]);
    }

    public function sending()
    {
        $countries = Countries::where('sending', 'yes')
            ->orderBy('country_name', 'ASC')
            ->get();

        return response()->json(['status' => true, 'message' => 'Countries retrieved successfully', 'data' => $countries]);
    }

    public function getpayoutChannels(Request $request)
    {
        $rates = IntlFundsTransferRates::all();
        if ($rates) {
            return response()->json(['status' => true, 'message' => 'Countries/rates retrieved successfully', 'data' => $rates]);
        }
    }

    public function getpayoutChannelsByCountry(Request $request)
    {
        $request->validate([
            's_country' => 'required',
            'rx_country' => 'required',
            // 's_currency' => 'nullable',
            // 'rx_currency' => 'nullable',
        ]);
        $rates = IntlFundsTransferRates::query();

        $rates->where('s_country', $request->s_country);
        $rates->where('rx_country', $request->rx_country);
        $r = $rates->get();
        if ($r) {
            return response()->json(['status' => true, 'message' => 'Countries/rates retrieved successfully', 'data' => $r]);
        }
    }
}
