<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function getStates($countryId)
    // {
    {
        $states = State::select('name', 'id')->where('country_id', $countryId)->get();

        return response()->json([
            'success' => true,
            'states' => $states->toArray(),
        ], 200);
    }
}
