<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use App\Models\TransferIntent;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $paymentIntentData['user_id'] = Auth::id();

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $request->input('amount'),
                'currency' => $request->input('currency', 'usd'),
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => [
                    'beneficiary_id' => $request->input('beneficiary_id'),
                    'beneficiary_account_id' => $request->input('beneficiary_account_id'),
                    'beneficiary_account_number' => $request->input('beneficiary_account_number'),
                    'beneficiary_account_name' => $request->input('beneficiary_account_name'),
                    'beneficiary_bank_name' => $request->input('beneficiary_bank_name'),
                    'beneficiary_bank_code' => $request->input('beneficiary_bank_code')
                ]
            ]);

            $paymentIntentData['intent_id'] = $paymentIntent->client_secret;
            $paymentIntentData['payment_intent_data'] = $paymentIntent;

            TransferIntent::create($paymentIntentData);

            return response()->json([
                'status' => true,
                'message' => 'Payment intent created successfully',
                'data' => [
                    'intent_id' => $paymentIntent->client_secret]
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
