<?php

namespace App\Http\Controllers;

use App\Constraints\DeliveryManStatus;
use App\Constraints\OrderStatus;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use App\Mail\PaymentEmail;
use App\Models\Bank_Detail;
use App\Models\City;
use App\Models\Country;
use App\Models\User;
use App\Models\WalkInCustomer;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users.orders.index');
    }

    /**
     * datatable for cutomer orders
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
                    $mar = number_format($order->shipping_rate->price, 2) . "<br>";
                    $url = route('payment.summary', ['order_id' => $order->id]);
                    $mar .= '<a href="' . $url . '" class="btn btn-info " >$ Pay</a>';
                    return $mar;
                } else {
                    $mar = number_format($order->shipping_rate->price, 2) . "<br>";
                    return number_format($mar, 2);
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

    public function allOrdersDeliveryMan()
    {
        return view('admin.orders.allOrdersDeliveryMan');
    }

    public function allOrdersCancel()
    {
        return view('admin.orders.allOrdersCancel');
    }
    public function allOrdersDelivered()
    {
        return view('admin.orders.allOrdersDelivered');
    }

    public function deliveryMan($id)
    {
        $users = [];
        $usrs = User::where('user_type','dispatcher')->where('blocked',0)->get();
        foreach($usrs as $usr){
            $users[] = array(
                'id' => $usr->id,
                'name' => $usr->name,
                'country' => Country::where('id',$usr->country)->first()->name??'',
            );
        }
        return view('admin.orders.deliveryMan')->with([
            'users' => $users,
            'id' => $id,
        ]);
    }

    public function destinationAgent($id)
    {
        $users = [];
        $usrs = User::where('user_type','agent')->get();
        foreach($usrs as $usr){
            $users[] = array(
                'id' => $usr->id,
                'name' => $usr->name,
                'country' => Country::where('id',$usr->country)->first()->name??'',
            );
        }
        return view('admin.orders.destinationAgent')->with([
            'users' => $users,
            'id' => $id,
        ]);
    }

    public function updateDeliveryMan(Request $request){
        Order::where('id',$request->id)->update([
            'dispatcher_id' => $request->deliveryMan,
            'status' => OrderStatus::IN_DELIVERY_MAN,
            'delivery_man_status' => DeliveryManStatus::ENGAGE_WITH_PARCEL,
            'dispatcher_date' => Carbon::now()
        ]);
        return redirect()->route('allOrders');
    }

    public function updateDeliverManStatus(Request $request){
        if($request->status=='delivered'){
            Order::where('id',$request->id)->update([
                'status' => $request->status, //OrderStatus::IN_DELIVERY_MAN,
                'delivery_man_status' => DeliveryManStatus::DELIVERED
            ]);
        }else{
        Order::where('id',$request->id)->update([
            'status' => $request->status, //OrderStatus::IN_DELIVERY_MAN,
            'delivery_man_status' => DeliveryManStatus::ENGAGE_WITH_PARCEL
        ]);
        }
        return redirect()->route('allOrdersDeliveryMan');
    }

    public function updateDestinationAgent(Request $request){
        Order::where('id',$request->id)->update([
            'destination_agent_id' => $request->deliveryMan,
            // 'status' => OrderStatus::IN_DELIVERY_MAN,
            // 'delivery_man_status' => DeliveryManStatus::ENGAGE_WITH_PARCEL,
            'agent_assign_date' => Carbon::now()
        ]);
        return redirect()->route('allOrders');
    }

    /**
     * datatable for all orders
     */
    public function allOrdersList(Request $request)
    {
        $query = Order::query();
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $start = Carbon::parse($request->startDate)->startOfDay();
            $end = Carbon::parse($request->endDate)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($request->filled('startDate')) {
            $start = Carbon::parse($request->startDate)->startOfDay();
            $query->whereDate('created_at', '=', $start);
        } elseif ($request->filled('endDate')) {
            $end = Carbon::parse($request->endDate)->endOfDay();
            $query->whereDate('created_at', '=', $end);
        }
        $orders = $query->orderBy('created_at', 'DESC')->get();

        return Datatables::of($orders)
            ->addIndexColumn()
            ->addColumn('agentInfo', function ($order) {
                $userInfo = User::where('id',$order->agent_id)->first();
                $country = Country::where('id',$userInfo->agent->country_id)->first();
                $mar = "<b>Name : </b>" . $userInfo->name . "<br>";
                $mar .= "<b>Phone:</b>" . $userInfo->agent->phone . "<br>";
                $mar .= "<b>Email:</b>" . $userInfo->email . "<br>";
                $mar .= "<b>Address :</b> ". $country->name.'. '. $userInfo->agent->address1. "<br>";
                $mar .= "<span class='text-danger'><b>Entry Date:</b>" . $order->created_at->format('d M Y, h:i A').'</span>';
                return $mar;
            })
            ->addColumn('status', function ($order) {
                $mar = "<span class= 'badge bg-secondary'>" . $order->status . "</span>";
                return $mar;
            })
            ->addColumn('destinationAgent', function ($order) {
                if($order->destination_agent_id){
                $userInfo = User::where('id',$order->destination_agent_id)->first();
                $country = Country::where('id',$userInfo->agent->country_id)->first();
                $mar = "<b>Name : </b>" . $userInfo->name . "<br>";
                $mar .= "<b>Phone:</b>" . $userInfo->agent->phone . "<br>";
                $mar .= "<b>Email:</b>" . $userInfo->email . "<br>";
                $mar .= "<b>Address :</b> ". $country->name.'. '. $userInfo->agent->address1. "<br>";
                $mar .= "<span class='text-danger'><b>Entry Date:</b>" . Carbon::parse($order->agent_assign_date)->format('d M Y, h:i A').'</span>';
                return $mar;
                }
                else{
                    return '<a class="badge bg-secondary" href="'. route('destinationAgent',$order->id) .'">Assign Agent</a>';
                }
            })
            ->addColumn('deliveryMan', function ($order) {
                if($order->dispatcher_id){
                    $userInfo = User::where('id',$order->dispatcher_id)->first();
                $country = Country::where('id',$userInfo->country)->first();
                $mar = "<b>Name : </b>" . $userInfo->name . "<br>";
                $mar .= "<b>Phone:</b>" . $userInfo->mobile. "<br>";
                $mar .= "<b>Email:</b>" . $userInfo->email. "<br>";
                $mar .= "<b>Assing Date:</b>" . Carbon::parse($order->dispatcher_date)->format('d M Y, h:i A'). "<br>";
                $mar .= "<span class='text-danger'><b>Status:</b>" . $order->delivery_man_status.'</span>';// . "<br>";
                // $mar .= "<b>Address :</b> ". $country->name.'. '. $userInfo->agent->address1. "<br>";
                // $mar .= "<span class='text-danger'><b>Entry Date:</b>" . $order->created_at->format('d M Y, h:i A').'</span>';
                return $mar;
                }
                else{
                return "<a href='".route('deliveryMan',$order->id)."' class='badge bg-secondary' >Assign Delivery Man</a>"; 
                }
            })
            ->addColumn('location', function ($order) {
                $current  = Country::where('id', $order->current_location_country_id)->first()->name . ", " . City::find($order->current_location_city_id)->name;
                $destination = Country::where('id', $order->delivery_location_country_id)->first()->name . ", " . City::find($order->delivery_location_city_id)->name;
                $origin = Country::where('id', $order->pickup_location_country_id)->first()->name . ", " . City::find($order->pickup_location_city_id)->name;
                $mar = "Origin : " . $origin . "<br>";
                $mar .= "Destination:" . $destination . "<br>";
                $mar .= "Current : " . $current;
                return $mar;
            })
            ->addColumn('date', function ($order) {
                $mar = "<b> Tracking ID : " . $order->tracking_id . "</b><br>";
                $mar .= "Booking Date : " . $order->created_at->format('d M Y, h:i A') . "<br>";
                $mar .= "<span class='text-danger'>Last updated:" . $order->updated_at->format('d M Y, h:i A').'</span>';
                return $mar;
            })
            ->addColumn('tax_code', function ($order) {
                $code = '';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $code = $cinfo->first()->tax_code;
                }
                return $code;
            })
            ->addColumn('document_type', function ($order) {
                $code = '';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $code = $cinfo->first()->doc_type;
                }
                return $code;
            })
            ->addColumn('document_number', function ($order) {
                $code = '';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $code = $cinfo->first()->doc_num;
                }
                return $code;
            })
            ->addColumn('sender', function ($order) {
                $mar = "<b>Name :</b> " . $order->pickup_name . "<br>";
                $mar .= "<b>Phone :</b> " . $order->pickup_phone . "<br>";
                $mar .= "<b>Address:</b>" . $order->pickup_country.'. <br>'.$order->pickup_address1;
                return $mar;
            })
            ->addColumn('receiver', function ($order) {
                $country = Country::where('id',$order->delivery_country)->first();
                $mar = "<b>Name :</b> " . $order->delivery_name . "<br>";
                $mar .= "<b>Phone :</b> " . $order->delivery_phone . "<br>";
                $mar .= "<b>Address:</b>" . $country->name .'. <br>'.$order->delivery_address1;
                return $mar;
            })
            ->addColumn('doc_front', function ($order) {

                $mar = '---';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $info = $cinfo->first();
                $mar = '<a type="button" class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#doc_front' . $order->id . 'Modal" data-toggle="tooltip" title="View Agent Information">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                    </svg>
                </a>';

                $mar .= '
                <div class="modal fade" id="doc_front' . $order->id . 'Modal" tabindex="-1" aria-labelledby="' . $order->id . 'ModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="' . $order->id . '">' . $order->name . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <iframe class="pdf w-100" src="' . asset('uploads/' . $info->doc_front) . '"  height="650"></iframe>    
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
                }
                return $mar;
            })
            ->addColumn('doc_back', function ($order) {

                $mar = '---';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $info = $cinfo->first();
                $mar = '<a type="button" class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#doc_back' . $order->id . 'Modal" data-toggle="tooltip" title="View Agent Information">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                    </svg>
                </a>';

                $mar .= '
                <div class="modal fade" id="doc_back' . $order->id . 'Modal" tabindex="-1" aria-labelledby="' . $order->id . 'ModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="' . $order->id . '">' . $order->name . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <iframe class="pdf w-100" src="' . asset('uploads/' . $info->doc_back) . '"  height="650"></iframe>    
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
                }
                return $mar;
            })
            ->addColumn('viewpdf', function ($order) {

                $mar = '---';
                if($order->cummercial_invoice){
                $mar = '<a type="button" class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#' . $order->id . 'Modal" data-toggle="tooltip" title="View Agent Information">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                    </svg>
                </a>';

                $mar .= '
                <div class="modal fade" id="' . $order->id . 'Modal" tabindex="-1" aria-labelledby="' . $order->id . 'ModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="' . $order->id . '">' . $order->name . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <iframe class="pdf w-100" src="' . asset('uploads/orders/' . $order->cummercial_invoice) . '"  height="650"></iframe>    
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
                }
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
                $mar = number_format($order->val_of_goods, 2) . "<br>";
                return $mar;
            })
            ->addColumn('view', function ($order) {
                $url = route('orders.show', $order->id);
                return '<a style="white-space:nowrap" href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View/ Track</a>';
            })
            // ->addColumn('delete', function ($order) {
            //     $url = route('shipping_rates.destroy', $order->id);
            //     return '<form method="POST" action="' . $url . '">
            //                 <input type="hidden" name = "_token" value = ' . csrf_token() . '>
            //                 <input type="hidden" name = "_method" value ="DELETE">
            //                 <button type="submit" onclick="return confirm(\'Are you sure you wish to delete this entry?\')" class="btn btn-sm btn-danger">Delete</button>
            //             </form>';
            // })
            ->rawColumns(['deliveryMan','destinationAgent','agentInfo','sender','receiver','status', 'location', 'edit', 'view', 'parties', 'date', 'price', 'viewpdf','doc_front','doc_back'])
            ->make(true);
    }
    public function allOrdersListDeliveryMan(Request $request)
    {
        $query = Order::query();
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $start = Carbon::parse($request->startDate)->startOfDay();
            $end = Carbon::parse($request->endDate)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($request->filled('startDate')) {
            $start = Carbon::parse($request->startDate)->startOfDay();
            $query->whereDate('created_at', '=', $start);
        } elseif ($request->filled('endDate')) {
            $end = Carbon::parse($request->endDate)->endOfDay();
            $query->whereDate('created_at', '=', $end);
        }
        $orders = $query->where('dispatcher_id',Auth::user()->id)
        ->whereNotIn('status',[OrderStatus::DELIVERED,OrderStatus::CANCELLED,OrderStatus::ACCEPT_BY_AGENT])
        ->orderBy('created_at', 'DESC')->get();

        return Datatables::of($orders)
            ->addIndexColumn()
            ->addColumn('agentInfo', function ($order) {
                $userInfo = User::where('id',$order->agent_id)->first();
                $country = Country::where('id',$userInfo->agent->country_id)->first();
                $mar = "<b>Name : </b>" . $userInfo->name . "<br>";
                $mar .= "<b>Phone:</b>" . $userInfo->agent->phone . "<br>";
                $mar .= "<b>Email:</b>" . $userInfo->email . "<br>";
                $mar .= "<b>Address :</b> ". $country->name.'. '. $userInfo->agent->address1. "<br>";
                $mar .= "<span class='text-danger'><b>Entry Date:</b>" . $order->created_at->format('d M Y, h:i A').'</span>';
                return $mar;
            })
            ->addColumn('status', function ($order) {
                $mar = "<a href='".route('statusChange',$order->id)."' class= 'badge bg-secondary'>" . $order->status . "</a>";
                return $mar;
            })
            ->addColumn('destinationAgent', function ($order) {
                if($order->destination_agent_id){
                $userInfo = User::where('id',$order->destination_agent_id)->first();
                $country = Country::where('id',$userInfo->agent->country_id)->first();
                $mar = "<b>Name : </b>" . $userInfo->name . "<br>";
                $mar .= "<b>Phone:</b>" . $userInfo->agent->phone . "<br>";
                $mar .= "<b>Email:</b>" . $userInfo->email . "<br>";
                $mar .= "<b>Address :</b> ". $country->name.'. '. $userInfo->agent->address1. "<br>";
                $mar .= "<span class='text-danger'><b>Entry Date:</b>" . Carbon::parse($order->agent_assign_date)->format('d M Y, h:i A').'</span>';
                return $mar;
                }
                else{
                    return '---';
                }
            })
            ->addColumn('deliveryMan', function ($order) {
                if($order->dispatcher_id){
                    $userInfo = User::where('id',$order->dispatcher_id)->first();
                $country = Country::where('id',$userInfo->country)->first();
                $mar = "<b>Name : </b>" . $userInfo->name . "<br>";
                $mar .= "<b>Phone:</b>" . $userInfo->mobile. "<br>";
                $mar .= "<b>Email:</b>" . $userInfo->email. "<br>";
                $mar .= "<b>Assing Date:</b>" . Carbon::parse($order->dispatcher_date)->format('d M Y, h:i A'). "<br>";
                $mar .= "<span class='text-danger'><b>Status:</b>" . $order->delivery_man_status.'</span>';// . "<br>";
                
                // $mar .= "<b>Address :</b> ". $country->name.'. '. $userInfo->agent->address1. "<br>";
                // $mar .= "<span class='text-danger'><b>Entry Date:</b>" . $order->created_at->format('d M Y, h:i A').'</span>';
                return $mar;
                }
                else{
                return "<a href='".route('deliveryMan',$order->id)."' class='badge bg-secondary' >Assign Delivery Man</a>"; 
                }
            })
            ->addColumn('statusEdit', function ($order) {
                return "<a href='".route('statusChange',$order->id)."' class='badge bg-secondary' >Change Status</a>"; 
            })
            ->addColumn('location', function ($order) {
                $current  = Country::where('id', $order->current_location_country_id)->first()->name . ", " . City::find($order->current_location_city_id)->name;
                $destination = Country::where('id', $order->delivery_location_country_id)->first()->name . ", " . City::find($order->delivery_location_city_id)->name;
                $origin = Country::where('id', $order->pickup_location_country_id)->first()->name . ", " . City::find($order->pickup_location_city_id)->name;
                $mar = "Origin : " . $origin . "<br>";
                $mar .= "Destination:" . $destination . "<br>";
                $mar .= "Current : " . $current;
                return $mar;
            })
            ->addColumn('date', function ($order) {
                $mar = "<b> Tracking ID : " . $order->tracking_id . "</b><br>";
                $mar .= "Booking Date : " . $order->created_at->format('d M Y, h:i A') . "<br>";
                $mar .= "<span class='text-danger'>Last updated:" . $order->updated_at->format('d M Y, h:i A').'</span>';
                return $mar;
            })
            ->addColumn('tax_code', function ($order) {
                $code = '';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $code = $cinfo->first()->tax_code;
                }
                return $code;
            })
            ->addColumn('document_type', function ($order) {
                $code = '';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $code = $cinfo->first()->doc_type;
                }
                return $code;
            })
            ->addColumn('document_number', function ($order) {
                $code = '';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $code = $cinfo->first()->doc_num;
                }
                return $code;
            })
            ->addColumn('sender', function ($order) {
                $mar = "<b>Name :</b> " . $order->pickup_name . "<br>";
                $mar .= "<b>Phone :</b> " . $order->pickup_phone . "<br>";
                $mar .= "<b>Address:</b>" . $order->pickup_country.'. <br>'.$order->pickup_address1;
                return $mar;
            })
            ->addColumn('receiver', function ($order) {
                $country = Country::where('id',$order->delivery_country)->first();
                $mar = "<b>Name :</b> " . $order->delivery_name . "<br>";
                $mar .= "<b>Phone :</b> " . $order->delivery_phone . "<br>";
                $mar .= "<b>Address:</b>" . $country->name .'. <br>'.$order->delivery_address1;
                return $mar;
            })
            ->addColumn('doc_front', function ($order) {

                $mar = '---';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $info = $cinfo->first();
                $mar = '<a type="button" class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#doc_front' . $order->id . 'Modal" data-toggle="tooltip" title="View Agent Information">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                    </svg>
                </a>';

                $mar .= '
                <div class="modal fade" id="doc_front' . $order->id . 'Modal" tabindex="-1" aria-labelledby="' . $order->id . 'ModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="' . $order->id . '">' . $order->name . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <iframe class="pdf w-100" src="' . asset('uploads/' . $info->doc_front) . '"  height="650"></iframe>    
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
                }
                return $mar;
            })
            ->addColumn('doc_back', function ($order) {

                $mar = '---';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $info = $cinfo->first();
                $mar = '<a type="button" class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#doc_back' . $order->id . 'Modal" data-toggle="tooltip" title="View Agent Information">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                    </svg>
                </a>';

                $mar .= '
                <div class="modal fade" id="doc_back' . $order->id . 'Modal" tabindex="-1" aria-labelledby="' . $order->id . 'ModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="' . $order->id . '">' . $order->name . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <iframe class="pdf w-100" src="' . asset('uploads/' . $info->doc_back) . '"  height="650"></iframe>    
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
                }
                return $mar;
            })
            ->addColumn('viewpdf', function ($order) {

                $mar = '---';
                if($order->cummercial_invoice){
                $mar = '<a type="button" class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#' . $order->id . 'Modal" data-toggle="tooltip" title="View Agent Information">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                    </svg>
                </a>';

                $mar .= '
                <div class="modal fade" id="' . $order->id . 'Modal" tabindex="-1" aria-labelledby="' . $order->id . 'ModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="' . $order->id . '">' . $order->name . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <iframe class="pdf w-100" src="' . asset('uploads/orders/' . $order->cummercial_invoice) . '"  height="650"></iframe>    
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
                }
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
                $mar = number_format($order->val_of_goods, 2) . "<br>";
                return $mar;
            })
            ->addColumn('view', function ($order) {
                $url = route('orders.show', $order->id);
                return '<a style="white-space:nowrap" href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View/ Track</a>';
            })
            // ->addColumn('delete', function ($order) {
            //     $url = route('shipping_rates.destroy', $order->id);
            //     return '<form method="POST" action="' . $url . '">
            //                 <input type="hidden" name = "_token" value = ' . csrf_token() . '>
            //                 <input type="hidden" name = "_method" value ="DELETE">
            //                 <button type="submit" onclick="return confirm(\'Are you sure you wish to delete this entry?\')" class="btn btn-sm btn-danger">Delete</button>
            //             </form>';
            // })
            ->rawColumns(['statusEdit','deliveryMan','destinationAgent','agentInfo','sender','receiver','status', 'location', 'edit', 'view', 'parties', 'date', 'price', 'viewpdf','doc_front','doc_back'])
            ->make(true);
    }
    public function allOrdersListDeliveryMan1(Request $request)
    {
        $query = Order::query();
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $start = Carbon::parse($request->startDate)->startOfDay();
            $end = Carbon::parse($request->endDate)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($request->filled('startDate')) {
            $start = Carbon::parse($request->startDate)->startOfDay();
            $query->whereDate('created_at', '=', $start);
        } elseif ($request->filled('endDate')) {
            $end = Carbon::parse($request->endDate)->endOfDay();
            $query->whereDate('created_at', '=', $end);
        }
        $orders = $query->where('dispatcher_id',Auth::user()->id)
        ->whereIn('status',[OrderStatus::CANCELLED])
        ->orderBy('created_at', 'DESC')->get();

        return Datatables::of($orders)
            ->addIndexColumn()
            ->addColumn('agentInfo', function ($order) {
                $userInfo = User::where('id',$order->agent_id)->first();
                $country = Country::where('id',$userInfo->agent->country_id)->first();
                $mar = "<b>Name : </b>" . $userInfo->name . "<br>";
                $mar .= "<b>Phone:</b>" . $userInfo->agent->phone . "<br>";
                $mar .= "<b>Email:</b>" . $userInfo->email . "<br>";
                $mar .= "<b>Address :</b> ". $country->name.'. '. $userInfo->agent->address1. "<br>";
                $mar .= "<span class='text-danger'><b>Entry Date:</b>" . $order->created_at->format('d M Y, h:i A').'</span>';
                return $mar;
            })
            ->addColumn('status', function ($order) {
                $mar = "<span class= 'badge bg-secondary'>" . $order->status . "</span>";
                return $mar;
            })
            ->addColumn('destinationAgent', function ($order) {
                if($order->destination_agent_id){
                $userInfo = User::where('id',$order->destination_agent_id)->first();
                $country = Country::where('id',$userInfo->agent->country_id)->first();
                $mar = "<b>Name : </b>" . $userInfo->name . "<br>";
                $mar .= "<b>Phone:</b>" . $userInfo->agent->phone . "<br>";
                $mar .= "<b>Email:</b>" . $userInfo->email . "<br>";
                $mar .= "<b>Address :</b> ". $country->name.'. '. $userInfo->agent->address1. "<br>";
                $mar .= "<span class='text-danger'><b>Entry Date:</b>" . Carbon::parse($order->agent_assign_date)->format('d M Y, h:i A').'</span>';
                return $mar;
                }
                else{
                    return '---';
                }
            })
            ->addColumn('deliveryMan', function ($order) {
                if($order->dispatcher_id){
                    $userInfo = User::where('id',$order->dispatcher_id)->first();
                $country = Country::where('id',$userInfo->country)->first();
                $mar = "<b>Name : </b>" . $userInfo->name . "<br>";
                $mar .= "<b>Phone:</b>" . $userInfo->mobile. "<br>";
                $mar .= "<b>Email:</b>" . $userInfo->email. "<br>";
                $mar .= "<b>Assing Date:</b>" . Carbon::parse($order->dispatcher_date)->format('d M Y, h:i A'). "<br>";
                $mar .= "<span class='text-danger'><b>Status:</b>" . $order->delivery_man_status.'</span>';// . "<br>";
                
                // $mar .= "<b>Address :</b> ". $country->name.'. '. $userInfo->agent->address1. "<br>";
                // $mar .= "<span class='text-danger'><b>Entry Date:</b>" . $order->created_at->format('d M Y, h:i A').'</span>';
                return $mar;
                }
                else{
                return "<a href='".route('deliveryMan',$order->id)."' class='badge bg-secondary' >Assign Delivery Man</a>"; 
                }
            })
            ->addColumn('statusEdit', function ($order) {
                return "<a href='".route('statusChange',$order->id)."' class='badge bg-secondary' >Change Status</a>"; 
            })
            ->addColumn('location', function ($order) {
                $current  = Country::where('id', $order->current_location_country_id)->first()->name . ", " . City::find($order->current_location_city_id)->name;
                $destination = Country::where('id', $order->delivery_location_country_id)->first()->name . ", " . City::find($order->delivery_location_city_id)->name;
                $origin = Country::where('id', $order->pickup_location_country_id)->first()->name . ", " . City::find($order->pickup_location_city_id)->name;
                $mar = "Origin : " . $origin . "<br>";
                $mar .= "Destination:" . $destination . "<br>";
                $mar .= "Current : " . $current;
                return $mar;
            })
            ->addColumn('date', function ($order) {
                $mar = "<b> Tracking ID : " . $order->tracking_id . "</b><br>";
                $mar .= "Booking Date : " . $order->created_at->format('d M Y, h:i A') . "<br>";
                $mar .= "<span class='text-danger'>Last updated:" . $order->updated_at->format('d M Y, h:i A').'</span>';
                return $mar;
            })
            ->addColumn('tax_code', function ($order) {
                $code = '';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $code = $cinfo->first()->tax_code;
                }
                return $code;
            })
            ->addColumn('document_type', function ($order) {
                $code = '';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $code = $cinfo->first()->doc_type;
                }
                return $code;
            })
            ->addColumn('document_number', function ($order) {
                $code = '';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $code = $cinfo->first()->doc_num;
                }
                return $code;
            })
            ->addColumn('sender', function ($order) {
                $mar = "<b>Name :</b> " . $order->pickup_name . "<br>";
                $mar .= "<b>Phone :</b> " . $order->pickup_phone . "<br>";
                $mar .= "<b>Address:</b>" . $order->pickup_country.'. <br>'.$order->pickup_address1;
                return $mar;
            })
            ->addColumn('receiver', function ($order) {
                $country = Country::where('id',$order->delivery_country)->first();
                $mar = "<b>Name :</b> " . $order->delivery_name . "<br>";
                $mar .= "<b>Phone :</b> " . $order->delivery_phone . "<br>";
                $mar .= "<b>Address:</b>" . $country->name .'. <br>'.$order->delivery_address1;
                return $mar;
            })
            ->addColumn('doc_front', function ($order) {

                $mar = '---';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $info = $cinfo->first();
                $mar = '<a type="button" class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#doc_front' . $order->id . 'Modal" data-toggle="tooltip" title="View Agent Information">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                    </svg>
                </a>';

                $mar .= '
                <div class="modal fade" id="doc_front' . $order->id . 'Modal" tabindex="-1" aria-labelledby="' . $order->id . 'ModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="' . $order->id . '">' . $order->name . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <iframe class="pdf w-100" src="' . asset('uploads/' . $info->doc_front) . '"  height="650"></iframe>    
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
                }
                return $mar;
            })
            ->addColumn('doc_back', function ($order) {

                $mar = '---';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $info = $cinfo->first();
                $mar = '<a type="button" class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#doc_back' . $order->id . 'Modal" data-toggle="tooltip" title="View Agent Information">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                    </svg>
                </a>';

                $mar .= '
                <div class="modal fade" id="doc_back' . $order->id . 'Modal" tabindex="-1" aria-labelledby="' . $order->id . 'ModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="' . $order->id . '">' . $order->name . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <iframe class="pdf w-100" src="' . asset('uploads/' . $info->doc_back) . '"  height="650"></iframe>    
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
                }
                return $mar;
            })
            ->addColumn('viewpdf', function ($order) {

                $mar = '---';
                if($order->cummercial_invoice){
                $mar = '<a type="button" class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#' . $order->id . 'Modal" data-toggle="tooltip" title="View Agent Information">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                    </svg>
                </a>';

                $mar .= '
                <div class="modal fade" id="' . $order->id . 'Modal" tabindex="-1" aria-labelledby="' . $order->id . 'ModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="' . $order->id . '">' . $order->name . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <iframe class="pdf w-100" src="' . asset('uploads/orders/' . $order->cummercial_invoice) . '"  height="650"></iframe>    
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
                }
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
                $mar = number_format($order->val_of_goods, 2) . "<br>";
                return $mar;
            })
            ->addColumn('view', function ($order) {
                $url = route('orders.show', $order->id);
                return '<a style="white-space:nowrap" href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View/ Track</a>';
            })
            // ->addColumn('delete', function ($order) {
            //     $url = route('shipping_rates.destroy', $order->id);
            //     return '<form method="POST" action="' . $url . '">
            //                 <input type="hidden" name = "_token" value = ' . csrf_token() . '>
            //                 <input type="hidden" name = "_method" value ="DELETE">
            //                 <button type="submit" onclick="return confirm(\'Are you sure you wish to delete this entry?\')" class="btn btn-sm btn-danger">Delete</button>
            //             </form>';
            // })
            ->rawColumns(['statusEdit','deliveryMan','destinationAgent','agentInfo','sender','receiver','status', 'location', 'edit', 'view', 'parties', 'date', 'price', 'viewpdf','doc_front','doc_back'])
            ->make(true);
    }

    
    public function allOrdersListDeliveryMan2(Request $request)
    {
        $query = Order::query();
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $start = Carbon::parse($request->startDate)->startOfDay();
            $end = Carbon::parse($request->endDate)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($request->filled('startDate')) {
            $start = Carbon::parse($request->startDate)->startOfDay();
            $query->whereDate('created_at', '=', $start);
        } elseif ($request->filled('endDate')) {
            $end = Carbon::parse($request->endDate)->endOfDay();
            $query->whereDate('created_at', '=', $end);
        }
        $orders = $query->where('dispatcher_id',Auth::user()->id)
        ->whereIn('status',[OrderStatus::DELIVERED,OrderStatus::ACCEPT_BY_AGENT])
        ->orderBy('created_at', 'DESC')->get();

        return Datatables::of($orders)
            ->addIndexColumn()
            ->addColumn('agentInfo', function ($order) {
                $userInfo = User::where('id',$order->agent_id)->first();
                $country = Country::where('id',$userInfo->agent->country_id)->first();
                $mar = "<b>Name : </b>" . $userInfo->name . "<br>";
                $mar .= "<b>Phone:</b>" . $userInfo->agent->phone . "<br>";
                $mar .= "<b>Email:</b>" . $userInfo->email . "<br>";
                $mar .= "<b>Address :</b> ". $country->name.'. '. $userInfo->agent->address1. "<br>";
                $mar .= "<span class='text-danger'><b>Entry Date:</b>" . $order->created_at->format('d M Y, h:i A').'</span>';
                return $mar;
            })
            ->addColumn('status', function ($order) {
                $mar = "<span class= 'badge bg-secondary'>" . $order->status . "</span>";
                return $mar;
            })
            ->addColumn('destinationAgent', function ($order) {
                if($order->destination_agent_id){
                $userInfo = User::where('id',$order->destination_agent_id)->first();
                $country = Country::where('id',$userInfo->agent->country_id)->first();
                $mar = "<b>Name : </b>" . $userInfo->name . "<br>";
                $mar .= "<b>Phone:</b>" . $userInfo->agent->phone . "<br>";
                $mar .= "<b>Email:</b>" . $userInfo->email . "<br>";
                $mar .= "<b>Address :</b> ". $country->name.'. '. $userInfo->agent->address1. "<br>";
                $mar .= "<span class='text-danger'><b>Entry Date:</b>" . Carbon::parse($order->agent_assign_date)->format('d M Y, h:i A').'</span>';
                return $mar;
                }
                else{
                    return '---';
                }
            })
            ->addColumn('deliveryMan', function ($order) {
                if($order->dispatcher_id){
                    $userInfo = User::where('id',$order->dispatcher_id)->first();
                $country = Country::where('id',$userInfo->country)->first();
                $mar = "<b>Name : </b>" . $userInfo->name . "<br>";
                $mar .= "<b>Phone:</b>" . $userInfo->mobile. "<br>";
                $mar .= "<b>Email:</b>" . $userInfo->email. "<br>";
                $mar .= "<b>Assing Date:</b>" . Carbon::parse($order->dispatcher_date)->format('d M Y, h:i A'). "<br>";
                $mar .= "<span class='text-danger'><b>Status:</b>" . $order->delivery_man_status.'</span>';// . "<br>";
                
                // $mar .= "<b>Address :</b> ". $country->name.'. '. $userInfo->agent->address1. "<br>";
                // $mar .= "<span class='text-danger'><b>Entry Date:</b>" . $order->created_at->format('d M Y, h:i A').'</span>';
                return $mar;
                }
                else{
                return "<a href='".route('deliveryMan',$order->id)."' class='badge bg-secondary' >Assign Delivery Man</a>"; 
                }
            })
            ->addColumn('statusEdit', function ($order) {
                return "<a href='".route('statusChange',$order->id)."' class='badge bg-secondary' >Change Status</a>"; 
            })
            ->addColumn('location', function ($order) {
                $current  = Country::where('id', $order->current_location_country_id)->first()->name . ", " . City::find($order->current_location_city_id)->name;
                $destination = Country::where('id', $order->delivery_location_country_id)->first()->name . ", " . City::find($order->delivery_location_city_id)->name;
                $origin = Country::where('id', $order->pickup_location_country_id)->first()->name . ", " . City::find($order->pickup_location_city_id)->name;
                $mar = "Origin : " . $origin . "<br>";
                $mar .= "Destination:" . $destination . "<br>";
                $mar .= "Current : " . $current;
                return $mar;
            })
            ->addColumn('date', function ($order) {
                $mar = "<b> Tracking ID : " . $order->tracking_id . "</b><br>";
                $mar .= "Booking Date : " . $order->created_at->format('d M Y, h:i A') . "<br>";
                $mar .= "<span class='text-danger'>Last updated:" . $order->updated_at->format('d M Y, h:i A').'</span>';
                return $mar;
            })
            ->addColumn('tax_code', function ($order) {
                $code = '';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $code = $cinfo->first()->tax_code;
                }
                return $code;
            })
            ->addColumn('document_type', function ($order) {
                $code = '';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $code = $cinfo->first()->doc_type;
                }
                return $code;
            })
            ->addColumn('document_number', function ($order) {
                $code = '';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $code = $cinfo->first()->doc_num;
                }
                return $code;
            })
            ->addColumn('sender', function ($order) {
                $mar = "<b>Name :</b> " . $order->pickup_name . "<br>";
                $mar .= "<b>Phone :</b> " . $order->pickup_phone . "<br>";
                $mar .= "<b>Address:</b>" . $order->pickup_country.'. <br>'.$order->pickup_address1;
                return $mar;
            })
            ->addColumn('receiver', function ($order) {
                $country = Country::where('id',$order->delivery_country)->first();
                $mar = "<b>Name :</b> " . $order->delivery_name . "<br>";
                $mar .= "<b>Phone :</b> " . $order->delivery_phone . "<br>";
                $mar .= "<b>Address:</b>" . $country->name .'. <br>'.$order->delivery_address1;
                return $mar;
            })
            ->addColumn('doc_front', function ($order) {

                $mar = '---';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $info = $cinfo->first();
                $mar = '<a type="button" class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#doc_front' . $order->id . 'Modal" data-toggle="tooltip" title="View Agent Information">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                    </svg>
                </a>';

                $mar .= '
                <div class="modal fade" id="doc_front' . $order->id . 'Modal" tabindex="-1" aria-labelledby="' . $order->id . 'ModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="' . $order->id . '">' . $order->name . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <iframe class="pdf w-100" src="' . asset('uploads/' . $info->doc_front) . '"  height="650"></iframe>    
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
                }
                return $mar;
            })
            ->addColumn('doc_back', function ($order) {

                $mar = '---';
                $cinfo = WalkInCustomer::where('id',$order->walk_in_customer_id);
                if($cinfo->exists()){
                    $info = $cinfo->first();
                $mar = '<a type="button" class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#doc_back' . $order->id . 'Modal" data-toggle="tooltip" title="View Agent Information">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                    </svg>
                </a>';

                $mar .= '
                <div class="modal fade" id="doc_back' . $order->id . 'Modal" tabindex="-1" aria-labelledby="' . $order->id . 'ModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="' . $order->id . '">' . $order->name . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <iframe class="pdf w-100" src="' . asset('uploads/' . $info->doc_back) . '"  height="650"></iframe>    
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
                }
                return $mar;
            })
            ->addColumn('viewpdf', function ($order) {

                $mar = '---';
                if($order->cummercial_invoice){
                $mar = '<a type="button" class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#' . $order->id . 'Modal" data-toggle="tooltip" title="View Agent Information">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                    </svg>
                </a>';

                $mar .= '
                <div class="modal fade" id="' . $order->id . 'Modal" tabindex="-1" aria-labelledby="' . $order->id . 'ModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="' . $order->id . '">' . $order->name . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <iframe class="pdf w-100" src="' . asset('uploads/orders/' . $order->cummercial_invoice) . '"  height="650"></iframe>    
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
                }
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
                $mar = number_format($order->val_of_goods, 2) . "<br>";
                return $mar;
            })
            ->addColumn('view', function ($order) {
                $url = route('orders.show', $order->id);
                return '<a style="white-space:nowrap" href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View/ Track</a>';
            })
            // ->addColumn('delete', function ($order) {
            //     $url = route('shipping_rates.destroy', $order->id);
            //     return '<form method="POST" action="' . $url . '">
            //                 <input type="hidden" name = "_token" value = ' . csrf_token() . '>
            //                 <input type="hidden" name = "_method" value ="DELETE">
            //                 <button type="submit" onclick="return confirm(\'Are you sure you wish to delete this entry?\')" class="btn btn-sm btn-danger">Delete</button>
            //             </form>';
            // })
            ->rawColumns(['statusEdit','deliveryMan','destinationAgent','agentInfo','sender','receiver','status', 'location', 'edit', 'view', 'parties', 'date', 'price', 'viewpdf','doc_front','doc_back'])
            ->make(true);
    }

    

    public function dispatchOrders()
    {
        // dd(Auth::user()->dispatcher->city);
        return view('dispatcher.orders.index');
    }

    /**
     * datatable for dispatcher orders
     */
    public function dispatchOrdersList()
    {
        $orders = Order::where('pickup_city', 'LIKE', '%' . Auth::user()->dispatcher->city . '%')->orWhere('delivery_city', 'LIKE', '%' . Auth::user()->dispatcher->city . '%')->orderBy('created_at', 'DESC')->get();

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
                $mar = number_format($order->shipping_rate->price, 2) . "<br>";
                return $mar;
            })
            ->addColumn('view', function ($order) {
                if ($order->status != 'unpaid') {
                    $url = route('dispatcher.accept', ['order_track_id' => $order->tracking_id]);
                    return '<a href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> Pickup/ Accept</a>';
                } else {
                    // $url = '#';
                    $url = route('dispatcher.accept', ['order_track_id' => $order->tracking_id]);
                    return '<a href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> Pickup/ Accept</a>';
                }
            })
            // ->addColumn('delete', function ($order) {
            //     $url = route('shipping_rates.destroy', $order->id);
            //     return '<form method="POST" action="' . $url . '">
            //                 <input type="hidden" name = "_token" value = ' . csrf_token() . '>
            //                 <input type="hidden" name = "_method" value ="DELETE">
            //                 <button type="submit" onclick="return confirm(\'Are you sure you wish to delete this entry?\')" class="btn btn-sm btn-danger">Delete</button>
            //             </form>';
            // })
            ->rawColumns(['status', 'location', 'view', 'parties', 'date', 'price'])
            ->make(true);
    }


    public function agentsOrders()
    {
        // dd(Auth::user()->dispatcher->city);
        return view('agents.orders.index');
    }

    public function acceptOrderAgentFromDeliveryMan($id)
    {
        Order::where('id',$id)->update([
            'agent_receive_status' => 1,
            'status' => OrderStatus::ACCEPT_BY_AGENT
        ]);
        return redirect()->route('agentDeliveryManOrder')->with(['message' => 'Order Receive Successful.', 'message_type' => 'success']);
    }

    public function agentDeliveryManOrder()
    {
        return view('agents.orders.agentDeliveryManOrder');
    }

    public function agentDeliveredOrder()
    {
        return view('agents.orders.agentDeliveredOrder');
    }

    /**
     * datatable for dispatcher orders
     */
    public function agentsOrdersList(Request $request)
    {
        $query = Order::query();
        // $cityName = Auth::user()->agent->city->name;

        // $query->where(function ($q) use ($cityName) {
        //     $q->where('pickup_city', 'LIKE', '%' . $cityName . '%')
        //         ->orWhere('delivery_city', 'LIKE', '%' . $cityName . '%');
        // });

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = Carbon::parse($request->startDate)->startOfDay();
            $endDate = Carbon::parse($request->endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($request->filled('startDate')) {
            $startDate = Carbon::parse($request->startDate)->startOfDay();
            $query->whereDate('created_at', '=', $startDate);
        } elseif ($request->filled('endDate')) {
            $endDate = Carbon::parse($request->endDate)->endOfDay();
            $query->whereDate('created_at', '=', $endDate);
        }

        $orders = $query->where('agent_id',Auth::user()->id)->orderBy('created_at', 'DESC')->get();

        return Datatables::of($orders)
            ->addIndexColumn()
            ->addColumn('status', function ($order) {
                return "<span class='badge bg-secondary'>" . $order->status . "</span>";
            })
            ->addColumn('sender', function ($order) {
                $mar = "<b>Name :</b> " . $order->pickup_name . "<br>";
                $mar .= "<b>Phone :</b> " . $order->pickup_phone . "<br>";
                $mar .= "<b>Address:</b>" . $order->pickup_country.'. <br>'.$order->pickup_address1;
                return $mar;
            })
            ->addColumn('receiver', function ($order) {
                $country = Country::where('id',$order->delivery_country)->first();
                $mar = "<b>Name :</b> " . $order->delivery_name . "<br>";
                $mar .= "<b>Phone :</b> " . $order->delivery_phone . "<br>";
                $mar .= "<b>Address:</b>" . $country->name .'. <br>'.$order->delivery_address1;
                return $mar;
            })
            ->addColumn('deliveryMan', function ($order) {
                if($order->dispatcher_id){
                    $userInfo = User::where('id',$order->dispatcher_id)->first();
                $country = Country::where('id',$userInfo->country)->first();
                $mar = "<b>Name : </b>" . $userInfo->name . "<br>";
                $mar .= "<b>Phone:</b>" . $userInfo->mobile. "<br>";
                $mar .= "<b>Email:</b>" . $userInfo->email. "<br>";
                $mar .= "<b>Assing Date:</b>" . Carbon::parse($order->dispatcher_date)->format('d M Y, h:i A'). "<br>";
                $mar .= "<span class='text-danger'><b>Status:</b>" . $order->delivery_man_status.'</span>';// . "<br>";
                
                // $mar .= "<b>Address :</b> ". $country->name.'. '. $userInfo->agent->address1. "<br>";
                // $mar .= "<span class='text-danger'><b>Entry Date:</b>" . $order->created_at->format('d M Y, h:i A').'</span>';
                return $mar;
                }
                else{
                return "---";
                }
            })
            ->addColumn('location', function ($order) {
                $current  = Country::find($order->current_location_country_id)->name . ", " . City::find($order->current_location_city_id)->name;
                $destination = Country::find($order->delivery_location_country_id)->name . ", " . City::find($order->delivery_location_city_id)->name;
                $origin = Country::find($order->pickup_location_country_id)->name . ", " . City::find($order->pickup_location_city_id)->name;
                return "Origin: " . $origin . "<br>Destination: " . $destination . "<br>Current: " . $current;
            })
            ->addColumn('date', function ($order) {
                return "<b>Tracking ID: " . $order->tracking_id . "</b><br>Created at: " . $order->created_at->format('d M Y h:i:s A');
            })
            ->addColumn('parties', function ($order) {
                return "Sender: " . $order->pickup_name . "<br>Receiver: " . $order->delivery_name;
            })
            ->addColumn('price', function ($order) {
                return fromEuroView(auth()->user()->currency_id ?? 0, $order->val_of_goods);
            })
            ->addColumn('view', function ($order) {
                $url = route('agent.accept', ['order_track_id' => $order->tracking_id]);
                return '<a href="' . $url . '" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Pickup/ Accept</a>';
            })
            ->addColumn('print', function ($order) {
                $url = route('orders.show', $order->id);
                return '<a href="' . $url . '" class="btn btn-info btn-sm"><i class="fa fa-eye"></i>View/Track</a>';
            })
            ->rawColumns(['sender','receiver','deliveryMan','status', 'location', 'view', 'parties', 'date', 'price', 'print'])
            ->make(true);
    }
    public function agentsOrdersListDeliveryMan(Request $request)
    {
        $query = Order::query();

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = Carbon::parse($request->startDate)->startOfDay();
            $endDate = Carbon::parse($request->endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($request->filled('startDate')) {
            $startDate = Carbon::parse($request->startDate)->startOfDay();
            $query->whereDate('created_at', '=', $startDate);
        } elseif ($request->filled('endDate')) {
            $endDate = Carbon::parse($request->endDate)->endOfDay();
            $query->whereDate('created_at', '=', $endDate);
        }

        $orders = $query->where('delivery_man_status','Delivered')
        ->whereIn('status',[OrderStatus::DELIVERED,OrderStatus::ACCEPT_BY_AGENT])
        ->orderBy('created_at', 'DESC')->get();

        return Datatables::of($orders)
            ->addIndexColumn()
            ->addColumn('status', function ($order) {
                $msg = "<span class='badge bg-secondary'>" . $order->status . "</span>";

                if($order->agent_receive_status==0){
                    $msg .= "<br> <br> <a href='". route('acceptOrderAgentFromDeliveryMan',$order->id) ."' class='badge bg-secondary'> Accept Order </a>";
                }
                return $msg;
            })
            ->addColumn('sender', function ($order) {
                $mar = "<b>Name :</b> " . $order->pickup_name . "<br>";
                $mar .= "<b>Phone :</b> " . $order->pickup_phone . "<br>";
                $mar .= "<b>Address:</b>" . $order->pickup_country.'. <br>'.$order->pickup_address1;
                return $mar;
            })
            ->addColumn('receiver', function ($order) {
                $country = Country::where('id',$order->delivery_country)->first();
                $mar = "<b>Name :</b> " . $order->delivery_name . "<br>";
                $mar .= "<b>Phone :</b> " . $order->delivery_phone . "<br>";
                $mar .= "<b>Address:</b>" . $country->name .'. <br>'.$order->delivery_address1;
                return $mar;
            })
            ->addColumn('deliveryMan', function ($order) {
                if($order->dispatcher_id){
                    $userInfo = User::where('id',$order->dispatcher_id)->first();
                $country = Country::where('id',$userInfo->country)->first();
                $mar = "<b>Name : </b>" . $userInfo->name . "<br>";
                $mar .= "<b>Phone:</b>" . $userInfo->mobile. "<br>";
                $mar .= "<b>Email:</b>" . $userInfo->email. "<br>";
                $mar .= "<b>Assing Date:</b>" . Carbon::parse($order->dispatcher_date)->format('d M Y, h:i A'). "<br>";
                $mar .= "<span class='text-danger'><b>Status:</b>" . $order->delivery_man_status.'</span>';// . "<br>";
                
                // $mar .= "<b>Address :</b> ". $country->name.'. '. $userInfo->agent->address1. "<br>";
                // $mar .= "<span class='text-danger'><b>Entry Date:</b>" . $order->created_at->format('d M Y, h:i A').'</span>';
                return $mar;
                }
                else{
                return "---";
                }
            })
            ->addColumn('location', function ($order) {
                $current  = Country::find($order->current_location_country_id)->name . ", " . City::find($order->current_location_city_id)->name;
                $destination = Country::find($order->delivery_location_country_id)->name . ", " . City::find($order->delivery_location_city_id)->name;
                $origin = Country::find($order->pickup_location_country_id)->name . ", " . City::find($order->pickup_location_city_id)->name;
                return "Origin: " . $origin . "<br>Destination: " . $destination . "<br>Current: " . $current;
            })
            ->addColumn('date', function ($order) {
                return "<b>Tracking ID: " . $order->tracking_id . "</b><br>Created at: " . $order->created_at->format('d M Y h:i:s A');
            })
            ->addColumn('parties', function ($order) {
                return "Sender: " . $order->pickup_name . "<br>Receiver: " . $order->delivery_name;
            })
            ->addColumn('price', function ($order) {
                return fromEuroView(auth()->user()->currency_id ?? 0, $order->val_of_goods);
            })
            ->addColumn('view', function ($order) {
                $url = route('agent.accept', ['order_track_id' => $order->tracking_id]);
                return '<a href="' . $url . '" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Pickup/ Accept</a>';
            })
            ->addColumn('print', function ($order) {
                $url = route('orders.show', $order->id);
                return '<a href="' . $url . '" class="btn btn-info btn-sm"><i class="fa fa-eye"></i>View/Track</a>';
            })
            ->rawColumns(['sender','receiver','deliveryMan','status', 'location', 'view', 'parties', 'date', 'price', 'print'])
            ->make(true);
    }
    public function agentsOrdersListDelivered(Request $request)
    {
        $query = Order::query();

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = Carbon::parse($request->startDate)->startOfDay();
            $endDate = Carbon::parse($request->endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($request->filled('startDate')) {
            $startDate = Carbon::parse($request->startDate)->startOfDay();
            $query->whereDate('created_at', '=', $startDate);
        } elseif ($request->filled('endDate')) {
            $endDate = Carbon::parse($request->endDate)->endOfDay();
            $query->whereDate('created_at', '=', $endDate);
        }

        $orders = $query->where('status',OrderStatus::CUSTOMER_RECEIVED)
        ->orderBy('created_at', 'DESC')->get();

        return Datatables::of($orders)
            ->addIndexColumn()
            ->addColumn('status', function ($order) {
                $msg = "<span class='badge bg-secondary'>" . $order->status . "</span>";

                if($order->agent_receive_status==0){
                    $msg .= "<br> <br> <a href='". route('acceptOrderAgentFromDeliveryMan',$order->id) ."' class='badge bg-secondary'> Accept Order </a>";
                }
                return $msg;
            })
            ->addColumn('sender', function ($order) {
                $mar = "<b>Name :</b> " . $order->pickup_name . "<br>";
                $mar .= "<b>Phone :</b> " . $order->pickup_phone . "<br>";
                $mar .= "<b>Address:</b>" . $order->pickup_country.'. <br>'.$order->pickup_address1;
                return $mar;
            })
            ->addColumn('receiver', function ($order) {
                $country = Country::where('id',$order->delivery_country)->first();
                $mar = "<b>Name :</b> " . $order->delivery_name . "<br>";
                $mar .= "<b>Phone :</b> " . $order->delivery_phone . "<br>";
                $mar .= "<b>Address:</b>" . $country->name .'. <br>'.$order->delivery_address1;
                return $mar;
            })
            ->addColumn('deliveryMan', function ($order) {
                if($order->dispatcher_id){
                    $userInfo = User::where('id',$order->dispatcher_id)->first();
                $country = Country::where('id',$userInfo->country)->first();
                $mar = "<b>Name : </b>" . $userInfo->name . "<br>";
                $mar .= "<b>Phone:</b>" . $userInfo->mobile. "<br>";
                $mar .= "<b>Email:</b>" . $userInfo->email. "<br>";
                $mar .= "<b>Assing Date:</b>" . Carbon::parse($order->dispatcher_date)->format('d M Y, h:i A'). "<br>";
                $mar .= "<span class='text-danger'><b>Status:</b>" . $order->delivery_man_status.'</span>';// . "<br>";
                
                // $mar .= "<b>Address :</b> ". $country->name.'. '. $userInfo->agent->address1. "<br>";
                // $mar .= "<span class='text-danger'><b>Entry Date:</b>" . $order->created_at->format('d M Y, h:i A').'</span>';
                return $mar;
                }
                else{
                return "---";
                }
            })
            ->addColumn('location', function ($order) {
                $current  = Country::find($order->current_location_country_id)->name . ", " . City::find($order->current_location_city_id)->name;
                $destination = Country::find($order->delivery_location_country_id)->name . ", " . City::find($order->delivery_location_city_id)->name;
                $origin = Country::find($order->pickup_location_country_id)->name . ", " . City::find($order->pickup_location_city_id)->name;
                return "Origin: " . $origin . "<br>Destination: " . $destination . "<br>Current: " . $current;
            })
            ->addColumn('date', function ($order) {
                return "<b>Tracking ID: " . $order->tracking_id . "</b><br>Created at: " . $order->created_at->format('d M Y h:i:s A');
            })
            ->addColumn('parties', function ($order) {
                return "Sender: " . $order->pickup_name . "<br>Receiver: " . $order->delivery_name;
            })
            ->addColumn('price', function ($order) {
                return fromEuroView(auth()->user()->currency_id ?? 0, $order->val_of_goods);
            })
            ->addColumn('view', function ($order) {
                $url = route('agent.accept', ['order_track_id' => $order->tracking_id]);
                return '<a href="' . $url . '" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Pickup/ Accept</a>';
            })
            ->addColumn('print', function ($order) {
                $url = route('orders.show', $order->id);
                return '<a href="' . $url . '" class="btn btn-info btn-sm"><i class="fa fa-eye"></i>View/Track</a>';
            })
            ->rawColumns(['sender','receiver','deliveryMan','status', 'location', 'view', 'parties', 'date', 'price', 'print'])
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
        return view('users.orders.new', ['locations' => $locations]);
    }

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
            'origin_id' => 'required|numeric|exists:locations,id',
            'dest_id' => 'required|numeric|exists:locations,id',
            'rate' => 'required',
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
            'type.*' => 'required|string',
            'len.*' => 'required',
            'width.*' => 'required',
            'height.*' => 'required',
            'weight.*' => 'required',
            'count.*' => 'required',
        ]);
        try {
            DB::beginTransaction();
            $l = new Order;
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
                $item->save();
            }
            DB::commit();
            return redirect()->route('orders.index')->with(['message' => 'Order saved You can proceed to pay', 'message_type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
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
        return view('users.orders.show', ['order' => $order]);
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
        return view('users.orders.edit', ['order' => $order, 'dest_str' => $dest_str, 'origin_str' => $origin_str]);
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


    // public function cummercialInvoice(){

    // $path = public_path('cu', )

    // }

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
