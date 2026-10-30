<?php

namespace App\Http\Controllers;

use App\Models\IntlFundTransferOrder;
use App\Models\IntlFundsTransferRates;
use App\Models\WalkInCustomer;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class IntlFundTransferOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dispatcher.intl_fund.index');
    }

    public function dispatchIntlFundOrders()
    {
        // dd(Auth::user()->dispatcher->city);
        return view('dispatcher.intl_fund.index');
    }

    /**
     * datatable for dispatcher orders
     */
    public function dispatchIntlFundOrdersList()
    {
        $orders = IntlFundTransferOrder::where('dispatcher_id', Auth::user()->dispatcher->id)->orderBy('created_at', 'DESC')->get();

        return Datatables::of($orders)
            ->addIndexColumn()
            ->addColumn('status', function ($order) {
                $mar = "<span class= 'badge bg-secondary'>" . $order->tx_status . "</span>";


                return $mar;
            })
            ->addColumn('sender', function ($order) {
                $mar = "Tax Id : " . $order->walk_in_customer->tax_code . "<br>";
                $mar .= "Name:" . $order->walk_in_customer->surname . ' ' . $order->walk_in_customer->name . "<br>";
                $mar .= "Country:" . $order->s_country . "<br>";
                $mar .= "Currency:" . $order->s_currency . "<br>";
                $mar .= "Amount Sent:" . $order->s_amount . "<br>";
                return $mar;
            })
            ->addColumn('date', function ($order) {
                $mar = "<b> Tracking ID : " . $order->tracking_id . "</b><br><br>";
                $mar .= "Created at : " . $order->created_at . "<br>";
                $mar .= "Last updated:" . $order->updated_at;
                return $mar;
            })
            ->addColumn('reciever', function ($order) {
                $mar = "Account name : " . $order->rx_bank_account_name . "<br>";
                $mar .= "Account Number:" . $order->rx_bank_account_number . "<br>";
                $mar .= "Country:" . $order->rx_country . "<br>";
                $mar .= "Currency:" . $order->rx_currency . "<br>";
                $mar .= "Amount Recieved:" . $order->rx_amount . "<br>";
                return $mar;
            })

            ->addColumn('view', function ($order) {
                $url = route('intl_fund_trasfer_order.show', $order->id);
                return '<a href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View </a>';
            })
            ->rawColumns(['status', 'sender', 'view', 'reciever', 'date'])
            ->make(true);
    }

    public function adminIntlFundOrders()
    {
        // dd(Auth::user()->dispatcher->city);
        return view('admin.intl_fund.index');
    }

    /**
     * datatable for dispatcher orders
     */
    public function adminIntlFundOrdersList()
    {
        $orders = IntlFundTransferOrder::orderBy('created_at', 'DESC')->get();

        return Datatables::of($orders)
            ->addIndexColumn()
            ->addColumn('status', function ($order) {
                $mar = "<span class= 'badge bg-secondary'>" . $order->tx_status . "</span><br>";
                if ($order->tx_status != 'done') {
                    $url = route('approveIntlFundTransfer');
                    $mar .= '<br><form method="POST" action="' . $url . '">
                            <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                            <input type="hidden" name = "trans_id" value ="' . $order->id . '">
                            <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-primary">Approve Transfer</button>
                        </form>';
                } else {
                    $url = route('unapproveIntlFundTransfer');
                    $mar .= '<br><form method="POST" action="' . $url . '">
                            <input type="hidden" name = "_token" value = ' . csrf_token() . '>
                            <input type="hidden" name = "trans_id" value ="' . $order->id . '">
                            <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-danger">Unapprove Transfer</button>
                        </form>';
                }
                return $mar;
            })
            ->addColumn('sender', function ($order) {
                $mar = "Tax Id : " . $order->walk_in_customer->tax_code . "<br>";
                $mar .= "Name:" . $order->walk_in_customer->surname . ' ' . $order->walk_in_customer->name . "<br>";
                $mar .= "Country:" . $order->s_country . "<br>";
                $mar .= "Currency:" . $order->s_currency . "<br>";
                $mar .= "Amount Sent:" . $order->s_amount . "<br>";
                return $mar;
            })
            ->addColumn('date', function ($order) {
                $mar = "<b> Tracking ID : " . $order->tracking_id . "</b><br><br>";
                $mar .= "Created at : " . $order->created_at . "<br>";
                $mar .= "Last updated:" . $order->updated_at;
                return $mar;
            })
            ->addColumn('reciever', function ($order) {
                $mar = "Account name : " . $order->rx_bank_account_name . "<br>";
                $mar .= "Account Number:" . $order->rx_bank_account_number . "<br>";
                $mar .= "Country:" . $order->rx_country . "<br>";
                $mar .= "Currency:" . $order->rx_currency . "<br>";
                $mar .= "Amount Recieved:" . $order->rx_amount . "<br>";
                return $mar;
            })

            ->addColumn('view', function ($order) {
                $url = route('intl_fund_trasfer_order.show', $order->id);
                return '<a href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View </a>';
            })
            ->rawColumns(['status', 'sender', 'view', 'reciever', 'date'])
            ->make(true);
    }

    public function approveIntlFundTransfer(Request $request)
    {
        $request->validate([
            'trans_id' => 'required'
        ]);

        try {
            IntlFundTransferOrder::where('id', $request->trans_id)->update([
                'tx_status' => 'done'
            ]);
            return back()->with(['message' => 'Transfer Approved', 'message_type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    public function unapproveIntlFundTransfer(Request $request)
    {
        $request->validate([
            'trans_id' => 'required'
        ]);

        try {
            IntlFundTransferOrder::where('id', $request->trans_id)->update([
                'tx_status' => 'rejected'
            ]);
            return back()->with(['message' => 'Transfer Rejected', 'message_type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dispatcher.intl_fund.new');
    }

    public function ratesFetch(Request $request)
    {
        if (!empty($request->s_country) && !empty($request->rx_country)) {
            $rate = IntlFundsTransferRates::where('s_country', $request->s_country)
                ->where('rx_country', $request->rx_country)
                ->where('min_amt', '<=', $request->rx_amount)
                ->where('max_amt', '>=', $request->rx_amount)
                ->where('status', 1)->first();

            $rate->req = $request->all();
            return json_encode($rate);
        }
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
            'tax_code_' => 'required',
            'surname_' => 'required',
            'name_' => 'required',
            'gender_' => 'required',
            'dob_' => 'required|date',
            'doc_type_' => 'required',
            'doc_num_' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'doc_front_' => 'file|mimes:jpg,jpeg,png,pdf',
            'doc_back_' => 'file|mimes:jpg,jpeg,png,pdf',
            's_country' => 'required',
            'rx_country' => 'required',
            'rx_amount' => 'required',
            's_currency' => 'required',
            'rx_currency' => 'required',
            'rate_id' => 'required',
            'rx_bank' => 'required',
            'rx_bank_routing_code' => 'required',
            'rx_bank_account_no' => 'required',
            'rx_bank_account_name' => 'required',
            'rx_phone' => 'required',
            'rx_email' => 'nullable|email'
        ]);
        try {
            DB::beginTransaction();
            $cust = WalkInCustomer::where('tax_code', $request->tax_code_)->first();
            // dd($request->file('doc_front_'));
            if (!$cust) {
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
                        'doc_back' => $docBackFileName,
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'address' => $request->address
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
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'address' => $request->address
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
                        'doc_back' => $docBackFileName,
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'address' => $request->address
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
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'address' => $request->address
                    ]);
                }
            }
            $l = new IntlFundTransferOrder;
            $l->customer_id = Auth::id();
            $l->walk_in_customer_id = $cust->id;
            $l->dispatcher_id = Auth::user()->dispatcher->id ?? null;
            $l->e_u_funds_transfer_rate_id = $request->rate_id;
            $l->rx_bank_name = $request->rx_bank;
            $l->rx_bank_routing_no = $request->rx_bank_routing_code;
            $l->rx_bank_account_name = $request->rx_bank_account_name;
            $l->rx_bank_account_number = $request->rx_bank_account_no;
            $l->rx_country = $request->rx_country;
            $l->s_country = $request->s_country;
            $l->rx_currency = $request->rx_currency;
            $l->s_currency = $request->s_currency;
            $l->s_amount = $request->s_amount;
            $l->rx_amount = $request->rx_amount;
            $l->rx_phone = $request->rx_phone;
            $l->rx_email = $request->rx_email;
            $l->tracking_id = 'SIO-INTL' . rand(19999999, 99999999);
            $l->save();
            DB::commit();
            return redirect()->route('intl_fund_trasfer_order.index')->with(['message' => 'Order saved You can proceed to pay', 'message_type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\IntlFundTransferOrder  $intlFundTransferOrder
     * @return \Illuminate\Http\Response
     */
    public function show($intlFundTransferOrder)
    {
        try {
            $o = IntlFundTransferOrder::where('id', $intlFundTransferOrder)->first();

            if ($o) {

                //calculate costs
                $main = $o->s_amount;
                $comm = ($o->rate->calc == 'perc') ? (($o->rate->commision / 100) * $o->s_amount) : $o->rate->commision;
                $subTotal = $main;
                $taxRate = 0;
                $taxCost = $subTotal + $taxRate;
                $totalCost = $subTotal + $taxCost;

                ///new stripe client instance
                // $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

                // if ($o->tx_status == 'unpaid') {
                //     //cretae an brand new stripe payment intent
                //     $stripeIntent =  $stripe->paymentIntents->create([
                //         'amount' => round($totalCost) * 100,
                //         'currency' => 'eur',
                //         'automatic_payment_methods' => ['enabled' => true],
                //     ]);
                // } else {
                //     $stripeIntent = $stripe->paymentIntents->retrieve($o->tx_reference);
                // }

                // if ($stripeIntent->status == 'succeeded') {
                //     IntlFundTransferOrder::where('id', $intlFundTransferOrder)->update([
                //         'tx_reference' => $stripeIntent->id,
                //         'tx_status' => 'pending'
                //     ]);
                // } else if ($stripeIntent->status == 'processing') {
                //     IntlFundTransferOrder::where('id', $intlFundTransferOrder)->update([
                //         'tx_reference' => $stripeIntent->id,
                //         'tx_status' => 'unpaid'
                //     ]);
                //     // return redirect()->route('payment.summary', ['order_id' => $pp->order_id]);
                // } else if ('requires_payment_method') {
                //     IntlFundTransferOrder::where('id', $intlFundTransferOrder)->update([
                //         'tx_reference' => $stripeIntent->id,
                //         'tx_status' => 'rejected',
                //     ]);
                //     // return redirect()->route('payment.summary', ['order_id' => $pp->order_id]);
                // } else {
                // }

                if (getAccountbalances(Auth::id())['balance'] >= $totalCost) {
                    if ($o->tx_status == 'unpaid') {
                        $u = updateAccountBalance(Auth::id(), ($taxCost), $o->tracking_id, 'credit', 'EU Fund Order Order ' . $o->tracking_id);
                        $c = updateAccountBalance(Auth::id(), ($comm), $o->tracking_id, 'debit', 'EU Fund Order Commision ' . $o->tracking_id);

                        $l = IntlFundTransferOrder::where('id', $intlFundTransferOrder)->update([
                            'tx_reference' => $u->id,
                            'tx_status' => 'pending'
                        ]);
                    }
                    $o = IntlFundTransferOrder::where('id', $intlFundTransferOrder)->first();
                    return view('dispatcher.intl_fund.show', [
                        'order' => $o,
                        'stripeIntent' => $o
                    ]);
                } else {
                    return back()->with(['message' => 'Insufficent Balance to complete Order']);
                }
            } else {
                return back()->with(['message' => 'Order does not exist']);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\IntlFundTransferOrder  $intlFundTransferOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(IntlFundTransferOrder $intlFundTransferOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\IntlFundTransferOrder  $intlFundTransferOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, IntlFundTransferOrder $intlFundTransferOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\IntlFundTransferOrder  $intlFundTransferOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(IntlFundTransferOrder $intlFundTransferOrder)
    {
        //
    }
}
