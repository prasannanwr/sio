<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Stripe\Stripe;
use App\Http\Controllers\Controller;
use App\Models\BeneficiaryAccount;

class WebhookController extends Controller
{
    public function handleStripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.endpoint_secret')
            );
        } catch (SignatureVerificationException $e) {
            return response()->json(['status' => false, 'message' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $intent = $event->data->object;
                $paymentIntent = $intent->client_secret;

                $transaction = Transaction::where('payment_intent', $paymentIntent)->first();

                if ($transaction) {
                    $transaction->update(['transaction_status' => 'success']);

                    $beneficiary_account_id = $transaction->beneficiary_account_id;

                    $beneficiary_account = BeneficiaryAccount::find($beneficiary_account_id);

                    $beneficiary_extra_datas = json_decode($beneficiary_account->extra_datas, true);

                    if (json_last_error() === JSON_ERROR_NONE && isset($beneficiary_extra_datas['customer_id'])) {
                        $bank_id = $beneficiary_extra_datas['bank_account_id'];
                    } else {
                        return response()->json(['error' => 'Missing bank_account_id in extra_datas'], 500);
                    }

                    $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

                    $stripe->payouts->create([
                        'amount' => $transaction->received_amount,
                        'currency' => 'usd',
                        'method' => 'instant',
                        'destination' => $bank_id,
                    ]);
                }

                break;
            case 'payment_intent.payment_failed':
                $intent = $event->data->object;
                $paymentIntent = $intent->id;

                $transaction = Transaction::where('payment_intent', $paymentIntent)->first();

                if ($transaction) {
                    $transaction->update(['transaction_status' => 'failed']);
                }
                break;
        }

        return response()->json(['status' => true, 'message' => 'success']);
    }
}
