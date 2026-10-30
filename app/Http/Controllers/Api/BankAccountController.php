<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\SetupIntent;

class BankAccountController extends Controller
{
    public function createSetupIntent(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $customer = Customer::create([
            'email' => $request->user()->email,
        ]);

        $setupIntent = SetupIntent::create([
            'customer' => $customer->id,
        ]);

        return response()->json(['client_secret' => $setupIntent->client_secret]);
    }

    public function verifyBankAccount(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $paymentMethod = $request->input('payment_method');

        $request->user()->createOrGetStripeCustomer();
        $request->user()->addPaymentMethod($paymentMethod);
        
        $intent = SetupIntent::retrieve($request->input('setup_intent'));
        $intent->confirm();

        return response()->json(['success' => true]);
    }
}
