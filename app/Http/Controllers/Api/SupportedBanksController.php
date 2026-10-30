<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportedBanks;
use Illuminate\Http\Request;

class SupportedBanksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banks = SupportedBanks::all();
        return response()->json(['status' => true, 'message' => 'All supported banks retrieved successfully', 'data' => $banks]);
    }

    public function listByCountry(Request $request)
    {
        ///supported-banks?country=NG
        $request->validate([
            'country' => 'required'
        ]);
        $banks = SupportedBanks::where('country_code', '=', $request->country)->get();
        return response()->json(['status' => true, 'message' => 'Supported banks retrieved successfully', 'data' => $banks]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SupportedBanks  $supportedBanks
     * @return \Illuminate\Http\Response
     */
    public function show(SupportedBanks $supportedBanks)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SupportedBanks  $supportedBanks
     * @return \Illuminate\Http\Response
     */
    public function edit(SupportedBanks $supportedBanks)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SupportedBanks  $supportedBanks
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SupportedBanks $supportedBanks)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SupportedBanks  $supportedBanks
     * @return \Illuminate\Http\Response
     */
    public function destroy(SupportedBanks $supportedBanks)
    {
        //
    }
}
