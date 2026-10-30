<?php

namespace App\Http\Controllers;

use App\Constraints\OrderStatus;
use App\Models\Location;
use App\Models\Order;
use App\Models\WalkInCustomer;
use App\Models\OrderPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use App\Mail\PaymentEmail;
use App\Models\Bank_Detail;
use App\Models\BankDetail;
use App\Models\City;
use App\Models\CityShippingCost;
use App\Models\Country;
use App\Models\OrderBatch;
use App\Models\ShippingCost;
use App\Models\ShippingRate;
use App\Models\User;
use App\Notifications\OrderStatusNotification;
use Illuminate\Support\Facades\Mail;
use App\Services\Shipping\DHLExpressService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class WalkInOrderAgents extends Controller
{
    protected $dhl;

    public function __construct(DHLExpressService $dhl)
    {
        $this->dhl = $dhl;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('agents.orders.index');
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
            ->rawColumns(['status', 'location', 'edit', 'view', 'parties', 'date', 'price'])
            ->make(true);
    }

    public function generatePDF()
    {

        $data = [
            [
                'title' => 'khyber pakhtunkhwa',
                'provition' => 'INSTITUTE OF MEDICAL SCIENCES',
                'content' => 'Payable at any Branch of BOK in Pakistan'
            ],
            [
                'b_Copty' => 'Bank Copty',
                'acTilte' => 'A/C Title: IOMS Admission, A/C #: 2007941059',
                'content' => 'Payable at any Branch of BOK in Pakistan'
            ],
        ];

        // $pdf = Pdf::loadView('pdf_view', $data);

        // return $pdf->dowload('voucher-pdf');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        // return $bank_detals;
        return view('agents.orders.new');
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

        //dd($request->all());
        $request->validate([
            // 'tax_code_' => 'required',
            'surname_' => 'required',
            'name_' => 'required',
            'gender_' => 'required',
            'dob_' => 'required|date',
            // 'doc_type_' => 'required',
            // 'doc_num_' => 'required',
            // 'company_name1' => 'nullable',
            // 'company_name2' => 'nullable',
            // 'doc_front_' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            // 'doc_back_' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            // 'origin_id' => 'required|numeric|exists:locations,id',
            // 'dest_id' => 'required|numeric|exists:locations,id',
            // 'rate' => 'required|numeric|exists:shipping_rates,id',
            'rx_name' => 'required|string',
            'rx_phone' => 'required|string',
            'rx_email' => 'required|string|email',
            // 'rx_phone_alt' => 'required|string',
            'rx_address1' => 'required|string',
            // 'rx_address2' => 'nullable|string',
            'rx_zip' => 'required|string',
            'customer_city_id' => 'required|string',
            // 'customer_state_id' => 'required|string',
            'customer_country_id' => 'required|string',
            // 's_name' => 'required|string',
            // 's_phone' => 'required|string',
            // 's_email' => 'required|string|email',
            // 's_phone_alt' => 'required|string',
            // 's_address1' => 'required|string',
            // 's_address2' => 'required|string',
            // 's_zip' => 'required|string',
            // 's_city' => 'required|string',
            // 's_state' => 'required|string',
            // 's_country' => 'required|string',
            'r_date' => 'nullable|string',
            'cond_of_goods' => 'required|string',
            'val_of_goods' => 'required|string',
            'val_cur' => 'required|string',
            'invoice_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'invoice_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
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
            'customer_country_id' => 'required',
            // 'customer_state_id' => 'required|numeric',
            'sh_id' => 'required',
            'customer_city_id' => 'required',
            'ship_from_country' => 'required',
            // 'ship_from_state' => 'required|numeric',
            'ship_from_city' => 'required',
            'ship_to_country' => 'required',
            // 'ship_to_state' => 'required',
            'ship_to_city' => 'required',
            'terms_of_sale' => 'required',
            'customs_inv_num' => 'required'
        ]);

        // try {
        DB::beginTransaction();
        $cus = WalkInCustomer::where('tax_code', $request->tax_code_)->first();
        // return $cus;
        if (!$cus) {
            //upload file
            if ($request->hasFile('doc_front_') && $request->hasFile('doc_back_')) {
                $docFront = $request->file('doc_front_');
                $docBack = $request->file('doc_back_');

                $docFrontFileName = 'doc_front_' . time() . '.' . $docFront->getClientOriginalExtension();
                $docBackFileName = 'doc_back_' . time() . '.' . $docBack->getClientOriginalExtension();


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
                    'doc_back' => $docBackFileName,
                    'company_name' => $request->company_name1,
                ]);
            } else {
                $cus = WalkInCustomer::create([
                    'surname' => $request->surname_,
                    'name' => $request->name_,
                    'birthDate' => $request->dob_,
                    'gender' => $request->gender_,
                    'doc_type' => $request->doc_type_,
                    'doc_num' => $request->doc_num_,
                    'tax_code' => $request->tax_code_,
                    'company_name' => $request->company_name1,
                ]);
            }
            $customer = $cus;
        } else {
            $customer = $cus;
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
                    'doc_back' => $docBackFileName,
                    'company_name' => $request->company_name1,
                ]);
            } else {
                $cus = WalkInCustomer::where('tax_code', $request->tax_code_)->update([
                    'surname' => $request->surname_,
                    'name' => $request->name_,
                    'birthDate' => $request->dob_,
                    'gender' => $request->gender_,
                    'doc_type' => $request->doc_type_,
                    'doc_num' => $request->doc_num_,
                    'tax_code' => $request->tax_code_,
                    'company_name' => $request->company_name1,
                ]);
            }
        }

        $invoiceDocName = null;
        $cummercialInvoiceName = null;

        if ($request->hasFile('invoice_document')) {
            $invoiceDoc = $request->file('invoice_document');
            $invoiceDocName = 'invoice_doc' . time() . '.' . $invoiceDoc->getClientOriginalExtension();
            $invoiceDoc->move(public_path('uploads/orders'), $invoiceDocName);
        }

        if ($request->hasFile('cummercial_invoice')) {
            $cummercialInvoice = $request->file('cummercial_invoice');
            $cummercialInvoiceName = 'invoice_doc_commertional' . time() . '.' . $cummercialInvoice->getClientOriginalExtension();
            $cummercialInvoice->move(public_path('uploads/orders'), $cummercialInvoiceName);
        }

        $l = new Order;
        $l->customer_id = $customer->id;
        $l->agent_id = Auth::user()->id;
        $l->walk_in_customer_id = $customer->id;
        $l->sh_id = $request->sh_id;
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
        $l->delivery_city = $request->customer_city_id;
        $l->delivery_state = $request->customer_state_id;
        $l->delivery_country = $request->customer_country_id;

        $cus = WalkInCustomer::where('tax_code', $request->tax_code_)->first();

        $l->pickup_name = $cus->surname ?? '-';
        $l->pickup_phone = $cus->phone ?? '-';
        $l->pickup_email = $cus->email ?? '-';
        $l->pickup_phone_alt = $request->s_phone_alt ?? '-';
        $l->pickup_address1 = $cus->address ?? '-';
        $l->pickup_address2 = $request->s_address2 ?? '-';
        $l->pickup_zip = $request->rx_zip;
        $l->pickup_city = City::find($request->ship_to_city)->name;
        $l->pickup_state = $request->ship_to_state ?? '-';
        $l->pickup_country = Country::find($request->ship_to_country)->name;

        $l->return_date = $request->r_date;
        
        $l->cond_of_goods1 = $request->cond_of_goods;
        $l->val_of_goods1 = $request->val_of_goods;

        $l->cond_of_goods = 0;
        $l->val_of_goods = 0;
        $l->val_cur = $request->val_cur;
        $l->provider = ((!in_array($request->rate, self::$supported_apis) ? $request->rate : 'SIOPAY'));
        $l->terms_of_sale = $request->terms_of_sale;
        $l->customs_inv_num = $request->customs_inv_num;
        $l->current_location_country_id = $request->ship_to_country;
        $l->current_location_city_id = $request->ship_to_city;
        $l->pickup_location_country_id = $request->ship_from_country;
        $l->pickup_location_state_id = $request->ship_from_state;
        $l->pickup_location_city_id = $request->ship_from_city;
        $l->delivery_location_country_id = $request->ship_to_country;
        $l->delivery_location_state_id = $request->ship_to_state;
        $l->delivery_location_city_id = $request->ship_to_city;
        $l->invoice_document = $invoiceDocName;
        $l->cummercial_invoice = $cummercialInvoiceName;
        $l->company_name = $request->company_name2;
        $l->save();
        // for ($o = 0; $o < count($request->group-a); $o++) {

        $totalWeight = 0;
        $package_items = [];
        $packages = [];
        foreach ($request->items as $key => $item) {

            $orderPackage = new OrderPackage;
            $orderPackage->order_id = $l->id;
            $orderPackage->type = $item['type'];
            $orderPackage->length = $item['len'];
            $orderPackage->width = $item['width'];
            $orderPackage->height = $item['height'];
            $orderPackage->weight = $item['weight'];
            $orderPackage->qty = $item['count'];
            $orderPackage->item_desc = $item['item_desc'];
            $orderPackage->item_value = $item['item_value'];
            $orderPackage->save();

            //packages for DHL shipment
            $packages[] = [
                        'typeCode' => '2BP',  
                        'weight' => (float) $item['weight'],
                        'dimensions' => [
                            'length' => (float) $item['len'],
                            'width' => (float) $item['width'],
                            'height' => (float) $item['height']
                        ],
                        'referenceNumber' => $key + 1,
                        ];

            //line items for DHL shipment
            $lineItems[] = [
                    'number' => $key + 1,
                    'description' => $item['type'],
                    'price' => (float) $item['item_value'],
                    'quantity' => [
                        'value' => (int) $item['count'],
                        'unitOfMeasurement' => 'BOX',
                    ],
                    'commodityCodes' => [
                        [
                            'typeCode' => 'outbound',
                            'value' => '851713',
                        ]
                    ],
                    'exportReasonType' => 'permanent',
                    'manufacturerCountry' => 'IT',
                    'weight' => [
                        'netValue' => (float) $item['weight'],
                        'grossValue' => (float) $item['weight'],
                    ],
                    'isTaxesPaid' => true,
                ];
            //csv rows data
            $package_items[] = [
                $l->tracking_id,
                Carbon::now()->format('d/m/Y'),
                $request->rx_name,
                '',
                $request->rx_address1,
                '',
                $l->pickup_city,
                $request->rx_zip,
                $request->ship_to_state,
                $l->pickup_country,
                $request->rx_email,
                $request->rx_phone,
                $orderPackage->type,
                $orderPackage->item_value,
                $orderPackage->item_desc,
                $orderPackage->weight,
                'DHL Express',
                '',
                $orderPackage->order_id,
                $orderPackage->qty,
                $request->company_name2 ?? '',
                'Yes',
                'No',
                $request->ship_to_country,
                $orderPackage->height,
                $orderPackage->width,
                $orderPackage->length,
                'DHL',
                'P',
                'KG',
                $request->val_cur,
                '',
                '',
                '',
                $orderPackage->item_desc,
                'No',
                '',
                'Yes',
                $request->tax_code_,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ];
        }

        $shipFromCountryId = $l->pickup_location_country_id;
        $shipFromCityId = $l->pickup_location_city_id;
        $shipToCountryId = $l->delivery_location_country_id;
        $totalWeight = (float) OrderPackage::where('order_id', $l->id)->sum('weight');

        $shippingCostPrice = 0;

        if($request->rdShippingcarrier == "siopay") {
            $locationShippingCost = $this->locationShippingCost($shipFromCityId, $l->delivery_location_city_id, $totalWeight);

            if (! $locationShippingCost) {
                DB::rollBack();

                return back()
                    ->withInput()
                    ->withErrors(['shipping_cost' => 'First define the location shipping rate for these cities and weight.']);
            }

            $shippingCostPrice = $this->locationShippingCostPrice($locationShippingCost, $totalWeight);
        } else {
            $shippingCostPrice = (float) $request->shipping_cost;

            //call dhl shipment api here
            $customerDetails = [
                'shipperDetails' => [
                    'postalAddress' => [
                        'cityName' => City::find($request->ship_from_city)->name,
                        'countryCode' => Country::find($request->ship_from_country)->iso2,
                        'addressLine1' => $request->sender_address_,
                        'addressLine2' => 'address2',
                        'postalCode' => $request->ship_from_zip ?? 'M5B 0A5',
                    ],
                    'contactInformation' => [
                        'email' => $request->sender_email_,
                        'phone' => $request->sender_phone_ ?? '999999',
                        'mobilePhone' => $request->sender_phone_,
                        'companyName' => $request->company_name1 ?? 'Sio Pay',
                        'fullName' => $request->name_ .' '. $request->surname_,
                    ]
                ],
                'receiverDetails' => [
                    'postalAddress' => [
                        'postalCode'   => $request->rx_zip,
                        'cityName'     => City::find($request->customer_city_id)->name,
                        'countryCode'  => Country::find($request->customer_country_id)->iso2,
                        'provinceCode' => $request->customer_state_id,
                        'addressLine1' => $request->rx_address1,
                        'addressLine2' => $request->rx_address2,
                        'addressLine3' => 'address2',
                        'countyName'   => 'NA',
                        'provinceName' => $request->customer_state_id,
                        'countryName'  => Country::find($request->customer_country_id)->name,
                    ],
                    'contactInformation' => [
                        'email'       => $request->rx_email,
                        'phone'       => $request->rx_phone ?? '999999',
                        'mobilePhone' => $request->rx_phone_alt ?? '999999',
                        'companyName' => $request->company_name2 ?? 'Company Name',
                        'fullName'    => $request->rx_name
                    ],
                    'registrationNumbers' => [
                        [
                            'typeCode' => 'VAT',
                            'number' => 'AU123456789',
                            'issuerCountryCode' => 'AU',
                        ]
                    ]
                ],
                'buyerDetails' => [
                    'postalAddress' => [
                        'postalCode'   => $request->rx_zip,
                        'cityName'     => City::find($request->customer_city_id)->name,
                        'countryCode'  => Country::find($request->customer_country_id)->iso2,
                        'provinceCode' => $request->customer_state_id,
                        'addressLine1' => $request->rx_address1,
                        'addressLine2' => $request->rx_address2,
                        'addressLine3' => 'address2',
                        'countyName'   => 'NA',
                        'provinceName' => $request->customer_state_id,
                        'countryName'  => Country::find($request->customer_country_id)->name,
                    ],
                    'contactInformation' => [
                        'email'       => $request->rx_email,
                        'phone'       => $request->rx_phone ?? '999999',
                        'mobilePhone' => $request->rx_phone_alt ?? '999999',
                        'companyName' => $request->company_name2 ?? 'Company Name',
                        'fullName'    => $request->rx_name
                    ]
                ]
            ];


            $exportDeclaration = [
                'invoice' => [
                    'number' => $request->customs_inv_num,
                    'date'   => Carbon::now()->toDateString(),
                ],
                'lineItems' => $lineItems
            ];

            $content =[
                'packages'               => $packages,
                'areMorePackagesToBeAddedLater' => false,
                'isCustomsDeclarable'    => false,
                'declaredValue'          => $request->val_of_goods ?? null,
                'declaredValueCurrency'  => $request->val_cur ?? null,
                'exportDeclaration'      => $exportDeclaration ?? null,
                'description'            => $request->customs_inv_num ?? null,
                'incoterm'               => 'DAP',
                'unitOfMeasurement'      => 'metric',
            ];

            //$plannedShippingDate = $request->r_date ?? Carbon::now()->nextWeekday()->toDateString();
            $date = Carbon::createFromFormat('Y-m-d', $request->r_date)
            ->setTime(17, 10, 9)          // set required time
            ->setTimezone('Europe/Paris'); // GMT+01:00

            $plannedShippingDate = $date->format('Y-m-d\TH:i:s \G\M\TP');
            $l->provider = $request->rdShippingcarrier;
        }


        $l->cond_of_goods = OrderPackage::where('order_id', $l->id)->sum('qty');
        $l->val_of_goods = OrderPackage::where('order_id', $l->id)->sum('item_value');
        $l->shipping_cost = $shippingCostPrice;
        $l->save();
        DB::commit();

        //create dhl shipment here
        if($request->rdShippingcarrier != "siopay") { //if carrier is dhl
            try {
                $dhlResponse = $this->createShipment($request->dhl_product_code, $customerDetails, $content, $plannedShippingDate);
                if (isset($dhlResponse['error'])) {              
                    return redirect()->route('agentsOrders')->with(['message' => 'DHL Shipment Error: ' . $dhlResponse['error'], 'message_type' => 'danger']);
                } else {
                    //save dhl shipment details
                    $l->shipping_rate_id = $dhlResponse['shipmentId'] ?? null;
                    // $l->dhl_label_url = $dhlResponse['labelUrl'] ?? null;
                    // $l->dhl_awb_number = $dhlResponse['awbNumber'] ?? null;
                    $l->tracking_id = $dhlResponse['shipmentTrackingNumber'] ?? null;
                    $l->save();
                }   
            } catch (\Exception $e) {
                Log::error('An error occured while creating DHL shipment: ' . $e->getMessage(), ['exception' => $e]);
                return redirect()->route('agentsOrders')->with(['message' => 'An error occured while creating DHL shipment: ' . $e->getMessage(), 'message_type' => 'danger']);
            }
        }

        //export csv file for dhl shipment
        if($request->rdShippingcarrier == 'dhl') {
            try {
                $fileName = 'dhl_order_' . $l->tracking_id . '.csv';
                //$path = base_path('exports/'.$fileName);
                $path = storage_path('app/'.$fileName);
                $headers = [
                'Order Number',
                'Date',
                'To Name',
                'Destination Building',
                'Destination Street',
                'Destination Suburb',
                'Destination City',
                'Destination Postcode',
                'Destination State',
                'Destination Country',
                'Destination Email',
                'Destination Phone',
                'Item Name',
                'Item Price',
                'Instructions',
                'Weight',
                'Shipping Method',
                'Reference',
                'SKU',
                'Qty',
                'Company',
                'Signature Required',
                'ATL',
                'Country Code',
                'Package Height',
                'Package Width',
                'Package Length',
                'Carrier',
                'Carrier Product Code',
                'Carrier Product Unit Type',
                'Declared Value Currency',
                'Code',
                'Color',
                'Size',
                'Contents',
                'Dangerous Goods',
                'Country of Manufacturer',
                'DDP',
                'ReceiverVAT',
                'ReceiverEORI',
                'ShippingFreightValue',
                'Brand',
                'Usage',
                'Material',
                'Model',
                'MID Code',
                'Receiver National ID',
                'Receiver Passport Number',
                'Receiver USCI',
                'Receiver CR',
                'Receiver Brazil CNP',
                ];

                // data rows
                $rows = $package_items;
                $file = fopen($path, 'w');
                fputcsv($file, $headers);
                foreach ($rows as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
                Log::info('Order CSV exported successfully at path: ' . $path);
            } catch (\Exception $e) {
                Log::error('An error occured while exporting orders to dhl csv : ' . $e->getMessage(), ['exception' => $e]);
            }
        }

        $users = User::where('user_type', 'admin')->where('blocked', false)->get();
        foreach ($users as $user) {
            $data = [
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->name,
                'subject' => 'Order Created',
                'body' => 'A new order has been made check all orders.',
                'url' => route('allOrders')
            ];
            $user->notify(new OrderStatusNotification($data));
        }
        return redirect()->route('agentsOrders')->with(['message' => 'Order saved You can proceed to add it to a batch', 'message_type' => 'success']);
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     Log::error($e->getMessage(), ['exception' => $e]);
        //     return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        // }
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
            // 's_name' => 'required|string',
            // 's_phone' => 'required|string',
            // 's_email' => 'required|string',
            // 's_phone_alt' => 'nullable|string',
            // 's_address1' => 'required|string',
            // 's_address2' => 'nullable|string',
            // 's_zip' => 'required|string',
            // 's_city' => 'required|string',
            // 's_state' => 'required|string',
            // 's_country' => 'required|string',
            'r_date' => 'nullable|string',
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
            // $l->pickup_name = $request->s_name;
            // $l->pickup_phone = $request->s_phone;
            // $l->pickup_email = $request->s_email;
            // $l->pickup_phone_alt = $request->s_phone_alt ?? null;
            // $l->pickup_address1 = $request->s_address1;
            // $l->pickup_address2 = $request->s_address2 ?? null;
            // $l->pickup_zip = $request->s_zip;
            // $l->pickup_city = $request->s_city;
            // $l->pickup_state = $request->s_state;
            // $l->pickup_country = $request->s_country;
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
        // return $request->all();
        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);
        try {
            $q = Order::where('id', $request->order_id)->count();
            if ($q > 0) {
                Order::where('id', $request->order_id)->update([
                    'status' => 'cancelled'
                ]);
                $users = User::where('user_type', 'admin')->where('blocked', false)->get();
                foreach ($users as $user) {
                    $data = [
                        'user_id' => Auth::user()->id,
                        'user_name' => Auth::user()->name,
                        'subject' => 'Order Cancelled',
                        'body' => 'A new order has been cancelled by agent.',
                        'url' => route('allOrders')
                    ];
                    $user->notify(new OrderStatusNotification($data));
                }
                return back()->with(['message' => 'Order Canceled', 'message_type' => 'success']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage());
        }
    }

    public function orderAccept(Request $request, $order_id)
    {
        try {

            $o = Order::where('id', $order_id)->first();
            $subTotal = 0;
            $total =  (float) $subTotal + (float) $o->shipping_cost;
            if (getAccountbalances(Auth::id())['balance'] >= $total) {
                DB::beginTransaction();


                Order::where('id', $order_id)->update([
                    'status' => 'placed',
                    'pickup_time' => Carbon::now(),
                ]);

                $u = updateAccountBalance(Auth::id(), ($total), $o->tracking_id, 'credit', 'Order ' . $o->tracking_id, 'sales');

                DB::commit();
                $users = User::where('user_type', 'admin')->where('blocked', false)->get();
                foreach ($users as $user) {
                    $data = [
                        'user_id' => Auth::user()->id,
                        'user_name' => Auth::user()->name,
                        'subject' => 'Order Placed',
                        'body' => 'A new order has been Placed by agent.',
                        'url' => route('allOrders')
                    ];
                    $user->notify(new OrderStatusNotification($data));
                }
                Mail::to($o->pickup_email)->send(new PaymentEmail($o));
                Mail::to($o->delivery_email)->send(new PaymentEmail($o));

                return back()->with(['message' => 'Order Accepted', 'message_type' => 'success']);
            } else {
                return back()->with(['message' => 'Insufficent Balance to complete Order']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage());
        }
    }

    /**
     * dispatcher accpt order
     */
    public function orderPickedUp(Request $request, $order_id)
    {
        try {
            $o = Order::where('id', $order_id)->first();
            $subTotal = $o->shipping_cost;
            OrderBatch::where('id', $o->batch_id)->update([
                'status' =>  'delivered'
            ]);
            Order::where('id', $order_id)->update([
                'status' => OrderStatus::CUSTOMER_RECEIVED,
                'delivery_time' => Carbon::now(),
                'current_location_id' => $o->delivery_location_city_id
            ]);
            $pickUpPercentage = Auth::user()->pickup_comission_percentage ?? 1;

            $c = updateAccountBalance(Auth::id(), ($subTotal * ($pickUpPercentage / 100)), $o->tracking_id, 'debit', 'Order commission ' . $o->tracking_id, 'commission');
            $users = User::where('user_type', 'admin')->where('blocked', false)->get();
            foreach ($users as $user) {
                $data = [
                    'user_id' => Auth::user()->id,
                    'user_name' => Auth::user()->name,
                    'subject' => 'Order Picked-Up',
                    'body' => 'A new order has been Picked-Up by agent.',
                    'url' => route('allOrders')
                ];
                $user->notify(new OrderStatusNotification($data));
            }
            return back()->with(['message' => 'Order Marked As Picked Up', 'message_type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage());
        }
    }

    /**
     * Store DHL Label
     */
    protected function storeLabel(array $documents, string $trackingNumber): void
    {
        foreach ($documents as $doc) {
            if ($doc['typeCode'] === 'label') {
                Log::info('Storing DHL Label PDF '.$trackingNumber);
                try {
                    Storage::put(
                        'shipping/dhl/' . $trackingNumber . '.pdf',
                        base64_decode($doc['content'])
                    );
                } catch (\Exception $e) {
                    Log::error('Error storing DHL Label: ' . $e->getMessage());
                    report($e);
                }
            }
        }
    }

    /**
     * Create DHL Shippment
     * @param array $shipperDetails
     * @param array $receiverDetails
     * @param array $packages
     * @return array
     */
    public function createShipment($productCode, $receiverDetails, $content, $plannedShippingDate)
    {
        try {
            $response = $this->dhl->createShipment([
                'customerDetails' => $receiverDetails,
                'productCode' =>  $productCode,
                'customerReferences' => [
                    [
                        'value' => 'CU-ORDER-10001',
                        'typeCode' => 'CU',
                    ]
                ],
                'content' => $content,
                'plannedShippingDateAndTime' => $plannedShippingDate,
                'areMorePackagesToBeAddedLater' => false,
            ]);

            if (!empty($response) && isset($response['shipmentTrackingNumber'])) {
                Log::info('DHL shipment created successfully', [
                    'tracking_number' => $response['shipmentTrackingNumber'],
                    'response' => $response,
                ]);

                if (!empty($response['documents'])) {
                    Log::info('DHL Label received, storing label...');
                    try {
                        $this->storeLabel($response['documents'], $response['shipmentTrackingNumber']);
                    } catch (\Exception $e) {
                        Log::error('Error storing DHL Label: ' . $e->getMessage());
                        report($e);
                    }
                } else {
                    Log::info('DHL Label empty');
                }
            } else {
                Log::warning('DHL shipment response received but shipment not created', [
                    'response' => $response,
                ]);
            }
            return $response;
       } catch (\Exception $e) {
           Log::error('An error occured while creating dhl shipment : ' . $e->getMessage(), ['exception' => $e]);
           print_r($e->getMessage());exit;
       }
    }

    protected function locationShippingCost($originCityId, $destinationCityId, float $weight): ?CityShippingCost
    {
        if (! Schema::hasColumn('city_shipping_costs', 'origin_city_id')) {
            return null;
        }

        return CityShippingCost::query()
            ->where('origin_city_id', $originCityId)
            ->where('destination_city_id', $destinationCityId)
            ->where(function ($query) use ($weight) {
                $query->whereNull('weight')
                    ->orWhere('weight', '>=', $weight);
            })
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
