<?php

namespace App\Http\Controllers;

use App\Mail\DispacherMail;
use App\Mail\DispacherNotificationMail;
use App\Models\Location;
use App\Models\Order;
use App\Models\Country;
use App\Models\City;
use App\Models\OrderBatch;
use App\Models\BatchorderLog;
use App\Models\Batchlog;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

use App\Mail\PaymentEmail;
use App\Models\Dispatcher;
use App\Models\User;
use App\Notifications\OrderStatusNotification;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class OrderBatchController extends Controller
{
    public function __construct()
    {
        DispatcherController::canDispatch();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dispatcher.settings.batches.index');
    }

    /**
     * ajax function for locationList
     */
    public function batchList()
    {
        $user = Auth::user();
        $batch = OrderBatch::where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();

        return Datatables::of($batch)
            ->addIndexColumn()
            ->addColumn('status', function ($orderBatch) {
                $mar = "<span class= 'badge bg-secondary'>" . $orderBatch->status . "</span>";
                return $mar;
            })
            ->addColumn('location', function ($orderBatch) {
                $mar = "Cur. Loc. : " . $orderBatch->batchlogs->first()->shipToCity->name;
                return $mar;
            })
            ->addColumn('edit', function ($orderBatch) {
                $edit_url = route('batches.edit', $orderBatch->id);
                return '<a href="' . $edit_url . '" class="btn btn-info btn-sm" ><i class="fa fa-pencil"></i> Add Log</a>';
            })
            ->addColumn('view', function ($orderBatch) {
                $url = route('batches.show', $orderBatch->id);
                return '<a href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View</a>';
            })

            
            ->rawColumns(['edit', 'view', 'location', 'status'])
            ->make(true);
    }

    /**
     * datatable for cutomer orders
     */
    public function batchOrdersList($batch_id)
    {
        $orders = Order::where('batch_id', $batch_id)->orderBy('created_at', 'DESC')->get();
        $batchLog = Batchlog::where('batch_id', $batch_id)->first();
        $originLocation =  $batchLog->shipFromCountry->name . ", " . $batchLog->shipFromCity->name ?? " ";
        $destinationLocation =  $batchLog->shipToCountry->name . ", " . $batchLog->shipToCity->name ?? " ";
        $currentLocation =  Batchlog::where('batch_id', $batch_id)->orderBy('id', 'desc')->first()->shipCurrentCountry->name . ", " . Batchlog::where('batch_id', $batch_id)->orderBy('id', 'desc')->first()->shipCurrentCity->name ?? " ";

        return Datatables::of($orders)
            ->addIndexColumn()
            ->addColumn('status', function ($order) {
                $mar = "<span class= 'badge bg-secondary'>" . $order->status . "</span>";
                return $mar;
            })
            ->addColumn('location', function ($order) use ($originLocation, $destinationLocation, $currentLocation) {
                $mar = "Origin : " . $originLocation . "<br>";
                $mar .= "Destination :" . $destinationLocation . "<br>";
                $mar .= "Current :" . $currentLocation;

                return $mar;
            })
            ->addColumn('date', function ($order) {
                $mar = "<b> Tracking ID : " . $order->tracking_id . "<br>";
                $mar .= "Created at : " . SupportCarbon::parse($order->created_at)->format('F j, Y,') . "<br>";
                $mar .= "Last updated:" . SupportCarbon::parse($order->updated_at)->format('F j, Y,');
                return $mar;
            })
            ->addColumn('parties', function ($order) {
                $mar = "Sender : " . $order->pickup_name . "<br>";
                $mar .= "Receiver:" . $order->delivery_name;
                return $mar;
            })
            ->addColumn('edit', function ($order) {
                $edit_url = route('batchOrderEdit', $order->id);
                return '<a href="' . $edit_url . '" class="btn btn-info btn-sm" ><i class="fa fa-pencil"></i> Edit</a>';
            })
            ->addColumn('price', function ($order) {
                $mar = $order->val_of_goods . "<br>";
                return $mar;
            })
            ->addColumn('view', function ($order) {
                $url = route('orders.show', [$order->id, 'mode' => '0']);
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

    /**
     * ajax search
     */
    public function search(Request $request)
    {
        $query = $request->get('term', '');

        $d = Auth::user()->dispatcher->id;

        $batches = OrderBatch::where('dispatcher_id', $d)->where('name', 'LIKE', '%' . $query . '%')
            ->select('id', 'name')
            ->get();

        $results = [];

        foreach ($batches as $batch) {
            $results[] = [
                'label' => $batch->name,
                'value' => $batch->id,
            ];
        }
        return response()->json($results);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $order_id = 0;
        if ($request->filled('order_id')) {
            $order_id = $request->order_id;
        }
        $dispachers = User::where('user_type', 'dispatcher')->get();
        return view('dispatcher.settings.batches.create', compact('dispachers', 'order_id'));
    }

    // public function imageUpload(){
    //  Storage::fake('photo');
    //  if($this->json('photo')){
    //   $responce = $this->json('POST','/photo', function(){
    //     UploadedFile::fake()->image('image.png');
    //   });
    //  }
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $request->validate([
            'name' => 'required',
            'ship_from_country_id' => 'required|numeric',
            'ship_from_city_id' => 'required|numeric',
            'ship_to_country_id' => 'required|numeric',
            'ship_to_city_id' => 'required|numeric',
            'dispatcher_id' => 'required|numeric',
            'order_id' => 'required',
        ]);

        // try {
        DB::beginTransaction();
        $b = new OrderBatch();
        $b->name = 'BA-' . time() . '-' . $request->name;
        $b->user_id = Auth::user()->id;
        $b->dispatcher_id = $request->dispatcher_id;
        $b->save();
        $b->batch_tracking_id = 'BA-' . rand(99999999, 100000000) . '_' . $b->id;
        $b->save();


        $batch_log = Batchlog::create([
            'ship_from_country_id' => $request->ship_from_country_id,
            'ship_from_city_id' => $request->ship_from_city_id,
            'ship_to_country_id' => $request->ship_to_country_id,
            'ship_to_city_id' => $request->ship_to_city_id,
            'current_location_county_id' => $request->ship_to_country_id,
            'current_location_city_id' => $request->ship_to_city_id,
            'batch_id' => $b->id,
        ]);

        Order::whereIn('id', $request->order_id)->update([
            'batch_id' => $b->id,
            'status' => "assigned",
            'current_location_country_id' => $request->ship_to_country_id,
            'current_location_city_id' => $request->ship_to_city_id,
        ]);
        $orders = Order::whereIn('id', $request->order_id)->get();
        // dd($orders->toArray());
        foreach ($orders as $key => $order) {
            $subTotal = $order->val_of_goods + $order->shipping_cost;
            $commissionAmount = $order->val_of_goods * 0.015;
            $total = $subTotal - $commissionAmount;
            $u = updateAccountBalance(Auth::id(), ($total), $order->tracking_id, 'debit', 'Order Payment To Admin After (0.015) commisssion' . $order->tracking_id);
        }

        foreach ($request->order_id as $value) {
            $batchorder_log = new BatchorderLog();
            $batchorder_log->batch_id = $b->id;
            $batchorder_log->order_id = $value;
            $batchorder_log->save();
        }
        DB::commit();
        $user = User::find($order->agent_id);
        if (isset($user)) {
            $data = [
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->name,
                'subject' => 'Order Status Update',
                'body' => 'A new order has been Assigned a Batch by Admin.',
                'url' => route('agentsOrders')
            ];
            $user->notify(new OrderStatusNotification($data));
        }

        $dispatcher = User::where('id', $b->dispatcher_id)->first();
        $orders = Order::whereIn('id', $request->order_id)->get();
        if (isset($dispatcher)) {
            $data = [
                'user_id' => $dispatcher->id,
                'user_name' => $dispatcher->name,
                'subject' => 'Order Status Update',
                'body' => 'A new order has been Assigned a Batch by Admin.',
                'url' => route('agentsOrders')
            ];

            $dispatcher->notify(new OrderStatusNotification($data));

            Mail::to($dispatcher->email)->send(new DispacherNotificationMail($b, $orders));
        }



        return redirect()->route('batches.index')->with(['message' => 'Batch Created', 'message_type' => 'success']);
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     Log::error($e->getMessage(), ['exception' => $e]);
        //     return back()->with('message', "An error occured " . $e->getMessage());
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderBatch  $orderBatch
     * @return \Illuminate\Http\Response
     */
    public function show($orderBatch)
    {
        $batch = OrderBatch::where('id', $orderBatch)->first();
        $batchLogs = $batch->batchlogs()->orderBy('id', 'asc')->get();
        $loactions = [];
        $start = 1;
        $end = count($batchLogs);
        if (count($batchLogs) > 0) {
            foreach ($batchLogs as $log) {
                if ($start == 1) {
                    $loactions[] = [
                        'name' => $log->shipFromCountry->name . ', ' . $log->shipFromCity->name,
                        'latitude' => $log->shipFromCity->latitude,
                        'longitude' => $log->shipFromCity->longitude,
                        'type' => 'pickup'
                    ];
                } else {
                    $loactions[] = [
                        'name' => $log->shipCurrentCountry->name . ', ' . $log->shipCurrentCity->name,
                        'latitude' => $log->shipCurrentCity->latitude,
                        'longitude' => $log->shipCurrentCity->longitude,
                        'type' => 'current'
                    ];
                }
                if ($start == $end) {
                    $loactions[] = [
                        'name' => $log->shipToCountry->name . ', ' . $log->shipToCity->name,
                        'latitude' => $log->shipToCity->latitude,
                        'longitude' => $log->shipToCity->longitude,
                        'type' => 'delivery'
                    ];
                }

                $start++;
            }
        }
        return view('dispatcher.settings.batches.show', ['batch' => $batch, 'loactions' => $loactions]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrderBatch  $orderBatch
     * @return \Illuminate\Http\Response
     */
    public function edit($orderBatch)
    {
        $orderBatch = OrderBatch::where('id', $orderBatch)->first();
        $lastCountryId = Batchlog::where('batch_id', $orderBatch->id)->orderBy('id', 'desc')->first()->current_location_county_id ?? 0;
        $lastCityId = Batchlog::where('batch_id', $orderBatch->id)->orderBy('id', 'desc')->first()->current_location_city_id ?? 0;

        $data = [
            'batch' => $orderBatch,
            'lastCountryId' => $lastCountryId,
            'lastCityId' => $lastCityId
        ];
        //  return $data;
        // $loc_str = $orderBatch->location->postcode . '-' . $orderBatch->location->name . ' [Lat: ' . $orderBatch->location->latitude . ', Long: ' . $orderBatch->location->longitude . ']';
        return view('dispatcher.settings.batches.edit', $data);
    }

    /**
     * modify an order
     */
    public function batchOrderEdit(Request $request, $order_id)
    {
        // dd($request->all());
        if ($request->method() == 'POST') {
            $request->validate([
                'batch_id' => 'required|numeric|exists:order_batches,id',
                'status' => 'required|string',
                'current_location_id' => 'required|numeric|exists:locations,id'
            ]);
            try {
                $o = Order::where('id', $order_id)->update([
                    'status' => $request->status,
                    'batch_id' => $request->batch_id,
                    'current_location_id' => $request->current_location_id
                ]);
                return back()->with(['message' => 'Order Updated', 'message_type' => 'success']);
            } catch (\Exception $e) {
                Log::error($e->getMessage(), ['exception' => $e]);
                return back()->with('message', "An error occured " . $e->getMessage());
            }
        } else {
            $o = Order::where('id', $order_id)->first();
            $loc_str = $o->current_location->postcode . '-' . $o->current_location->name . ' [Lat: ' . $o->current_location->latitude . ', Long: ' . $o->current_location->longitude . ']';
            return view('dispatcher.settings.orders.edit', ['order' => $o, 'loc_str' => $loc_str]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrderBatch  $orderBatch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $orderBatch)
    {
        // return $request;
        $request->validate([
            'ship_to_country_id' => 'required|numeric',
            'ship_to_city_id' => 'required|numeric',
            'status' => 'required'
        ]);

        try {
            $orderBatch = OrderBatch::where('id', $orderBatch)->first();
            DB::beginTransaction();
            $orderBatch->update([
                'status' => $request->status
            ]);
            $firstLog = Batchlog::where('batch_id', $orderBatch->id)->first();

            $batch_log = Batchlog::create([
                'ship_from_country_id' => $firstLog->ship_from_country_id,
                'ship_from_city_id' => $firstLog->ship_from_city_id,
                'ship_to_country_id' => $firstLog->ship_to_country_id,
                'ship_to_city_id' => $firstLog->ship_to_city_id,
                'current_location_county_id' => $request->ship_to_country_id,
                'current_location_city_id' => $request->ship_to_city_id,
                'batch_id' => $orderBatch->id,
            ]);

            //update status of associated orders
            Order::where('batch_id', $orderBatch->id)->update([
                'status' => $request->status,
                'current_location_country_id' => $request->ship_to_country_id,
                'current_location_city_id' => $request->ship_to_city_id,
            ]);

            DB::commit();
            $oo = Order::where('batch_id', $orderBatch->id)->get();
            foreach ($oo as $o) {
                Mail::to($o->pickup_email)->send(new PaymentEmail($o));
                Mail::to($o->delivery_email)->send(new PaymentEmail($o));
            }
            return back()->with(['message' => 'Batch Updated', 'message_type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with(['message' => "An error occured " . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderBatch  $orderBatch
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderBatch $orderBatch)
    {
        //
    }
}
