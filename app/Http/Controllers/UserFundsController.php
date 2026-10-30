<?php

namespace App\Http\Controllers;

use App\Models\BankDetail;
use App\Models\UserFunds;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserFundsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (null != request()->get('payment_intent')) {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $stripeIntent = $stripe->paymentIntents->retrieve(request()->get('payment_intent'));
            // dd($stripeIntent);
            if ($stripeIntent->status == 'succeeded') {
                if (accountTxExists(request()->get('payment_intent'))) {
                    return view('agents.settings.wallet')->with(['message' => 'Deposit successfully received', 'message_type' => 'success']);
                } else {
                    $u = updateAccountBalance(Auth::id(), ($stripeIntent->amount_received / 100), $stripeIntent->id, 'debit', 'Wallet Funding');
                    if ($u) {
                        return view('agents.settings.wallet')->with(['message' => 'Deposit successfully received', 'message_type' => 'success']);
                    } else {
                        return view('agents.settings.wallet')->with(['message' => 'Failed to update Wallet']);
                    }
                }
            } else {
                return view('agents.settings.wallet')->with(['message' => 'Deposit Unssuccessful']);
            }
        } else if (null != request()->get('deposit_amt')) {
            $amt = request()->get('deposit_amt');
            ///new stripe client instance
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

            //cretae an brand new stripe payment intent
            $stripeIntent =  $stripe->paymentIntents->create([
                'amount' => round($amt) * 100,
                'currency' => 'eur',
                'automatic_payment_methods' => ['enabled' => true],
            ]);

            return view('agents.settings.wallet', ['stripeIntent' => $stripeIntent, 'amount' => $amt]);
        } elseif (null != request()->get('date_from') && null != request()->get('date_to')) {
            $dateFrom = Carbon::parse(request('date_from'))->startOfDay();
            $dateTo = Carbon::parse(request('date_to'))->endOfDay();
            $recordsBetweenDates = UserFunds::where('user_id', Auth::id())->whereBetween('created_at', [$dateFrom, $dateTo])->get();
           
            return view('agents.settings.wallet', ['wallet_history' => $recordsBetweenDates]);
        } else {

            return view('agents.settings.wallet');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserFunds  $userFunds
     * @return \Illuminate\Http\Response
     */
    public function show(UserFunds $userFunds)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserFunds  $userFunds
     * @return \Illuminate\Http\Response
     */
    public function edit(UserFunds $userFunds)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserFunds  $userFunds
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserFunds $userFunds)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserFunds  $userFunds
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserFunds $userFunds)
    {
        //
    }
}
