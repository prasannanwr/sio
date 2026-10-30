<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class CustomerController extends Controller
{

    public function customerReports()
    {
        $rep = [];
        $rep['total_orders'] = Order::count();
        $rep['total_unpaid_orders'] = Order::where('status', 'unpaid')->where('customer_id', Auth::user()->customer->id)->count();
        $rep['totsl_paid_orders'] = Order::where('status', '!=', 'unpaid')->where('customer_id', Auth::user()->customer->id)->count();
        $rep['total_in_transit_orders'] = Order::where('status', 'in_transit')->where('customer_id', Auth::user()->customer->id)->count();
        $rep['total_delivered_orders'] = Order::where('status', 'delivered')->where('customer_id', Auth::user()->customer->id)->count();
        $rep['total_cancelled_orders'] = Order::where('status', 'cancelled')->where('customer_id', Auth::user()->customer->id)->count();

        $rep['total_payments'] = Payment::where('status', 'done')->where('customer_id', Auth::user()->customer->id)->count();
        $rep['total_payments_pending'] = Payment::where('status', 'pending')->where('customer_id', Auth::user()->customer->id)->count();
        $rep['total_payments_failed'] = Payment::where('status', 'failed')->where('customer_id', Auth::user()->customer->id)->count();
        $rep['total_payments_value'] = Payment::where('status', 'done')->where('customer_id', Auth::user()->customer->id)->sum('amt_paid');

        return $rep;
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
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
