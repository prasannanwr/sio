<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BeneficiaryAccount;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BeneficiaryAccountController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'beneficiary_id' => 'required'
        ]);
        $accounts = BeneficiaryAccount::where('beneficiary_id', $request->beneficiary_id)->where('user_id', $request->user()->id)->get();
        return response()->json(['status' => true, 'message' => 'Beneficiary accounts retrieved successfully', 'data' => $accounts]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'beneficiary_id' => 'required',
            'beneficiary_account_number' => 'required',
            'beneficiary_account_name' => 'nullable',
            'beneficiary_bank_name' => 'nullable',
            'beneficiary_bank_code' => 'required',
            'extra_datas' => 'nullable'
        ]);

        try {
            $account = new BeneficiaryAccount;
            $account->beneficiary_id = $request->beneficiary_id;
            $account->beneficiary_account_number = $request->beneficiary_account_number;
            $account->user_id = $request->user()->id;
            $account->beneficiary_bank_name = ($request->beneficiary_bank_name) ? $request->beneficiary_bank_name : 'N/A';
            $account->beneficiary_bank_code = ($request->beneficiary_bank_code) ? $request->beneficiary_bank_code : 'N/A';
            $account->beneficiary_account_name = ($request->beneficiary_account_name) ? $request->beneficiary_account_name : 'N/A';
            $account->extra_datas = ($request->extra_datas) ? $request->extra_datas : null;

            $account->save();

            $beneficiary = Beneficiary::find($account->beneficiary_id);

            $beneficiary_extra_datas = json_decode($beneficiary->extra_datas, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($beneficiary_extra_datas['customer_id'])) {
                $customer_id = $beneficiary_extra_datas['customer_id'];
            } else {
                return response()->json(['error' => 'Invalid or missing customer_id in extra_datas'], 500);
            }

            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            // $stripe->customers->createSource(
            //     $customer_id,
            //     ['source' => [
            //             'account_number' => $account->beneficiary_account_number,
            //             'country' => $account->beneficiary_account_country,
            //             'currency' => ,
            //             'account_holder_name' => ,
            //             'account_holder_type' => ,
            //             ''
            //         ]
            //     ]
            // );

            $bank_account = $stripe->customers->createSource(
                $customer_id,
                ['source' => [
                        'account_number' => '000123456789',
                        'country' => 'US',
                        'currency' => 'USD',
                        'account_holder_name' => 'Test User',
                        'account_holder_type' => 'individual',
                        'object' => 'bank_account',
                        'routing_number' => '111000000'
                    ]
                ]
            );

            $stripe_data = [
                'bank_account_id' => $bank_account->id,
            ];

            $account->update([
                'extra_datas' => json_encode($stripe_data),
            ]);

            return response()->json(['beneficiary_account' => $account], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'A server error occured'], 500);
        }
    }

    public function update(Request $request, $account_id)
    {
        $request->validate([
            'beneficiary_account_number' => 'required',
            'beneficiary_account_name' => 'nullable',
            'beneficiary_bank_name' => 'nullable',
            'beneficiary_bank_code' => 'required',
            'extra_datas' => 'nullable'
        ]);

        try {
            $account = BeneficiaryAccount::where('id', $account_id)->first();
            $account->beneficiary_account_number = $request->beneficiary_account_number;
            $account->user_id = $request->user()->id;
            $account->beneficiary_bank_name = ($request->beneficiary_bank_name) ? $request->beneficiary_bank_name : 'N/A';
            $account->beneficiary_bank_code = ($request->beneficiary_bank_code) ? $request->beneficiary_bank_code : 'N/A';
            $account->beneficiary_account_name = ($request->beneficiary_account_name) ? $request->beneficiary_account_name : 'N/A';
            $account->extra_datas = ($request->extra_datas) ? $request->extra_datas : null;
            $account->save();
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'A server error occured'], 500);
        }
    }

    public function show($account_id)
    {
        $account = BeneficiaryAccount::where('id', $account_id)->where('user_id', auth()->user()->id)->first();
        if ($account) {
            return response()->json([
                'status' => true,
                'message' => 'Beneficiary Account Fetched',
                'data' => $account
            ]);
        } else {

            return response()->json([
                'status' => false,
                'message' => 'Beneficiary Account not found',
            ], 404);
        }
    }

    public function delete(Request $request, $account_id)
    {
        $user = auth()->user();

        $query = BeneficiaryAccount::query()->where('user_id', $user->id)
            ->where('id', $account_id);

        try {
            $query->delete();

            return response()->json([
                'status' => true,
                'message' => 'Beneficiary account deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => true,
                'message' => 'Error deleting beneficiary account'
            ], 500);
        }
    }
}
