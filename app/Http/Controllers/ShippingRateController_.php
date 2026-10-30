<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\CityShippingCost;
use App\Models\Country;
use App\Models\CountryShippingCost;
use App\Models\Location;
use App\Models\ShippingCost;
use App\Models\ShippingRate;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Exception;

class ShippingRateController extends Controller
{
    public function __construct()
    {
        AdminController::canDispatch();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.settings.shipping_rates.index');
    }

    /**
     * ajax function for locationList
     */
    public function shippingRatesList()
    {

        // $items = Country::select('countries.id', 'countries.name', 'countries.iso2')
        //     ->leftJoin('shipping_costs', 'countries.id', '=', 'shipping_costs.country_id')
        //     ->selectRaw('MIN(shipping_costs.weight) as min_weight, MAX(shipping_costs.weight) as max_weight')
        //     ->groupBy('countries.id')
        //     ->get();

        // $shippingCosts = ShippingCost::whereIn('country_id', $items->pluck('id'))
        //     ->select('country_id', 'weight', 'cost')
        //     ->get()
        //     ->groupBy('country_id');

        $items = Country::with(['shippingCountryCosts' => function ($query) {
            $query->select('country_id', 'weight', 'cost');
        }])
            ->select('id', 'name', 'iso2');
        
        // Shipping Costs প্রসেস করা
        // $items->each(function ($item) {
        //     $item->min_weight = $item->shippingCosts->min('weight');
        //     $item->max_weight = $item->shippingCosts->max('weight');
        //     $item->min_cost = $item->shippingCosts->where('weight', $item->min_weight)->first()->cost ?? 0;
        //     $item->max_cost = $item->shippingCosts->where('weight', $item->max_weight)->first()->cost ?? 0;
        // });
        
        // dd($items);

        return DataTables::of($items)
        ->addIndexColumn()
        ->filter(function ($query) {
            if (request()->has('search')) {
                $search = request('search')['value'];
                $query->where('name', 'like', "%{$search}%")
                ->orWhere('iso2', 'like', "%{$search}%");
            }
        })
        ->addColumn('name', function ($item) {
            return $item->name ?? 'N/A';
        })
        ->addColumn('iso2', function ($item) {
            return $item->iso2 ?? 'N/A';
        })
        ->addColumn('shipping_cost', function ($item) {
            $minWeight = $item->shippingCountryCosts->min('weight');
            $maxWeight = $item->shippingCountryCosts->max('weight');
    
            $minCost = $item->shippingCountryCosts->where('weight', $minWeight)->first()->cost ?? 0;
            $maxCost = $item->shippingCountryCosts->where('weight', $maxWeight)->first()->cost ?? 0;
    
            return $minCost . ' - ' . $maxCost;
        })
        ->addColumn('action', function ($item) {
            $urlc = route('cities.shipping.country', $item->id);
            $url = route('cities.shipping.rates', $item->id);
            $weightUrl = route('weight.shipping.rates', $item->id);
    
            return '
            <a href="' . $urlc . '" class="btn btn-info btn-sm"><i class="fa fa-eye me-2"></i>Country</a> 
            <a href="' . $url . '" class="btn btn-info btn-sm"><i class="fa fa-eye me-2"></i>City</a> 
            <a href="' . $weightUrl . '" class="btn btn-primary btn-sm"><i class="fa fa-eye me-1"></i>Weight</a>';
        })
        ->rawColumns(['action', 'shipping_cost'])
        ->make(true);
    }

    /**
     * ajax fetch
     */
    public function rates_fetch(Request $request)
    {
        // dd($request->all());
        $shipFromCountryId = $request->ship_from_country;
        $shipFromCityId = $request->ship_from_city;
        $shipToCountryId = $request->ship_to_country;
        $shipToCityId = $request->ship_to_city;
        $totalWeight = (int) $request->weightTotal;
        // return $shipFromCountryId;
        $shippingCostPrice = 0;
        $day = 'No delivery day';
        $percentage = 1;

        // ---------- day calculation 
        if($shipFromCountryId==$shipToCountryId){
            if(CityShippingCost::where('country_id', $shipFromCountryId)->exists()){
                $day = CityShippingCost::where('country_id', $shipFromCountryId)->first()->day;
            }
    }
    else{
        if(CountryShippingCost::where('country_id', $shipFromCountryId)->where('city_id', $shipToCountryId)->exists()){
            $day = CountryShippingCost::where('country_id', $shipFromCountryId)->where('city_id', $shipToCountryId)->first()->day;
            $percentage = CountryShippingCost::where('country_id', $shipFromCountryId)->where('city_id', $shipToCountryId)->first()->percentage;
        }


        }
        
        // ---------- day calculation 


        $shippingCost = ShippingCost::where('country_id', $shipFromCountryId)->where('weight', $totalWeight)->first();
        if ($shipFromCountryId == $shipToCountryId) {
            $cityShppingCost = CityShippingCost::where('country_id', $shipFromCountryId)->where('city_id', $shipFromCityId)->first();
            if ($cityShppingCost) {
                $shippingCostPrice = (float) $shippingCost->cost / (float) $percentage ;
            } else {
                return response()->json([
                    'success' => false,
                    "message" => "First Define the shipping cost Of the City",
                ]);
            }
        } else {
            $shippingCostPrice = (float) $shippingCost->cost / (float) $percentage;
        }
        // dd($shippingCost);

        // $o = Location::find($request->origin);
        // $d = Location::find($request->dest);

        //if the count array and others are not supplied, it means we are estimating the ship cost of a single item
        //most likely a request from the json api, that is cusumed by siostore...this is deprecated, as the rates_fetch_api() method handles external requests now
        // if (!isset($request->countArray)) {
        //     $request->countArray = [$request->count];
        //     $request->descArray = [$request->descArray];
        //     $request->weightArray = [$request->weightArray];
        //     $request->valueArray = [$request->valueArray];
        // }

        //get the rates
        // $rates = ShippingRate::where('origin_id', $request->origin)
        //     ->where('destination_id', $request->dest)
        //     ->where('weight_start', '<=', $request->weight)
        //     ->where('weight_end', '>=', $request->weight)
        //     ->where('length', '>=', $request->length)
        //     ->where('width', '>=', $request->width)
        //     ->where('height', '>=', $request->height)
        //     ->with(['destination', 'origin'])
        //     ->limit(20)
        //     ->get();


        $resp = [
            'day' => $day,
            'shipping_cost' => $shippingCostPrice,
            'ship_from' => 'County : ' . Country::find($shipFromCountryId)->name . '<br>, City : ' . City::find($shipFromCityId)->name,
            'ship_to' => 'County : ' . Country::find($shipToCountryId)->name . '<br>, City : ' . City::find($shipToCityId)->name,
            'total_weight' => $request->weightTotal,
            'heightTotal' => $request->heightTotal,
            'widthTotal' => $request->widthTotal,
            'lengthTotal' => $request->lengthTotal,
            'valueTotal' => $request->valueTotal,
            'countTotal' => $request->countTotal,
            'total' => (float)  $shippingCostPrice
        ];
        return response()->json([
            'success' => true,
            'data' => $resp
        ]);



        //lets remove the fedw

        // $commodities = [];

        // for ($i = 0; $i < count($request->countArray); $i++) {
        //     $r = [
        //         'description' => $request->descArray[$i],
        //         'numberOfPieces' => $request->countArray[$i],
        //         'weight' => [
        //             'value' => $request->weightArray[$i],
        //             'units' => 'KG'
        //         ],
        //         'quantity' => $request->countArray[$i],
        //         'quantityUnits' => 'PCS',
        //         'unitPrice' => [
        //             'amount' => $request->valueArray[$i],
        //             'currency' => 'EUR'
        //         ],
        //         'customsValue' => [
        //             'amount' => $request->valueArray[$i],
        //             'currency' => 'EUR'
        //         ]
        //     ];

        //     array_push($commodities, $r);
        // }

        // $fedExData = [
        //     'shipper_city' => $o->name,
        //     'recipient_city' => $d->name,
        //     'shipper_zip' => $o->postcode,
        //     'recipient_zip' => $d->postcode,
        //     'shipper_country_code' => $o->country_code,
        //     'recipient_country_code' => $d->country_code,
        //     'commodities' => $commodities,
        //     'requestedPackageLineItems' => [
        //         [
        //             'weight' => [
        //                 'value' => $request->weight,
        //                 'units' => 'KG'
        //             ]
        //         ]
        //     ]

        // ];

        // $fedExResponse = FedExController::estimateCost($fedExData);
        // // dd($fedExResponse->original);
        // if ($fedExResponse->status() == 200) {
        //     $fedex_price = ($fedExResponse->original['data']->ratedShipmentDetails[0]->totalNetChargeWithDutiesAndTaxes);

        //     $fedex_price = ((env('FEDEX_API_PROFIT_PERC') / 100) * $fedex_price) + $fedex_price;

        //     $resp = ['siopay' => $rates, 'fedex' => $fedex_price];

        //     return response()->json($resp);
        // } else {
        //     return response()->json(['status' => false, 'msg' => "failed to estimate cost at fedex"], 500);
        // }
    }

    /**
     * api fetch
     */
    public function rates_fetch_api(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
            'length' => 'required|numeric',
            'count' => 'required|numeric',
            'item_desc' => 'required',
            'item_value' => 'nullable|numeric',
            'origin_city' => 'required',
            'dest_city' => 'required',
            'origin_zip' => 'required',
            'dest_zip' => 'required',
            'origin_country' => 'required',
            'dest_country' => 'required'
        ]);

        $commodities = [];
        $r = [
            'description' => $request->item_desc,
            'numberOfPieces' => $request->count,
            'weight' => [
                'value' => $request->weight,
                'units' => 'KG'
            ],
            'quantity' => $request->count,
            'quantityUnits' => 'PCS',
            'unitPrice' => [
                'amount' => $request->item_value,
                'currency' => 'EUR'
            ],
            'customsValue' => [
                'amount' => $request->item_value,
                'currency' => 'EUR'
            ]
        ];

        array_push($commodities, $r);


        $fedExData = [
            'shipper_city' => $request->origin_city,
            'recipient_city' => $request->dest_city,
            'shipper_zip' => $request->origin_zip,
            'recipient_zip' => $request->dest_zip,
            'shipper_country_code' => $request->origin_country,
            'recipient_country_code' => $request->dest_country,
            'commodities' => $commodities,
            'requestedPackageLineItems' => [
                [
                    'weight' => [
                        'value' => $request->weight,
                        'units' => 'KG'
                    ]
                ]
            ]

        ];

        // $fedExResponse = FedExController::estimateCost($fedExData);

        $o = Location::where('name', 'LIKE', "%" . $request->origin_city . "%")->where('country_code', $request->origin_country)->first();
        $d = Location::where('name', 'LIKE', "%" . $request->dest_city . "%")->where('country_code', $request->dest_country)->first();

        if ($o && $d) {
            $rate = ShippingRate::where('origin_id', $o->id)
                ->where('destination_id', $d->id)
                ->where('weight_start', '<=', $request->weight)
                ->where('weight_end', '>=', $request->weight)
                ->where('length', '>=', $request->length)
                ->where('width', '>=', $request->width)
                ->where('height', '>=', $request->height)
                ->with(['destination', 'origin'])
                ->first();
        } else {
            $rate = false;
        }
        // dd($fedExResponse->original);
        // if ($fedExResponse->status() == 200) {
        //     $fedex_price = ($fedExResponse->original['data']->ratedShipmentDetails[0]->totalNetChargeWithDutiesAndTaxes);

        //     $fedex_price = ((env('FEDEX_API_PROFIT_PERC') / 100) * $fedex_price) + $fedex_price;
        // } else {
        //     $fedex_price = '';
        // }

        //TODO:return trnasit time in rate
        if ($rate) {
            // $resp = ['siopay' => $rate->price, 'fedex' => $fedex_price];
            $resp = ['siopay' => $rate->price, 'fedex' => ''];
        } else {
            // $resp = ['siopay' => '', 'fedex' => $fedex_price];
            $resp = ['siopay' => '', 'fedex' => ''];
        }

        return response()->json($resp);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $locations = Location::all();
        return view('admin.settings.shipping_rates.create')->with('locations', $locations);
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
                'origin' => 'required|exists:locations,id',
                'dest' => 'required|exists:locations,id',
                'min_weight' => 'required|numeric|',
                'max_weight' => 'required|numeric',
                'width' => 'required|numeric',
                'len' => 'required|numeric',
                'height' => 'required|numeric',
                'price' => 'required|numeric',
                'desc' => 'nullable|string',
                'transit_days' => 'required'
            ]);
            DB::beginTransaction();
            $l = new ShippingRate;
            $l->name = $request->name;
            $l->origin_id = $request->origin;
            $l->destination_id = $request->dest;
            $l->weight_start = $request->min_weight;
            $l->weight_end = $request->max_weight;
            $l->price = $request->price;
            $l->height = $request->height;
            $l->width = $request->width;
            $l->length = $request->len;
            $l->transit_days = $request->transit_days;
            $l->desc = $request->desc ?? '';
            $l->save();
            DB::commit();
            return back()->with(['message' => 'Shipping Route/ Rate Saved', 'message_type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ShippingRate  $shippingRate
     * @return \Illuminate\Http\Response
     */
    public function show(ShippingRate $shippingRate)
    {
        return view('admin.settings.shipping_rates.show')->with('rate', $shippingRate);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ShippingRate  $shippingRate
     * @return \Illuminate\Http\Response
     */
    public function edit(ShippingRate $shippingRate)
    {
        $locations = Location::all();
        return view('admin.settings.shipping_rates.edit', ['shippingRate' => $shippingRate, 'locations' => $locations]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShippingRate  $shippingRate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShippingRate $shippingRate)
    {
        try {
            $request->validate([
                'name' => 'required',
                'origin' => 'required|exists:locations,id',
                'dest' => 'required|exists:locations,id',
                'min_weight' => 'required|numeric|',
                'max_weight' => 'required|numeric',
                'width' => 'required|numeric',
                'len' => 'required|numeric',
                'height' => 'required|numeric',
                'pickup_cost_per_km' => 'required|numeric',
                'delivery_cost_per_km' => 'required|numeric',
                'price' => 'required|numeric',
                'desc' => 'nullable|string'
            ]);
            DB::beginTransaction();
            $l = $shippingRate;
            $l->name = $request->name;
            $l->origin_id = $request->origin;
            $l->destination_id = $request->dest;
            $l->weight_start = $request->min_weight;
            $l->weight_end = $request->max_weight;
            $l->price = $request->price;
            $l->height = $request->height;
            $l->pickup_cost_per_km = $request->pickup_cost_per_km;
            $l->delivery_cost_per_km = $request->delivery_cost_per_km;
            $l->width = $request->width;
            $l->length = $request->len;
            $l->desc = $request->desc ?? '';
            $l->update();
            DB::commit();
            return back()->with(['message' => 'Shipping Route/ Rate Updated', 'message_type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShippingRate  $shippingRate
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShippingRate $shippingRate)
    {
        if (!$shippingRate) {
            abort(404);
        }

        try {
            $shippingRate->delete();
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === 23000) {
                // Foreign key constraint violated
                return back()->with('message', 'Cannot delete resource. There are foreign key constraints in place.');
            } else {
                throw $e;
            }
        }

        return redirect()->route('resource.index')->with(['message' => 'Shipping Rate deleted', 'message_type' => 'success']);
    }


    public function country()
    {
        return view('backend.admin.all_countries');
    }



    public function citiseShippingCountry($id)
    {
        $country = Country::find($id);
        $data = [
            'country' => $country,
            'cities' => Country::whereNotIn('id',[$id   ])->orderBy('name')->get() ?? [],

        ];
        return view('admin.settings.shipping_rates.city_shipping_country', $data);
    }

    public function citiseShippingRates($id)
    {
        $country = Country::find($id);
        $data = [
            'country' => $country,
            'cities' => $country->cities()->orderBy('name')->get() ?? [],

        ];
        return view('admin.settings.shipping_rates.city_shipping_cost', $data);
    }

    public function weightShippingRates($id)
    {
        $country = Country::find($id);
        $shippingCosts = ShippingCost::where('country_id', $country->id)->get();
        $data = [
            'country' => $country,
            'shippingCosts' => $shippingCosts ?? [],
        ];
        return view('admin.settings.shipping_rates.weight_shipping_cost', $data);
    }

    public function citiesShippingRatesList($country_id)
    {
        $items = City::where('country_id', $country_id)->orderBy('name')->get();
        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('name', function ($item) {
                return ($item->name ?? 'N/A');
            })
            ->addColumn('shipping_percentage', function ($item) {
                $shippingCost = CityShippingCost::where('city_id', $item->id)->where('country_id', $item->country_id)->first();
                if (isset($shippingCost->percentage) && $shippingCost->percentage > 0) {
                    return $shippingCost->percentage . ' %';
                }
                return 'N/A';
            })
            ->addColumn('day', function ($item) {
                $shippingCost = CityShippingCost::where('city_id', $item->id)->where('country_id', $item->country_id)->first();
                if (isset($shippingCost->day)) {
                    return $shippingCost->day;
                }
                return 'N/A';
            })
            ->addColumn('shipping_cost', function ($item) {
                $shippingCost = CityShippingCost::where('city_id', $item->id)->where('country_id', $item->country_id)->first();
                if (isset($shippingCost->percentage) && $shippingCost->percentage > 0) {
                    $min = ShippingCost::where('country_id', $item->country_id)->whereNotNull('cost')->orderBy('id', "asc")->first();
                    $max = ShippingCost::where('country_id', $item->country_id)->whereNotNull('cost')->orderBy('id', 'desc')->first();

                    $min_cost = number_format(($shippingCost->percentage * $min->cost ?? 1) / 100, 2);
                    $max_cost = number_format(($shippingCost->percentage * $max->cost ?? 1) / 100, 2);
                    return $min_cost . ' - ' . $max_cost;
                }
                return 'N/A';
            })
            ->addColumn('weight', function ($item) {
                $min = ShippingCost::where('country_id', $item->country_id)->whereNotNull('cost')->orderBy('id', "asc")->first();
                $max = ShippingCost::where('country_id', $item->country_id)->whereNotNull('cost')->orderBy('id', 'desc')->first();
                return ($min->weight ?? 0) . ' - ' . ($max->weight ?? 0);
            })
            ->addColumn('action', function ($item) {

                return '<a href="javascript:void(0)" onclick="addShippingCost(' . $item->id . ')" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function countryShippingRatesList($country_id)
    {
        $items = CountryShippingCost::where('country_id', $country_id)->get();
        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('name', function ($item) {
                $country = Country::where('id',$item->city_id)->first();
                return ($country->name ?? 'N/A');
            })
            ->addColumn('shipping_percentage', function ($item) {
                if (isset($item->percentage)) {
                    return $item->percentage . ' %';
                }
                return 'N/A';
            })
            ->addColumn('day', function ($item) {
                
                if (isset($item->day)) {
                    return $item->day;
                }
                return 'N/A';
            })
            ->addColumn('shipping_cost', function ($item) {
                $shippingCost = CountryShippingCost::where('city_id', $item->city_id)->where('country_id', $item->country_id)->first();
                if (isset($shippingCost->percentage) && $shippingCost->percentage > 0) {
                    $min = ShippingCost::where('country_id', $item->country_id)->whereNotNull('cost')->orderBy('id', "asc")->first();
                    $max = ShippingCost::where('country_id', $item->country_id)->whereNotNull('cost')->orderBy('id', 'desc')->first();

                    $min_cost = number_format(($shippingCost->percentage * $min->cost ?? 1) / 100, 2);
                    $max_cost = number_format(($shippingCost->percentage * $max->cost ?? 1) / 100, 2);
                    return $min_cost . ' - ' . $max_cost;
                }
                return 'N/A';
            })
            ->addColumn('weight', function ($item) {
                $min = ShippingCost::where('country_id', $item->country_id)->whereNotNull('cost')->orderBy('id', "asc")->first();
                $max = ShippingCost::where('country_id', $item->country_id)->whereNotNull('cost')->orderBy('id', 'desc')->first();
                return ($min->weight ?? 0) . ' - ' . ($max->weight ?? 0);
            })
            ->addColumn('action', function ($item) {

                return '<a href="javascript:void(0)" onclick="addShippingCost(' . $item->id . ')" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }




    public function weightShippingRatesList($country_id)
    {
        $country = Country::find($country_id);
        $items = ShippingCost::where('country_iso_2', $country->iso2)->get();

        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('name', function ($item) {
                return ($item->country_name ?? 'N/A');
            })
            ->addColumn('cost', function ($item) {
                return ($item->cost ?? 'N/A');
            })
            ->addColumn('weight', function ($item) {
                return ($item->weight ?? 'N/A');
            })
            ->addColumn('action', function ($item) {

                return '<a href="javascript:void(0)" onclick="addWeightCost(' . $item->id . ')" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> Edit Cost</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }



    public function getCityShippingCost($city_id)
    {
        $city = city::find($city_id);
        if ($city) {
            $shippingCost = CityShippingCost::where('city_id', $city_id)->where('country_id', $city->country_id)->first();

            return response()->json([
                'success' => true,
                'city_name' => $shippingCost->city->name ?? null,
                'shipping_percentage' => $shippingCost->percentage ?? 0
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'City not found'
            ]);
        }
    }

    public function getWeightShippingCost($shipping_cost_id)
    {
        $shippingCost = ShippingCost::find($shipping_cost_id);
        if ($shippingCost) {
            $shippingCost = ShippingCost::where('id', $shipping_cost_id)->first();
            return response()->json([
                'success' => true,
                'shippingCost' => $shippingCost->cost ?? 0,
                'shippingId' => $shippingCost->id ?? 0

            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Shiping Address not found'
            ]);
        }
    }



    public function saveCityShippingCostPercentage(Request $request, $id)
    {

        $request->validate([
            'percentage' => 'required|numeric|min:0|max:100',
        ]);
        return DB::transaction(function () use ($id, $request) {
            $country = Country::find($id);

            if ($request->has('multiple_cities')) {
                $city_ids = $request->cities;
                if (in_array('all', $city_ids)) {
                    $cities = City::where('country_id', $id)->pluck('id')->toArray();
                } else {
                    $cities = City::whereIn('id', $city_ids)->where('country_id', $id)->pluck('id')->toArray();
                }

                foreach ($cities as $city_id) {
                    $shippingCost = new CityShippingCost();
                    $shippingCost->updateOrCreate([
                        'country_id' => $country->id,
                        'city_id' => $city_id,
                    ], [
                        'country_id' => $country->id,
                        'city_id' => $city_id,
                        'percentage' => $request->percentage,
                        'day' => $request->day,
                    ]);
                }

                return back()->with(['success' => 'Data Saved Successfully !']);
            } else {
                $city_id = (int)$request->city_id;
                if ($request->city_id) {
                    $shippingCost = new CityShippingCost();

                    $shippingCost->updateOrCreate([
                        'country_id' => $country->id,
                        'city_id' => $city_id,
                    ], [
                        'country_id' => $country->id,
                        'city_id' => $city_id,
                        'percentage' => $request->percentage,
                        'day' => $request->day,
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Data Saved Successfully'
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'message' => 'Something went wrong'
                    ]);
                    return redirect()->back()->withErrors('If Selecting individual cities, please unselect All Cities options');
                }
            }
        });
    }

    public function saveCountryShippingCostPercentage(Request $request, $id)
    {

        $request->validate([
            'percentage' => 'required|numeric|min:0|max:100',
            'day' => 'required',
        ]);
        // dd($request->all());
        return DB::transaction(function () use ($id, $request) {
            $country = Country::find($id);

            if ($request->has('multiple_cities')) {
                $city_ids = $request->cities;
                if (in_array('all', $city_ids)) {
                    $cities = Country::whereNotIn('id', [$id])->pluck('id')->toArray();
                    // dd(1);
                } else {
                    // dd(2);
                    $cities = Country::whereIn('id', $city_ids)->whereNotIn('id', [$id])->pluck('id')->toArray();
                }
                // dd($cities);

                foreach ($cities as $city_id) {
                    $shippingCost = new CountryShippingCost();
                    $shippingCost->updateOrCreate([
                        'country_id' => $country->id,
                        'city_id' => $city_id,
                    ], [
                        'country_id' => $country->id,
                        'city_id' => $city_id,
                        'percentage' => $request->percentage,
                        'day' => $request->day,
                    ]);
                }

                return back()->with(['success' => 'Data Saved Successfully !']);
            } else {
                $city_id = (int)$request->city_id;
                if ($request->city_id) {
                    $shippingCost = new CountryShippingCost();

                    $shippingCost->updateOrCreate([
                        'country_id' => $country->id,
                        'city_id' => $city_id,
                    ], [
                        'country_id' => $country->id,
                        'city_id' => $city_id,
                        'percentage' => $request->percentage,
                        'day' => $request->day,
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Data Saved Successfully'
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'message' => 'Something went wrong'
                    ]);
                    return redirect()->back()->withErrors('If Selecting individual cities, please unselect All Cities options');
                }
            }
        });
    }

    public function updateWeightShippingCost(Request $request)
    {

        try {
            $request->validate([
                'cost' => 'required|numeric|min:0',
            ]);
            ShippingCost::where('id', $request->shipping_id)->update([
                'cost' => $request->cost,
            ]);

            return back()->with(['success' => 'Cost updated Successfully !']);
        } catch (Exception $e) {
            return redirect()->back()->withErrors('Something went wrong in updating cost');
        }
    }
}
