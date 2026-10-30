<?php

namespace App\Http\Controllers;

use App\Mail\DispacherMail;
use App\Models\Dispatcher;
use App\Models\Order;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use App\Mail\PaymentEmail;
use App\Mail\SignUpEmail;
use App\Models\Batchlog;
use App\Models\BatchorderLog;
use App\Models\City;
use App\Models\Country;
use App\Models\OrderBatch;
use App\Models\State;
use App\Models\SmtpConfiguration;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use LDAP\Result;

class DispatcherController extends Controller
{
    public static function canDispatch()
    {
        // dd(request()->user());
        if (Auth::user() && Auth::user()->user_type != 'admin' && Auth::user()->user_type != 'dispatcher') {
            return abort(402, 'You do not have access to this functionality');
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * hompage for the various dispatcher settings
     */

    public function settings()
    {
        return view('dispatcher.settings.index');
    }

    /**
     * hompage for the accepting orders
     */

    public function accept()
    {
        return view('dispatcher.accept');
    }

    /**
     * hompage for the accepting orders
     */

    public function accept_search(Request $request)
    {
        $request->validate([
            'order_track_id' => 'required|exists:orders,tracking_id'
        ]);

        $o = Order::where('tracking_id', $request->order_track_id)->first();
        if ($o) {
            return view('dispatcher.accept')->with(['order' => $o]);
        } else {
            return back()->with('message', 'Order not Found');
        }
    }


    public function assigendOrder(Request $request, $order_id)
    {
        try {
            $o = Order::where('id', $order_id)->first();
            $subTotal = $o->val_of_goods;
            $total =  (float) $subTotal + (float) $o->shipping_cost;
            if (getAccountbalances(Auth::id())['balance'] >= $total) {
                DB::beginTransaction();
                $batch = new OrderBatch();
                $batch->name = 'BA-' . time() . '-' . $request->batch_ ?? '';
                $batch->user_id = Auth::user()->id;
                $batch->dispatcher_id = $request->dispatcher_id;
                $batch->save();
                $batch->batch_tracking_id = 'BA-' . rand(99999999, 100000000) . '_' . $batch->id;
                $batch->save();


                Batchlog::create([
                    'ship_from_country_id' => $o->pickup_location_country_id,
                    'ship_from_city_id' => $o->pickup_location_city_id,
                    'ship_to_country_id' => $o->delivery_location_country_id,
                    'ship_to_city_id' => $o->delivery_location_city_id,
                    'current_location_county_id' => $o->current_location_country_id,
                    'current_location_city_id' => $o->current_location_city_id,
                    'batch_id' => $batch->id,
                ]);

                $batchorder_log = new BatchorderLog();
                $batchorder_log->batch_id = $batch->id;
                $batchorder_log->order_id = $o->id;
                $batchorder_log->save();

                Order::where('id', $order_id)->update([
                    'status' => 'assigned',
                    'batch_id' => $batch->id,
                    'dispatcher_id' => $request->dispatcher_id,
                    'pickup_time' => Carbon::now(),
                ]);
                
                DB::commit();
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
    public function orderAccept(Request $request, $order_id)
    {
        // return $request;
        try {

            $o = Order::where('id', $order_id)->first();
            $subTotal = $o->val_of_goods;
            $total =  (float) $subTotal + (float) $o->shipping_cost;
            if (getAccountbalances(Auth::id())['balance'] >= $total) {
                DB::beginTransaction();
              

                Order::where('id', $order_id)->update([
                    'status' => 'placed',
                    'pickup_time' => Carbon::now(),
                ]);

                $u = updateAccountBalance(Auth::id(), ($total), $o->tracking_id, 'credit', 'Order ' . $o->tracking_id);

                DB::commit();
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
            $subTotal = $o->val_of_goods;
            OrderBatch::where('id', $o->batch_id)->update([
                'status' =>  'delivered'
            ]);
            Order::where('id', $order_id)->update([
                'status' => 'delivered',
                'delivery_time' => Carbon::now(),
                'current_location_id' => $o->delivery_location_city_id
            ]);
            $c = updateAccountBalance(Auth::id(), ($subTotal * 0.015), $o->tracking_id, 'debit', 'Order Commision ' . $o->tracking_id);
            return back()->with(['message' => 'Order Marked As Picked Up', 'message_type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {

            $d = Dispatcher::where('user_id', Auth::id())->first();
            if ($d) {
                return view('dispatcher.settings.profile', ['dispatcher' => $d]);
            } else {
                $d = Dispatcher::create([
                    'user_id' => Auth::id(),
                    'name' => Auth::user()->name
                ]);
                return view('dispatcher.settings.profile', ['dispatcher' => $d]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage());
        }
    }

    public function createDispatcher()
    {
        return view('dispatcher.settings.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'agency_type' => ['required'],
            'business_name' => ['required'],
            'tax_id_code' => ['required'],
            'vat_no' => ['required'],
            'pec' => ['required'],
            'sdi' => ['required'],
            'phone' => ['required'],
            'address1' => ['required'],
            'zip' => ['required'],
            'residential_country' => ['required'],
            'residential_state' => ['required'],
            'residential_city' => ['required'],
        ]);


        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'dispatcher',
                'country' => $request->residential_country,
                'blocked' => true,
            ]);

            Dispatcher::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'phone' => $request->phone,
                'phone_alt' => $request->phone_alt,
                'address1' => $request->address1,
                'address2' => $request->address2,
                'zip' => $request->zip,
                'city' => City::find($request->residential_city)->name ?? '',
                'state' => State::find($request->residential_state)->name ?? '',
                'country' => Country::find($request->residential_country)->name ?? '',
                'agency_type' => $request->agency_type ?? '',
                'business_name' => $request->business_name ?? '',
                'tax_id_code' => $request->tax_id_code ?? '',
                'vat_no' => $request->vat_no ?? '',
                'pec' => $request->pec ?? '',
                'sdi' => $request->sdi ?? '',
                'city_id' => $request->residential_city ?? '',
                'state_id' => $request->residential_state ?? '',
                'country_id' => $request->residential_country ?? ''
            ]);
            return $user;
        });

        // Use the active SMTP configuration (managed in smtp-configurations) instead of the .env mailer.
        SmtpConfiguration::applyActiveIfAny();
        Mail::purge('smtp');

        Mail::to($user->email)->send(new SignUpEmail($user, $request->password));

        return redirect()->route('allUsers')->with('message', "Dispatcher Created Successfully !")->with('message_type', "success");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Dispatcher  $dispatcher
     * @return \Illuminate\Http\Response
     */
    public function show(Dispatcher $dispatcher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Dispatcher  $dispatcher
     * @return \Illuminate\Http\Response
     */
    public function edit(Dispatcher $dispatcher)
    {
        $dispatcher = Dispatcher::find($dispatcher->id);
        return view('dispatcher.settings.edit', compact('dispatcher'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dispatcher  $dispatcher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dispatcher $dispatcher)
    {
        $dispatcher = Dispatcher::find($dispatcher->id);
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'phone_alt' => 'nullable|string',
            'address1' => 'required|string',
            'address2' => 'nullable|string',
            'zip' => 'required',
            'residential_country' => 'required',
            'residential_state' => 'required',
            'residential_city' => 'required',
            'agency_type' => 'required',
            'tax_id_code' => 'required',
        ]);
        try {
            $dispatcher->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'phone_alt' => $request->phone_alt,
                'address1' => $request->address1,
                'address2' => $request->address2,
                'zip' => $request->zip,
                'city' => City::find($request->residential_city)->name ?? '',
                'state' => State::find($request->residential_state)->name ?? '',
                'country' => Country::find($request->residential_country)->name ?? '',
                'agency_type' => $request->agency_type ?? '',
                'business_name' => $request->business_name ?? '',
                'tax_id_code' => $request->tax_id_code ?? '',
                'vat_no' => $request->vat_no ?? '',
                'pec' => $request->pec ?? '',
                'sdi' => $request->sdi ?? '',
                'city_id' => $request->residential_city ?? '',
                'state_id' => $request->residential_state ?? '',
                'country_id' => $request->residential_country ?? ''
            ]);
            return back()->with(['message' => 'Record Updated Successfu', 'message_type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Dispatcher  $dispatcher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dispatcher $dispatcher)
    {
        //
    }
}
