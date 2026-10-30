<?php

namespace App\Http\Controllers;

use App\Models\EUFundsTransferRates;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class EUFundsTransferRatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.settings.eu_fund_rates.index');
    }


    public function EUFundsTransferRatesList()
    {
        $rate = EUFundsTransferRates::where('status', 1)->get();

        return Datatables::of($rate)
            ->addIndexColumn()
            ->addColumn('location', function ($rate) {
                $mar = "Origin : " . $rate->s_country_eu . "<br>";
                $mar .= "Destination:" . $rate->rx_country_eu;
                return $mar;
            })
            ->addColumn('commision', function ($rate) {
                $mar = "Commision Calculation : " . $rate->calc . "<br>";
                $mar .= "Commision Value:" . $rate->commision;
                return $mar;
            })
            ->addColumn('limits', function ($rate) {
                $mar = "Minimum Amout Allowed : " . $rate->min_amt . "<br>";
                $mar .= "Maximum Amout Allowed:" . $rate->max_amt;
                return $mar;
            })
            ->addColumn('edit', function ($rate) {
                $edit_url = route('eu_fund_rates.edit', $rate->id);
                return '<a href="' . $edit_url . '" class="btn btn-info btn-sm" ><i class="fa fa-pencil"></i> Edit</a>';
            })
            // ->addColumn('view', function ($rate) {
            //     $url = route('eu_fund_rates.show', $rate->id);
            //     return '<a href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View</a>';
            // })
            ->addColumn('delete', function ($rate) {
                $url = route('eu_fund_rates.destroy', $rate->id);
                return '<form method="POST" action="' . $url . '">
                            <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                            <input type="hidden" name = "_method" value ="DELETE">
                            <button type="submit" onclick="return confirm(\'Are you sure you wish to delete this entry?\')" class="btn btn-sm btn-danger">Delete</button>
                        </form>';
            })
            ->rawColumns(['commision', 'location', 'delete', 'limits', 'edit'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.settings.eu_fund_rates.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                's_country_eu' => 'required',
                'rx_country_eu' => 'required',
                'calc' => 'required',
                'commision' => 'required',
                'min_amt' => 'required',
                'max_amt' => 'required'

            ]);
            DB::beginTransaction();
            $l = new EUFundsTransferRates;
            $l->name = $request->name;
            $l->s_country_eu = $request->s_country_eu;
            $l->rx_country_eu = $request->rx_country_eu;
            $l->calc = $request->calc;
            $l->commision = $request->commision;
            $l->min_amt = $request->min_amt;
            $l->max_amt = $request->max_amt;
            $l->save();
            DB::commit();
            return back()->with(['message' => 'EU Funds transfer Route/ Rate Saved', 'message_type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EUFundsTransferRates  $eUFundsTransferRates
     * @return \Illuminate\Http\Response
     */
    public function show(EUFundsTransferRates $eUFundsTransferRates)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EUFundsTransferRates  $eUFundsTransferRates
     * @return \Illuminate\Http\Response
     */
    public function edit($eUFundsTransferRates)
    {
        $eUFundsTransferRates = EUFundsTransferRates::find($eUFundsTransferRates);
        return view('admin.settings.eu_fund_rates.edit', ['eUFundsTransferRates' => $eUFundsTransferRates]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EUFundsTransferRates  $eUFundsTransferRates
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $eUFundsTransferRates)
    {
        try {
            $eUFundsTransferRates = EUFundsTransferRates::where('id', $eUFundsTransferRates)->first();
            $request->validate([
                'name' => 'required',
                's_country_eu' => 'required',
                'rx_country_eu' => 'required',
                'calc' => 'required',
                'commision' => 'required',
                'min_amt' => 'required',
                'max_amt' => 'required'

            ]);
            DB::beginTransaction();
            $l = $eUFundsTransferRates;
            $l->name = $request->name;
            $l->s_country_eu = $request->s_country_eu;
            $l->rx_country_eu = $request->rx_country_eu;
            $l->calc = $request->calc;
            $l->commision = $request->commision;
            $l->min_amt = $request->min_amt;
            $l->max_amt = $request->max_amt;
            $l->update();
            DB::commit();
            return back()->with(['message' => 'EU Funds transfer Route/ Rate Updated', 'message_type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EUFundsTransferRates  $eUFundsTransferRates
     * @return \Illuminate\Http\Response
     */
    public function destroy($eUFundsTransferRates)
    {
        try {
            $eUFundsTransferRates = EUFundsTransferRates::where('id', $eUFundsTransferRates)->update([
                'status' => 0
            ]);
            return back()->with(['message' => 'EU Funds transfer Route/ Rate Deleted', 'message_type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage());
        }
    }
}
