<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CurrencyExchangeRate;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CurrencyExchangeRateController extends Controller
{

    public function index()
    {
        $countries = Country::all();
        return view('admin.currency_exchange.index', compact('countries'));
    }

    public function currencyExchangeRateList()
    {
        $items = CurrencyExchangeRate::orderBy('id', 'desc')->get();
        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('name', function ($item) {
                return ($item->country->name ?? 'N/A');
            })
            ->addColumn('iso2', function ($item) {
                return (($item->country->iso2 ?? 'N/A'));
            })
            ->addColumn('exchange_rate', function ($item) {
                return number_format($item->exchange_rate, 2) . ' ' . $item->country?->currency_symbol;
            })
            ->addColumn('action', function ($item) {
                // removeCurrencyExchangeRate
                $btn = '<a href="javascript:void(0)" onclick="editWeightCost(' . $item->id . ')" class="btn btn-info btn-sm" ><i class="fa fa-pencil"></i> Edit </a>';
                $btn .= '<a href="' . route('removeCurrencyExchangeRate', $item->id) . '"  class="btn btn-danger btn-sm ms-2" ><i class="fa fa-trash"></i> Remove </a>';
                return $btn;
            })
            ->rawColumns(['action', 'exchange_rate'])
            ->make(true);
    }

    public function updateCurrencyExchangeRate(Request $request)
    {
        try {

            $currency = CurrencyExchangeRate::where('id', $request->currency_id)->update([
                'exchange_rate' => $request->exchange_rate,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Currency exchange rate updated successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ]);
        }
    }

    public function storeCurrencyExchangeRate(Request $request)
    {
        try {
            CurrencyExchangeRate::create([
                'exchange_rate' => $request->exchange_rate,
                'country_id' => $request->country_id,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Currency exchange rate Added successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ]);
        }
    }

    public function getCurrencyExchangeRate($id)
    {
        try {

            $currency = CurrencyExchangeRate::find($id);
            return response()->json([
                'success' => true,
                'data' => $currency
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ]);
        }
    }

    public function removeCurrencyExchangeRate($id)
    {
        $currency = CurrencyExchangeRate::find($id);
        $currency->delete();
        return redirect()->back()->with(['message' => 'Record Removed Successfully !', 'message_type' => 'success']);
    }

    public function assignCurrency(Request $request, $user_id)
    {
        try {
            $user = User::find($user_id);
            $user->currency_id = $request->currency_id;
            $user->pickup_comission_percentage = $request->pickup_comission_percentage;
            $user->save();
            return back()->with(['message' => 'Currency Add Successfully', 'message_type' => 'success']);
        } catch (\Throwable $th) {
            return back()->with('message', 'An Erorr Accured' . $th->getMessage());
        }
    }
}
