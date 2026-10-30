<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Order;
use App\Models\WalkInCustomer;
use App\Models\OrderPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use App\Mail\PaymentEmail;
use App\Models\City;
use App\Models\Country;
use App\Models\ShippingRate;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class WalkInCustomerOrder extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dispatcher.orders.index');
    }

    /**
     * datatable for agent orders
     */
    public function myOrdersList()
    {
        $orders = Order::where('customer_id', Auth::user()->customer->id)->orderBy('created_at', 'DESC')->get();

        return Datatables::of($orders)
            ->addIndexColumn()
            ->addColumn('status', function ($order) {
                $mar = "<span class= 'badge bg-secondary'>" . $order->status . "</span>";
                return $mar;
            })
            ->addColumn('location', function ($order) {
                $mar = "Origin : " . $order->pickup_location->name . "<br>";
                $mar .= "Destination:" . $order->delivery_location->name;
                return $mar;
            })
            ->addColumn('date', function ($order) {
                $mar = "<b> Tracking ID : " . $order->tracking_id . "</b><br><br>";
                $mar .= "Created at : " . $order->created_at . "<br>";
                $mar .= "Last updated:" . $order->updated_at;
                return $mar;
            })
            ->addColumn('parties', function ($order) {
                $mar = "Sender : " . $order->pickup_name . "<br>";
                $mar .= "Receiver:" . $order->delivery_name;
                return $mar;
            })
            ->addColumn('edit', function ($order) {
                if ($order->status == 'unpaid') {
                    $edit_url = route('orders.edit', $order->id);
                    return '<a href="' . $edit_url . '" class="btn btn-info btn-sm" ><i class="fa fa-pencil"></i> Edit</a>';
                } else {
                    $edit_url = '#';
                    return '<a href="' . $edit_url . '" class="btn btn-info btn-sm disabled" ><i class="fa fa-pencil"></i> Edit</a>';
                }
            })
            ->addColumn('price', function ($order) {
                if ($order->status == 'unpaid') {
                    $mar = $order->shipping_rate->price . "<br>";
                    $url = route('payment.summary', ['order_id' => $order->id]);
                    $mar .= '<a href="' . $url . '" class="btn btn-info " >$ Pay</a>';
                    return $mar;
                } else {
                    $mar = $order->shipping_rate->price . "<br>";
                    return $mar;
                }
            })
            ->addColumn('view', function ($order) {
                $url = route('orders.show', $order->id);
                return '<a href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View/ Track</a>';
            })
            // ->addColumn('delete', function ($order) {
            //     $url = route('shipping_rates.destroy', $order->id);
            //     return '<form method="POST" action="' . $url . '">
            //                 <input type="hidden" name = "_token" value = ' . csrf_token() . '>
            //                 <input type="hidden" name = "_method" value ="DELETE">
            //                 <button type="submit" onclick="return confirm(\'Are you sure you wish to delete this entry?\')" class="btn btn-sm btn-danger">Delete</button>
            //             </form>';
            // })
            ->rawColumns(['status', 'location', 'edit', 'view', 'parties', 'date', 'price'])
            ->make(true);
    }

    public function allOrders()
    {
        return view('admin.orders.index');
    }

    /**
     * datatable for all orders
     */
    public function allOrdersList()
    {
        $orders = Order::orderBy('created_at', 'DESC')->get();

        return Datatables::of($orders)
            ->addIndexColumn()
            ->addColumn('location', function ($order) {
                $current  = Country::where('id', $order->current_location_country_id)->first()->name .", ". City::find($order->current_location_city_id)->name;
                $destination = Country::where('id', $order->delivery_location_country_id)->first()->name .", ". City::find($order->delivery_location_city_id)->name;
                $origin = Country::where('id', $order->pickup_location_country_id)->first()->name .", ". City::find($order->pickup_location_city_id)->name;
                $mar = "Origin : " . $origin . "<br>";
                $mar .= "Destination:" . $destination . "<br>";
                $mar .= "Current : ". $current;
                return $mar;
            })
            
            
            ->addColumn('date', function ($order) {
                $mar = "<b> Tracking ID : " . $order->tracking_id . "</b><br><br>";
                $mar .= "Created at : " . $order->created_at . "<br>";
                $mar .= "Last updated:" . $order->updated_at;
                return $mar;
            })
            ->addColumn('parties', function ($order) {
                $mar = "Sender : " . $order->pickup_name . "<br>";
                $mar .= "Receiver:" . $order->delivery_name;
                return $mar;
            })
            // ->addColumn('edit', function ($order) {
            //     if ($order->status == 'unpaid' || $order->status == 'placed') {
            //         $edit_url = route('orders.edit', $order->id);
            //         return '<a href="' . $edit_url . '" class="btn btn-info btn-sm" ><i class="fa fa-pencil"></i> Edit</a>';
            //     } else {
            //         $edit_url = '#';
            //         return '<a href="' . $edit_url . '" class="btn btn-info btn-sm disabled" ><i class="fa fa-pencil"></i> Edit</a>';
            //     }
            // })
            ->addColumn('price', function ($order) {
                $mar = $order->shipping_rate->price . "<br>";
                return $mar;
            })
            ->addColumn('view', function ($order) {
                $url = route('orders.show', $order->id);
                return '<a href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View/ Track</a>';
            })
            // ->addColumn('delete', function ($order) {
            //     $url = route('shipping_rates.destroy', $order->id);
            //     return '<form method="POST" action="' . $url . '">
            //                 <input type="hidden" name = "_token" value = ' . csrf_token() . '>
            //                 <input type="hidden" name = "_method" value ="DELETE">
            //                 <button type="submit" onclick="return confirm(\'Are you sure you wish to delete this entry?\')" class="btn btn-sm btn-danger">Delete</button>
            //             </form>';
            // })
            ->rawColumns(['status', 'location', 'edit', 'view', 'parties', 'date', 'price','agentInfo'])
            ->make(true);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $locations = Location::all();
        return view('dispatcher.orders.new', ['locations' => $locations]);
    }

    public static $supported_apis = ['FEDEX'];

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'tax_code_' => 'required',
            'surname_' => 'required',
            'name_' => 'required',
            'gender_' => 'required',
            'dob_' => 'required|date',
            'doc_type_' => 'nullable',
            'doc_num_' => 'nullable',
            'doc_front_' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'doc_back_' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'origin_id' => 'required|numeric|exists:locations,id',
            'dest_id' => 'required|numeric|exists:locations,id',
            'rate' => 'required|numeric|exists:shipping_rates,id',
            'rx_name' => 'required|string',
            'rx_phone' => 'required|string',
            'rx_email' => 'required|string|email',
            'rx_phone_alt' => 'nullable|string',
            'rx_address1' => 'required|string',
            'rx_address2' => 'nullable|string',
            'rx_zip' => 'required|string',
            'rx_city' => 'required|string',
            'rx_state' => 'required|string',
            'rx_country' => 'required|string',
            's_name' => 'required|string',
            's_phone' => 'required|string',
            's_email' => 'required|string|email',
            's_phone_alt' => 'nullable|string',
            's_address1' => 'required|string',
            's_address2' => 'nullable|string',
            's_zip' => 'required|string',
            's_city' => 'required|string',
            's_state' => 'required|string',
            's_country' => 'required|string',
            'r_date' => 'required|string',
            'cond_of_goods' => 'nullable|string',
            'val_of_goods' => 'nullable|string',
            'val_cur' => 'nullable|string',
            'type' => 'array',
            'len' => 'array',
            'width' => 'array',
            'height' => 'array',
            'weight' => 'array',
            'count' => 'array',
            'item_desc' => 'array',
            'item_value' => 'array',
            'type.*' => 'required|string',
            'len.*' => 'required',
            'width.*' => 'required',
            'height.*' => 'required',
            'weight.*' => 'required',
            'count.*' => 'required',
            'item_desc.*' => 'required',
            'item_value.*' => 'required',
            'terms_of_sale' => 'nullable',
            'customs_inv_num' => 'nullable'
        ]);
        try {
            DB::beginTransaction();
            $cus = WalkInCustomer::where('tax_code', $request->tax_code_)->first();
            // dd($);
            if (!$cus) {
                //upload file
                if ($request->hasFile('doc_front_') && $request->hasFile('doc_back_')) {
                    $docFront = $request->file('doc_front_');
                    $docBack = $request->file('doc_back_');

                    // Generate unique filenames for the uploaded files
                    $docFrontFileName = 'doc_front_' . time() . '.' . $docFront->getClientOriginalExtension();
                    $docBackFileName = 'doc_back_' . time() . '.' . $docBack->getClientOriginalExtension();

                    // Move the uploaded files to the public directory
                    $docFront->move(public_path('uploads'), $docFrontFileName);
                    $docBack->move(public_path('uploads'), $docBackFileName);

                    $cus = WalkInCustomer::create([
                        'surname' => $request->surname_,
                        'name' => $request->name_,
                        'birthDate' => $request->dob_,
                        'gender' => $request->gender_,
                        'doc_type' => $request->doc_type_,
                        'doc_num' => $request->doc_num_,
                        'tax_code' => $request->tax_code_,
                        'doc_front' => $docFrontFileName,
                        'doc_back' => $docBackFileName
                    ]);
                } else {
                    $cus = WalkInCustomer::create([
                        'surname' => $request->surname_,
                        'name' => $request->name_,
                        'birthDate' => $request->dob_,
                        'gender' => $request->gender_,
                        'doc_type' => $request->doc_type_,
                        'doc_num' => $request->doc_num_,
                        'tax_code' => $request->tax_code_
                    ]);
                }
            } else {
                //upload file
                if ($request->hasFile('doc_front_') && $request->hasFile('doc_back_')) {
                    $docFront = $request->file('doc_front_');
                    $docBack = $request->file('doc_back_');

                    // Generate unique filenames for the uploaded files
                    $docFrontFileName = 'doc_front_' . time() . '.' . $docFront->getClientOriginalExtension();
                    $docBackFileName = 'doc_back_' . time() . '.' . $docBack->getClientOriginalExtension();

                    // Move the uploaded files to the public directory
                    $docFront->move(public_path('uploads'), $docFrontFileName);
                    $docBack->move(public_path('uploads'), $docBackFileName);

                    $cus = WalkInCustomer::where('tax_code', $request->tax_code_)->update([
                        'surname' => $request->surname_,
                        'name' => $request->name_,
                        'birthDate' => $request->dob_,
                        'gender' => $request->gender_,
                        'doc_type' => $request->doc_type_,
                        'doc_num' => $request->doc_num_,
                        'tax_code' => $request->tax_code_,
                        'doc_front' => $docFrontFileName,
                        'doc_back' => $docBackFileName
                    ]);
                } else {
                    $cus = WalkInCustomer::where('tax_code', $request->tax_code_)->update([
                        'surname' => $request->surname_,
                        'name' => $request->name_,
                        'birthDate' => $request->dob_,
                        'gender' => $request->gender_,
                        'doc_type' => $request->doc_type_,
                        'doc_num' => $request->doc_num_,
                        'tax_code' => $request->tax_code_
                    ]);
                }
            }
            $l = new Order;
            $l->customer_id = Auth::id(); //id of the Agent
            $l->walk_in_customer_id = $cus->id;
            $l->pickup_location_id = $request->origin_id;
            $l->delivery_location_id = $request->dest_id;
            $l->current_location_id = $request->origin_id;
            $l->shipping_rate_id = ((!in_array($request->rate, self::$supported_apis) ? $request->rate : null));
            $l->tracking_id = 'SIO' . time();
            $l->delivery_name = $request->rx_name;
            $l->delivery_phone = $request->rx_phone;
            $l->delivery_email = $request->rx_email;
            $l->delivery_phone_alt = $request->rx_phone_alt ?? null;
            $l->delivery_address1 = $request->rx_address1;
            $l->delivery_address2 = $request->rx_address2 ?? null;
            $l->delivery_zip = $request->rx_zip;
            $l->delivery_city = $request->rx_city;
            $l->delivery_state = $request->rx_state;
            $l->delivery_country = $request->rx_country;
            $l->pickup_name = $request->s_name;
            $l->pickup_phone = $request->s_phone;
            $l->pickup_email = $request->s_email;
            $l->pickup_phone_alt = $request->s_phone_alt ?? null;
            $l->pickup_address1 = $request->s_address1;
            $l->pickup_address2 = $request->s_address2 ?? null;
            $l->pickup_zip = $request->s_zip;
            $l->pickup_city = $request->s_city;
            $l->pickup_state = $request->s_state;
            $l->pickup_country = $request->s_country;
            $l->return_date = $request->r_date;
            $l->cond_of_goods = $request->cond_of_goods;
            $l->val_of_goods = $request->val_of_goods;
            $l->val_cur = $request->val_cur;
            $l->provider = ((!in_array($request->rate, self::$supported_apis) ? $request->rate : 'SIOPAY'));
            $l->terms_of_sale = $request->terms_of_sale;
            $l->customs_inv_num = $request->customs_inv_num;
            $l->save();
            for ($o = 0; $o < count($request->type); $o++) {
                $item = new OrderPackage;

                $item->order_id = $l->id;
                $item->type = $request->type[$o];
                $item->length = $request->len[$o];
                $item->width = $request->width[$o];
                $item->height = $request->height[$o];
                $item->weight = $request->weight[$o];
                $item->qty = $request->count[$o];
                $item->item_desc = $request->item_desc[$o];
                $item->item_value = $request->item_value[$o];
                $item->save();
            }

            // if ($request->rate == 'FEDEX') {
            //     //prepare fedex data
            //     $o = Location::find($request->origin_id);
            //     $d = Location::find($request->dest_id);
            //     $commodities = [];


            //     for ($i = 0; $i < count($request->count); $i++) {
            //         $r = [
            //             'description' => $request->item_desc[$i],
            //             'numberOfPieces' => $request->count[$i],
            //             'weight' => [
            //                 'value' => $request->weight[$i],
            //                 'units' => 'KG'
            //             ],
            //             'quantity' => $request->count[$i],
            //             'quantityUnits' => 'PCS',
            //             'unitPrice' => [
            //                 'amount' => $request->item_value[$i],
            //                 'currency' => 'EUR'
            //             ],
            //             'customsValue' => [
            //                 'amount' => $request->item_value[$i],
            //                 'currency' => 'EUR'
            //             ]
            //         ];

            //         array_push($commodities, $r);
            //     }

            //     $fedExData = [
            //         'shipper_city' => $request->s_city,
            //         'recipient_city' => $request->rx_city,
            //         'shipper_zip' => $request->s_zip,
            //         'recipient_zip' => $request->rx_zip,
            //         'shipper_country_code' => $request->s_country,
            //         'recipient_country_code' => $request->rx_country,
            //         'shipper_address1' => $request->s_address1,
            //         'shipper_address2' => $request->s_address2 ?? '',
            //         'recipient_address1' => $request->rx_address1,
            //         'recipient_address2' => $request->rx_address2,
            //         'recipient_phone' => $request->rx_phone,
            //         'recipient_name' => $request->rx_name,
            //         'recipient_email' => $request->rx_email,
            //         'shipper_phone' => $request->s_phone,
            //         'shipper_name' => $request->s_name,
            //         'shipper_email' => $request->s_email,
            //         'customs_note' => $request->cond_of_goods,
            //         'customs_terms_of_sale' => $request->terms_of_sale,
            //         'commodities' => $commodities,
            //         'shipDatestamp' => $request->r_date,
            //         'customs_inv_num' => $request->customs_inv_num,
            //         'requestedPackageLineItems' => [
            //             [
            //                 'weight' => [
            //                     'value' => $request->weight,
            //                     'units' => 'KG'
            //                 ]
            //             ]
            //         ]

            //     ];

            //     $fedExResponse = FedExController::ship($fedExData);
            // }

            DB::commit();
            return redirect()->route('walk_in_customer_order.index')->with(['message' => 'Order saved You can proceed to add it to a batch', 'message_type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    /**
     * api method for external apps to ship packages
     */
    public function external_app_order(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'ref' => 'required',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
            'length' => 'required|numeric',
            'count' => 'required|numeric',
            'item_desc' => 'required',
            'item_value' => 'required|numeric',
            'origin_country' => 'required',
            'origin_zip' => 'required',
            'origin_city' => 'required',
            'origin_name' => 'required',
            'origin_address' => 'required',
            'origin_email' => 'required',
            'pickup_date' => 'required',
            'dest_city' => 'required',
            'dest_zip' => 'required',
            'dest_email' => 'required',
            'dest_phone' => 'required',
            'dest_address' => 'required',
            'dest_address2' => 'nullable',
            'dest_name' => 'required',
            'dest_country' => 'required',
            'dest_state' => 'required',
        ]);

        try {
            DB::beginTransaction();
            $o = Location::where('name', 'LIKE', "%" . $request->origin_city . "%")->where('country_code', $request->origin_country)->first();
            $d = Location::where('name', 'LIKE', "%" . $request->dest_city . "%")->where('country_code', $request->dest_country)->first();
            // dd($d);
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
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Location miasmatch'
                    ],
                    400
                );
            }


            if ($rate) {
                $l = new Order;
                // $l->customer_id = Auth::id();
                // $l->walk_in_customer_id = $cus->id;
                $l->pickup_location_id = $o->id;
                $l->delivery_location_id = $d->id;
                $l->current_location_id = $o->id;
                $l->shipping_rate_id = $rate->id;
                $l->tracking_id = 'SIOSTORE' . time() . '_' . $request->ref;
                $l->delivery_name = $request->dest_name;
                $l->delivery_phone = $request->dest_phone;
                $l->delivery_email = $request->dest_email;
                $l->delivery_address1 = $request->dest_address;
                $l->delivery_address2 = $request->dest_address2 ?? null;
                $l->delivery_zip = $request->dest_zip;
                $l->delivery_city = $request->dest_city;
                $l->delivery_state = $request->dest_state;
                $l->delivery_country = $request->dest_country;
                $l->pickup_name = $request->origin_name;
                $l->pickup_email = $request->origin_email;
                $l->pickup_address1 = $request->origin_address;
                $l->pickup_zip = $request->origin_zip;
                $l->pickup_city = $request->origin_city;
                $l->pickup_country = $request->origin_country;
                $l->return_date = $request->pickup_date;
                $l->provider = ((!in_array($request->rate, self::$supported_apis) ? $rate->id : 'SIOPAY'));
                $l->save();

                $item = new OrderPackage;

                $item->order_id = $l->id;
                $item->type = 'percel';
                $item->length = $request->length;
                $item->width = $request->width;
                $item->height = $request->height;
                $item->weight = $request->weight;
                $item->qty = 1;
                $item->item_desc = $request->item_desc;
                $item->item_value = $request->item_value;
                $item->save();
            } else {
                DB::rollBack();

                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Failed to obtain a shipping rate/slot for this package, try again later or contact spport'
                    ],
                    400
                );
            }

            DB::commit();
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Item posted for shipping successfully',
                    'data' => [
                        'tracking_id' => $l->tracking_id
                    ]
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('An error occured while creatin external shipping order : ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(
                [
                    'status' => false,
                    'message' => 'An Error occured, try again later or contact spport'
                ],
                500
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return view('dispatcher.orders.show', ['order' => $order]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        $dest_str = Location::where('id', $order->delivery_location_id)->first();
        $origin_str = Location::where('id', $order->pickup_location_id)->first();

        $dest_str = $dest_str->postcode . '-' . $dest_str->name . ' [Lat: ' . $dest_str->latitude . ', Long: ' . $dest_str->longitude . ']';
        $origin_str = $origin_str->postcode . '-' . $origin_str->name . ' [Lat: ' . $origin_str->latitude . ', Long: ' . $origin_str->longitude . ']';
        return view('dispatcher.orders.edit', ['order' => $order, 'dest_str' => $dest_str, 'origin_str' => $origin_str]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        // dd($request->all());
        $request->validate([
            'origin_id' => 'required|numeric|exists:locations,id',
            'dest_id' => 'required|numeric|exists:locations,id',
            'rate' => 'required|numeric|exists:shipping_rates,id',
            'rx_name' => 'required|string',
            'rx_phone' => 'required|string',
            'rx_email' => 'required|string',
            'rx_phone_alt' => 'nullable|string',
            'rx_address1' => 'required|string',
            'rx_address2' => 'nullable|string',
            'rx_zip' => 'required|string',
            'rx_city' => 'required|string',
            'rx_state' => 'required|string',
            'rx_country' => 'required|string',
            's_name' => 'required|string',
            's_phone' => 'required|string',
            's_email' => 'required|string',
            's_phone_alt' => 'nullable|string',
            's_address1' => 'required|string',
            's_address2' => 'nullable|string',
            's_zip' => 'required|string',
            's_city' => 'required|string',
            's_state' => 'required|string',
            's_country' => 'required|string',
            'r_date' => 'required|string',
            'cond_of_goods' => 'nullable|string',
            'val_of_goods' => 'nullable|string',
            'val_cur' => 'nullable|string',
            'type' => 'array',
            'len' => 'array',
            'width' => 'array',
            'height' => 'array',
            'weight' => 'array',
            'count' => 'array',
            'type.*' => 'required|string',
            'len.*' => 'required|numeric',
            'width.*' => 'required|numeric',
            'height.*' => 'required|numeric',
            'weight.*' => 'required|numeric',
            'count.*' => 'required|numeric'
        ]);
        try {
            DB::beginTransaction();
            $l = $order;
            $l->customer_id = Auth::id();
            $l->pickup_location_id = $request->origin_id;
            $l->delivery_location_id = $request->dest_id;
            $l->current_location_id = $request->origin_id;
            $l->shipping_rate_id = $request->rate;
            $l->tracking_id = 'SIO' . time();
            $l->delivery_name = $request->rx_name;
            $l->delivery_phone = $request->rx_phone;
            $l->delivery_email = $request->rx_email;
            $l->delivery_phone_alt = $request->rx_phone_alt ?? null;
            $l->delivery_address1 = $request->rx_address1;
            $l->delivery_address2 = $request->rx_address2 ?? null;
            $l->delivery_zip = $request->rx_zip;
            $l->delivery_city = $request->rx_city;
            $l->delivery_state = $request->rx_state;
            $l->delivery_country = $request->rx_country;
            $l->pickup_name = $request->s_name;
            $l->pickup_phone = $request->s_phone;
            $l->pickup_email = $request->s_email;
            $l->pickup_phone_alt = $request->s_phone_alt ?? null;
            $l->pickup_address1 = $request->s_address1;
            $l->pickup_address2 = $request->s_address2 ?? null;
            $l->pickup_zip = $request->s_zip;
            $l->pickup_city = $request->s_city;
            $l->pickup_state = $request->s_state;
            $l->pickup_country = $request->s_country;
            $l->return_date = $request->r_date;
            $l->cond_of_goods = $request->cond_of_goods;
            $l->val_of_goods = $request->val_of_goods;
            $l->val_cur = $request->val_cur;
            $l->update();

            //delete all items that are not in submitted item list
            // dd($item_list_str);
            $s = DB::table('order_packages')->whereNotIn('id', $request->item_ids)->delete();
            for ($o = 0; $o < count($request->type); $o++) {
                if ($request->item_ids[$o] == '00') {
                    $item = new OrderPackage;

                    $item->order_id = $l->id;
                    $item->type = $request->type[$o];
                    $item->length = $request->len[$o];
                    $item->width = $request->width[$o];
                    $item->height = $request->height[$o];
                    $item->weight = $request->weight[$o];
                    $item->qty = $request->count[$o];
                    $item->save();
                } else {
                    $item = [];
                    $item['type'] = $request->type[$o];
                    $item['length'] = $request->len[$o];
                    $item['width'] = $request->width[$o];
                    $item['height'] = $request->height[$o];
                    $item['weight'] = $request->weight[$o];
                    $item['qty'] = $request->count[$o];

                    OrderPackage::where('id', $request->item_ids[$o])->update($item);
                }
            }
            DB::commit();
            return back()->with(['message' => 'Order Updated', 'message_type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    /**
     * cancel an order
     */
    public function cancelOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);
        try {
            //check if the order belongs to the authenticated user
            $q = Order::where('id', $request->order_id)->where('customer_id', Auth::user()->customer->id)->count();

            if ($q > 0) {
                Order::where('id', $request->order_id)->update([
                    'status' => 'cancelled'
                ]);
                return back()->with(['message' => 'Order Canceled', 'message_type' => 'success']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
