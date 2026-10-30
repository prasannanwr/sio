<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
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
                    'beneficiary_bank_code' => $request->input('beneficiary_bank_code'),
                    'sending_country_code' => $request->input('sending_country_code'),
                    'receiving_country_code' => $request->input('receiving_country_code'),
                    'received_amount' => $request->input('received_amount'),
                    'transaction_fee' => $request->input('transaction_fee')
                ]
            ]);

            $paymentIntentData['beneficiary_id'] = $request->input('beneficiary_id');
            $paymentIntentData['beneficiary_account_id'] = $request->input('beneficiary_account_id');
            $paymentIntentData['beneficiary_account_number'] = $request->input('beneficiary_account_number');
            $paymentIntentData['beneficiary_account_name'] = $request->input('beneficiary_account_name');
            $paymentIntentData['beneficiary_bank_name'] = $request->input('beneficiary_bank_name');
            $paymentIntentData['beneficiary_bank_code'] = $request->input('beneficiary_bank_code');
            $paymentIntentData['transaction_id'] = $this->generateTransactionId();
            $paymentIntentData['transaction_amount'] = $request->input('amount');
            $paymentIntentData['payment_provider'] = $request->input('payment_provider');
            $paymentIntentData['sending_country_code'] = $request->input('sending_country_code');
            $paymentIntentData['receiving_country_code'] = $request->input('receiving_country_code');
            $paymentIntentData['received_amount'] = $request->input('received_amount');
            $paymentIntentData['transaction_fee'] = $request->input('transaction_fee');

            $paymentIntentData['payment_intent'] = $paymentIntent->client_secret;
            $paymentIntentData['payment_intent_data'] = $paymentIntent;

            Transaction::create($paymentIntentData);

            return response()->json(['status' => true, 'message' => 'Payment intent created successfully', 'data' => ['client_secret' => $paymentIntent->client_secret]]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function generateTransactionId($prefix = 'SIOPAY-TXN')
    {
        $uniqueId = uniqid($prefix, true);

        $randomNumber = rand(1000, 9999);

        return $uniqueId . $randomNumber;
    }

    public function index(Request $request)
    {
        $searchWord = $request->input('search');
        $transaction_status = $request->input('transaction_status');
        $pageNumber = $request->input('page', 1);
        $limit = $request->input('limit', 20);

        $query = Transaction::query()->where('user_id', Auth::id());

        if ($searchWord) {
            $query->where(function ($query) use ($searchWord) {
                $query->where('beneficiary_account_number', 'like', '%' . $searchWord . '%')
                    ->orWhere('beneficiary_account_name', 'like', '%' . $searchWord . '%')
                    ->orWhere('beneficiary_bank_name', 'like', '%' . $searchWord . '%');
            });
        }

        if ($transaction_status) {
            $query->where('transaction_status', $transaction_status);
        }

        $query->orderBy('created_at', 'desc');

        $trans = $query->paginate(
            $limit,
            ['*'],
            'page',
            $pageNumber
        );

        return response()->json([
            'status' => true,
            'message' => 'Transactions retrieved successfully',
            'total' => $trans->total(),
            'page' => $trans->currentPage(),
            'page_size' => $trans->perPage(),
            'data' => $trans->items()
        ]);
    }
}
