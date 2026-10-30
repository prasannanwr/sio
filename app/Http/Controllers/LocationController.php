<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        AdminController::canDispatch();
        return view('admin.settings.locations.index');
    }

    /**
     * ajax function for locationList
     */
    public function locationList()
    {
        AdminController::canDispatch();
        $loaction = Location::orderBy('name', 'ASC')->get();

        return Datatables::of($loaction)
            ->addIndexColumn()
            ->addColumn('edit', function ($loaction) {
                $edit_url = route('locations.edit', $loaction->id);
                return '<a href="' . $edit_url . '" class="btn btn-info btn-sm" ><i class="fa fa-pencil"></i> Edit</a>';
            })
            ->addColumn('view', function ($loaction) {
                $url = route('locations.show', $loaction->id);
                return '<a href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View</a>';
            })
            ->addColumn('delete', function ($loaction) {
                $url = route('locations.destroy', $loaction->id);
                return '<form method="POST" action="' . $url . '">
                            <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                            <input type="hidden" name = "_method" value ="DELETE">
                            <button type="submit" onclick="return confirm(\'Are you sure you wish to delete this entry?\')" class="btn btn-sm btn-danger">Delete</button>
                        </form>';
            })
            ->rawColumns(['edit', 'view', 'delete'])
            ->make(true);
    }

    /**
     * ajax search
     */
    public function search(Request $request)
    {
        $query = $request->get('term', '');

        $locations = Location::where('name', 'LIKE', '%' . $query . '%')
            ->orWhere('latitude', 'LIKE', '%' . $query . '%')
            ->orWhere('longitude', 'LIKE', '%' . $query . '%')
            ->orWhere('postcode', 'LIKE', '%' . $query . '%')
            ->select('id', 'name', 'latitude', 'longitude', 'postcode')
            ->get();

        $results = [];

        foreach ($locations as $location) {
            $results[] = [
                'label' => $location->postcode . '-' . $location->name . ' [Lat: ' . $location->latitude . ', Long: ' . $location->longitude . ']',
                'value' => $location->id, // You can set this to whatever value you need
            ];
        }
        return response()->json($results);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        AdminController::canDispatch();
        return view('admin.settings.locations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        AdminController::canDispatch();
        try {
            $request->validate([
                'name' => 'required|string',
                'lat' => 'required|numeric',
                'long' => 'required|numeric',
                'postcode' => 'required',
                'country_code' => 'required'
            ]);
            DB::beginTransaction();
            $l = new Location;
            $l->name = $request->name;
            $l->latitude = $request->lat;
            $l->longitude = $request->long;
            $l->postcode = $request->postcode;
            $l->country_code = $request->country_code;
            $l->save();
            DB::commit();
            return back()->with(['message' => 'Location Saved', 'message_type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show(Location $location)
    {
        AdminController::canDispatch();
        return view('admin.settings.locations.show')->with('location', $location);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {
        AdminController::canDispatch();
        return view('admin.settings.locations.edit')->with(['location' => $location]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Location $location)
    {
        AdminController::canDispatch();
        try {
            $request->validate([
                'name' => 'required|string',
                'lat' => 'required|numeric',
                'long' => 'required|numeric',
                'postcode' => 'required',
                'country_code' => 'required'
            ]);
            DB::beginTransaction();
            $l = $location;
            $l->name = $request->name;
            $l->latitude = $request->lat;
            $l->longitude = $request->long;
            $l->postcode = $request->postcode;
            $l->country_code = $request->country_code;
            $l->update();
            DB::commit();
            return back()->with(['message' => 'Location Updated', 'message_type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location)
    {

        AdminController::canDispatch();
        if (!$location) {
            abort(404);
        }

        try {
            $location->delete();
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === 23000) {
                // Foreign key constraint violated
                return back()->with('message', 'Cannot delete resource. There are foreign key constraints in place.');
            } else {
                throw $e;
            }
        }

        return redirect()->route('resource.index')->with(['message' => 'Location deleted', 'message_type' => 'success']);
    }
}
