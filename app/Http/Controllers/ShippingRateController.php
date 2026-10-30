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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Exception;
use App\Services\Shipping\DHLExpressService;

//clean cache
use Illuminate\Support\Facades\File;

class ShippingRateController extends Controller
{

    protected $dhl;

    public function __construct(DHLExpressService $dhl)
    {
        $this->dhl = $dhl;
        AdminController::canDispatch();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.settings.shipping_rates.index', [
            'locationSearchUrl' => route('shipping-rates.cities.search'),
        ]);
    }

    /**
     * ajax function for locationList
     */
    public function shippingRatesList()
    {
        $items = CityShippingCost::query()
            ->with(['originCity.country', 'destinationCity.country'])
            ->whereNotNull('origin_city_id')
            ->whereNotNull('destination_city_id');

        return DataTables::of($items)
        ->addIndexColumn()
        ->filter(function ($query) {
            if (request()->has('search')) {
                $search = request('search')['value'];
                $query->where(function ($query) use ($search) {
                    $query->whereHas('originCity', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    })->orWhereHas('destinationCity', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    })->orWhere('weight', 'like', "%{$search}%")
                        ->orWhere('weight_range', 'like', "%{$search}%")
                        ->orWhere('freight_mode', 'like', "%{$search}%")
                        ->orWhere('cost', 'like', "%{$search}%");
                });
            }
        })
        ->addColumn('origin', function ($item) {
            return $this->cityLabel($item->originCity);
        })
        ->addColumn('destination', function ($item) {
            return $this->cityLabel($item->destinationCity);
        })
        ->addColumn('weight_display', function ($item) {
            return $item->weight_range ?: ($item->weight ? $item->weight . ' kg' : 'Any weight');
        })
        ->addColumn('shipping_cost', function ($item) {
            return $item->cost !== null ? number_format((float) $item->cost, 2) : '0.00';
        })
        ->addColumn('documents', function ($item) {
            return collect([
                'Invoice' => $item->invoice_document,
                'Packing List' => $item->packing_list_document,
                'ID' => $item->id_document,
                'Permit' => $item->permit_document,
            ])->filter()->map(function ($path, $label) {
                return '<a href="' . asset($path) . '" target="_blank" class="badge bg-secondary me-1">' . e($label) . '</a>';
            })->implode(' ') ?: '<span class="text-muted">None</span>';
        })
        ->addColumn('action', function ($item) {
            $editUrl = route('shipping-location-rates.edit', $item->id);
            $deleteUrl = route('shipping-location-rates.destroy', $item->id);
            return '
            <a href="' . $editUrl . '" class="btn btn-info btn-sm"><i class="fa fa-pen me-1"></i>Edit</a>
            <form action="' . $deleteUrl . '" method="POST" class="d-inline-block" onsubmit="return confirm(\'Delete this shipping rate?\')">
                ' . csrf_field() . method_field('DELETE') . '
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash me-1"></i>Delete</button>
            </form>';
        })
        ->rawColumns(['action', 'shipping_cost', 'documents'])
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
        $totalWeight = (float) $request->weightTotal;
        $freightMode = $request->input('freight_mode', $request->input('mode'));
        $freightMode = $freightMode ? ucfirst(strtolower($freightMode)) : null;
        $locationRatesOnly = $request->boolean('location_rates_only');
        // return $shipFromCountryId;
        $shippingCostPrice = 0;
        $day = 'No delivery day';
        $percentage = 1;

        // ---------- day calculation 
        if (!$locationRatesOnly && $shipFromCountryId == $shipToCountryId) {
            if (CityShippingCost::where('country_id', $shipFromCountryId)->exists()) {
                $day = CityShippingCost::where('country_id', $shipFromCountryId)->first()->day;
            }
        } elseif (!$locationRatesOnly) {
            if (CountryShippingCost::where('country_id', $shipFromCountryId)->where('city_id', $shipToCountryId)->exists()) {
                $day = CountryShippingCost::where('country_id', $shipFromCountryId)->where('city_id', $shipToCountryId)->first()->day;
                $percentage = CountryShippingCost::where('country_id', $shipFromCountryId)->where('city_id', $shipToCountryId)->first()->percentage;
            }
        }
        
        // ---------- day calculation 


        $locationShippingCost = $this->locationShippingCost($shipFromCityId, $shipToCityId, $totalWeight, $freightMode);

        if ($locationShippingCost) {
            $day = $locationShippingCost->day ?: $day;
            $shippingCostPrice = $this->locationShippingCostPrice($locationShippingCost, $totalWeight);
        } else {
            if ($locationRatesOnly) {
                return response()->json([
                    'success' => false,
                    "message" => "First define the location shipping rate for these cities and weight",
                ]);
            }

            $shippingCost = ShippingCost::where('country_id', $shipFromCountryId)->where('weight', $totalWeight)->first();

            if (! $shippingCost) {
                return response()->json([
                    'success' => false,
                    "message" => "First define the shipping cost for this location and weight",
                ]);
            }

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

        /* get dhl shipping rates */
        $resp = [];
        $resp[] = [
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
                    'total' => (float)  $shippingCostPrice,
                    'carrier' => 'siopay'
                    ];


        $shipperDetails = [
                    'postalCode' => $request->ship_from_postal_code,
                    'cityName' => City::find($shipFromCityId)->name,
                    'countryCode' => Country::find($shipFromCountryId)->iso2,
        ];
        $receiverDetails = [
                    'postalCode' => $request->ship_to_postal_code,
                    'cityName' => City::find($shipToCityId)->name,
                    'countryCode' => Country::find($shipToCountryId)->iso2,
        ];
        $items = $request->input('items', []);

        $packages = [
                    'total_weight' => $request->weightTotal,
                    'heightTotal' => $request->heightTotal,
                    'widthTotal' => $request->widthTotal,
                    'lengthTotal' => $request->lengthTotal,
                    'pickupDate' => $request->pickupDate ?? Carbon::now()->nextWeekday()->toDateString(),
                    ];
                    
        $dhlRates = $this->quote($shipperDetails, $receiverDetails, $packages);

        $shipFrom = 'County : ' . Country::find($shipFromCountryId)->name
                . '<br>, City : ' . City::find($shipFromCityId)->name;

        $shipTo   = 'County : ' . Country::find($shipToCountryId)->name
                . '<br>, City : ' . City::find($shipToCityId)->name;

        if($dhlRates['rates'] != null && count($dhlRates['rates'])>0) {
            foreach ($dhlRates['rates']['products'] as $rate) {

                $day = (int) ($rate['deliveryCapabilities']['totalTransitDays'] ?? 0);
                // Shipping price (BASEC)
                $shippingCostPrice = 0;
                foreach ($rate['totalPrice'] as $priceRow) {
                    if ($priceRow['currencyType'] === 'BASEC') {
                        $shippingCostPrice = (float) $priceRow['price'];
                        if($priceRow['price'] > 0) {
                            $priceCurrency = $priceRow['priceCurrency'];
                            $displayPrice = sprintf('%0.2f %s', $shippingCostPrice, $priceCurrency);
                        }
                        break;
                    }
                }

                if((float) $shippingCostPrice > 0){   
                    $resp[] = [
                        'service_name'   => $rate['productName']. ' (' . $rate['productCode'] . ')',
                        'day'            => $day,
                        'shipping_cost'  => $displayPrice,
                        'ship_from'      => $shipFrom,
                        'ship_to'        => $shipTo,
                        'total_weight'   => $request->weightTotal,
                        'heightTotal'    => $request->heightTotal,
                        'widthTotal'     => $request->widthTotal,
                        'lengthTotal'    => $request->lengthTotal,
                        'valueTotal'     => $request->valueTotal,
                        'countTotal'     => $request->countTotal,
                        'total'          => $displayPrice,
                        'carrier'        => $rate['productName']. ' (' . $rate['productCode'] . ')' . ' - DHL'
                    ];
                }
            }
        }
        
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

    public function createLocationShippingRate()
    {
        return view('admin.settings.shipping_rates.location_form', [
            'rate' => new CityShippingCost(),
            'action' => route('shipping-location-rates.store'),
            'method' => 'POST',
            'buttonText' => 'Save Location Rate',
            'locationSearchUrl' => route('shipping-rates.cities.search'),
            'selectedOriginLocation' => $this->selectedCityLocation(session()->getOldInput('origin_city_id')),
            'selectedDestinationLocation' => $this->selectedCityLocation(session()->getOldInput('destination_city_id')),
            'weightRanges' => $this->weightRanges(),
        ]);
    }

    public function storeLocationShippingRate(Request $request)
    {
        $data = $this->validatedLocationShippingRate($request);
        $originCity = City::findOrFail($data['origin_city_id']);
        $destinationCity = City::findOrFail($data['destination_city_id']);

        try {
            $data['distance_km'] = $this->calculateCityDistance($originCity, $destinationCity)['distance_km'];
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['distance_km' => $e->getMessage()]);
        }

        $data['country_id'] = $originCity->country_id;
        $data['city_id'] = $data['origin_city_id'];
        $data['percentage'] = null;
        $data = array_merge($data, $this->storeLocationRateDocuments($request));
        CityShippingCost::create($data);

        return redirect()
            ->route('shipping_rates.index')
            ->with(['message' => 'Location shipping rate saved successfully.', 'message_type' => 'success']);
    }

    public function editLocationShippingRate(CityShippingCost $cityShippingCost)
    {
        return view('admin.settings.shipping_rates.location_form', [
            'rate' => $cityShippingCost,
            'action' => route('shipping-location-rates.update', $cityShippingCost->id),
            'method' => 'PUT',
            'buttonText' => 'Update Location Rate',
            'locationSearchUrl' => route('shipping-rates.cities.search'),
            'selectedOriginLocation' => $this->selectedCityLocation(session()->getOldInput('origin_city_id', $cityShippingCost->origin_city_id)),
            'selectedDestinationLocation' => $this->selectedCityLocation(session()->getOldInput('destination_city_id', $cityShippingCost->destination_city_id)),
            'weightRanges' => $this->weightRanges(),
        ]);
    }

    public function updateLocationShippingRate(Request $request, CityShippingCost $cityShippingCost)
    {
        $data = $this->validatedLocationShippingRate($request, $cityShippingCost);
        $originCity = City::findOrFail($data['origin_city_id']);
        $destinationCity = City::findOrFail($data['destination_city_id']);

        try {
            $data['distance_km'] = $this->calculateCityDistance($originCity, $destinationCity)['distance_km'];
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['distance_km' => $e->getMessage()]);
        }

        $data['country_id'] = $originCity->country_id;
        $data['city_id'] = $data['origin_city_id'];
        $data['percentage'] = null;
        $data = array_merge($data, $this->updateLocationRateDocuments($request, $cityShippingCost));
        $cityShippingCost->update($data);

        return redirect()
            ->route('shipping_rates.index')
            ->with(['message' => 'Location shipping rate updated successfully.', 'message_type' => 'success']);
    }

    public function destroyLocationShippingRate(CityShippingCost $cityShippingCost)
    {
        foreach ($this->locationRateDocumentFields() as $field) {
            $this->deleteLocationRateDocument($cityShippingCost->{$field});
        }

        $cityShippingCost->delete();

        return redirect()
            ->route('shipping_rates.index')
            ->with(['message' => 'Location shipping rate deleted successfully.', 'message_type' => 'success']);
    }

    public function searchCities(Request $request)
    {
        $term = trim($request->get('q', ''));

        $query = City::query()
            ->with('country:id,name')
            ->select('id', 'name', 'country_id');

        if ($term) {
            $query->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhereHas('country', function ($query) use ($term) {
                        $query->where('name', 'like', "%{$term}%");
                    });
            });
        }

        $results = $query
            ->orderBy('name')
            ->limit(25)
            ->get()
            ->map(function (City $city) {
                return [
                    'id' => $city->id,
                    'text' => $this->cityLabel($city),
                ];
            });

        return response()->json(['results' => $results]);
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

    protected function validatedLocationShippingRate(Request $request, ?CityShippingCost $rate = null): array
    {
        $data = $request->validate([
            'origin_city_id' => ['required', 'integer', Rule::exists('cities', 'id')],
            'destination_city_id' => ['required', 'integer', Rule::exists('cities', 'id')],
            'freight_mode' => ['required', 'string', Rule::in(['Air', 'Sea', 'Road'])],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'weight_range' => ['nullable', Rule::in(array_keys($this->weightRanges()))],
            // Cost is now entered per currency; at least one is required.
            'cost_eur' => ['nullable', 'required_without:cost_naira', 'numeric', 'min:0'],
            'cost_naira' => ['nullable', 'required_without:cost_eur', 'numeric', 'min:0'],
            'day' => ['nullable', 'string', 'max:120'],
            'rate_per_km_per_kg' => ['nullable', 'numeric', 'min:0'],
            'invoice_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
            'packing_list_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
            'id_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
            'permit_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
        ]);

        // Keep the legacy `cost` column populated (EUR preferred, else NGN) for existing pricing logic.
        $data['cost'] = $data['cost_eur'] ?? $data['cost_naira'] ?? 0;

        return $data;
    }

    protected function storeLocationRateDocuments(Request $request): array
    {
        $documents = [];
        $directory = public_path('uploads/shipping-location-rates');

        File::ensureDirectoryExists($directory);

        foreach ($this->locationRateDocumentFields() as $field) {
            if (! $request->hasFile($field)) {
                continue;
            }

            $file = $request->file($field);
            $originalName = $file->getClientOriginalName();
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'file';
            $fileName = time() . '_' . Str::random(8) . '_' . Str::slug($field . '-' . $baseName) . '.' . $extension;

            $file->move($directory, $fileName);

            $documents[$field] = 'uploads/shipping-location-rates/' . $fileName;
        }

        return $documents;
    }

    protected function updateLocationRateDocuments(Request $request, CityShippingCost $rate): array
    {
        $documents = [];

        foreach ($this->storeLocationRateDocuments($request) as $field => $path) {
            $this->deleteLocationRateDocument($rate->{$field});
            $documents[$field] = $path;
        }

        return $documents;
    }

    protected function deleteLocationRateDocument(?string $path): void
    {
        if (! $path || ! Str::startsWith($path, 'uploads/shipping-location-rates/')) {
            return;
        }

        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    protected function locationRateDocumentFields(): array
    {
        return [
            'invoice_document',
            'packing_list_document',
            'id_document',
            'permit_document',
        ];
    }

    protected function calculateCityDistance(City $originCity, City $destinationCity): array
    {
        if ($originCity->id === $destinationCity->id) {
            return [
                'distance_km' => 0,
                'duration_minutes' => 0,
            ];
        }

        if (! $originCity->latitude || ! $originCity->longitude || ! $destinationCity->latitude || ! $destinationCity->longitude) {
            throw new Exception('Selected cities must have latitude and longitude before distance can be calculated.');
        }

        $apiKey = env('OPENROUTE_API_KEY');

        if (! $apiKey) {
            throw new Exception('OPENROUTE_API_KEY is not configured.');
        }

        $response = Http::withHeaders([
            'Authorization' => $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(20)->post(
            'https://api.openrouteservice.org/v2/directions/driving-car',
            [
                'coordinates' => [
                    [(float) $originCity->longitude, (float) $originCity->latitude],
                    [(float) $destinationCity->longitude, (float) $destinationCity->latitude],
                ],
            ]
        );

        if (! $response->successful()) {
            throw new Exception('Distance calculation failed. Please check the selected cities and OpenRouteService settings.');
        }

        $summary = $response->json('routes.0.summary');

        if (! isset($summary['distance'])) {
            throw new Exception('Distance calculation did not return a route for the selected cities.');
        }

        return [
            'distance_km' => round(((float) $summary['distance']) / 1000, 2),
            'duration_minutes' => isset($summary['duration']) ? round(((float) $summary['duration']) / 60, 2) : null,
        ];
    }

    protected function locationShippingCost($originCityId, $destinationCityId, float $weight, ?string $freightMode = null): ?CityShippingCost
    {
        if (! Schema::hasColumn('city_shipping_costs', 'origin_city_id')) {
            return null;
        }

        $query = CityShippingCost::query()
            ->where('origin_city_id', $originCityId)
            ->where('destination_city_id', $destinationCityId)
            ->where(function ($query) use ($weight) {
                $query->whereNull('weight')
                    ->orWhere('weight', '>=', $weight);
            });

        if ($freightMode && Schema::hasColumn('city_shipping_costs', 'freight_mode')) {
            $query->where('freight_mode', $freightMode);
        }

        return $query
            ->orderByRaw('CASE WHEN weight IS NULL THEN 1 ELSE 0 END')
            ->orderBy('weight')
            ->first();
    }

    protected function locationShippingCostPrice(CityShippingCost $rate, float $weight): float
    {
        if ($rate->distance_km && $rate->rate_per_km_per_kg) {
            return round((float) $rate->distance_km * (float) $rate->rate_per_km_per_kg * $weight, 2);
        }

        return round((float) $rate->cost, 2);
    }

    protected function selectedCityLocation($cityId): ?object
    {
        if (! $cityId) {
            return null;
        }

        $city = City::with('country:id,name')->find($cityId);

        if (! $city) {
            return null;
        }

        return (object) [
            'id' => $city->id,
            'display_label' => $this->cityLabel($city),
        ];
    }

    protected function cityLabel(?City $city): string
    {
        if (! $city) {
            return 'N/A';
        }

        return $city->name . ($city->country ? ', ' . $city->country->name : '');
    }

    protected function weightRanges(): array
    {
        $ranges = [];

        for ($i = 1; $i <= 50; $i++) {
            $ranges[(string) $i] = $i . ' kg';
        }

        return $ranges;
    }

    /**
     * Get DHL Quote
     * @param array $shipperDetails
     * @param array $receiverDetails
     * @param array $packages
     * @return array
     */
    public function quote($shipperDetails, $receiverDetails, $packages)
    {
        try {
            $rates = $this->dhl->getRates([
                'customerDetails' => [
                    'shipperDetails' => $shipperDetails,
                    'receiverDetails' => $receiverDetails,
                ],
                'packages' => [
                    'total' => $packages
                ],
            ]);

            if (empty($rates) || !isset($rates['products']) || count($rates['products']) === 0) {
                return [
                    'success' => false,
                    'message' => 'No shipping rates are available for the selected address and package details.',
                    'rates'   => []
                ];
            }

            return [
                'success' => true,
                'message' => 'Shipping rates retrieved successfully.',
                'rates'   => $rates
            ];
        
       } catch (Exception $e) {
           Log::error('DHL Quote Error: ' . $e->getMessage(), ['exception' => $e]);
           return [
               'success' => false,
               'message' => 'An error occurred while retrieving shipping rates.',
               'rates' => []
           ];
       }
    }
}
