<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $beneficiaries = $user->beneficiaries;
        return response()->json(['status' => true, 'message' => 'Beneficiaries retrieved successfully', 'data' => $beneficiaries]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Validate the request data
        /*$request->validate([
            'name' => 'required|string',
            // Add more validation rules as needed
        ]);*/

        $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required',
            'document_issue_date' => 'required|date',
            'document_expiry_date' => 'required|date',
        ]);

        // Create a new beneficiary for the authenticated user
        $beneficiary = $user->beneficiaries()->create($request->all());

        return response()->json(['beneficiary' => $beneficiary], 201);
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'country_code' => 'required|string',
            'relationship' => 'required|string',
            'gender' => 'required|string',
        ]);

        // Create a new beneficiary for the authenticated user
        $beneficiary = $user->beneficiaries()->create($request->all());

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        $create_customer = $stripe->customers->create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'metadata' => [
                'country_code' => $request->input('country_code'),
                'relationship' => $request->input('relationship'),
                'gender' => $request->input('gender')
            ]
        ]);

        $stripe_data = [
            'customer_id' => $create_customer->id,
        ];

        $beneficiary->update([
            'extra_datas' => json_encode($stripe_data),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Beneficiary Added'
        ]);
    }

    public function updateBeneficiary(Request $request, $beneficiaryId)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'country_code' => 'required|string',
            'relationship' => 'required|string',
            'gender' => 'required|string',
        ]);

        // Create a new beneficiary for the authenticated user
        $updated = Beneficiary::where('id', $beneficiaryId)
            ->where('user_id', $user->id)->update($request->except('bank_accounts'));

        if ($updated) {
            return response()->json([
                'status' => true,
                'message' => 'Beneficiary Updated'
            ]);
        } else {
            return response()->json([
                'status' => true,
                'message' => 'Error Deleting Beneficiary'
            ], 500);
        }
    }


    public function getBeneficiaries(Request $request)
    {

        $user = auth()->user();

        $searchWord = $request->input('search');
        $country = $request->input('country');
        $pageNumber = $request->input('page', 1);
        $limit = $request->input('limit', 20);

        $query = Beneficiary::query()->where('user_id', $user->id)->where('deleted', 0);

        if ($searchWord) {
            $query->where('name', 'like', '%' . $searchWord . '%');
        }

        if ($country) {
            $query->where('country_code', $country);
        }

        $query->orderBy('name', 'asc');

        $beneficiaries = $query->paginate(
            $limit,
            ['id',  'name', 'phone', 'country_code', 'relationship', 'gender'],
            'page',
            $pageNumber
        );

        return response()->json([
            'status' => true,
            'message' => 'Beneficiaries retrieved successfully',
            'total' => $beneficiaries->total(),
            'page' => $beneficiaries->currentPage(),
            'page_size' => $beneficiaries->perPage(),
            'data' => $beneficiaries->items()
        ]);
    }

    public function deleteBeneficiary($beneficiaryId): JsonResponse
    {

        $user = auth()->user();



        $query = Beneficiary::query()->where('user_id', $user->id)
            ->where('id', $beneficiaryId);

        try {
            $query->update(['deleted' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'Beneficiaries deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => true,
                'message' => 'Error deleting beneficiary'
            ], 500);
        }
    }


    public function search(Request $request)
    {
        return response()->json(['results' => $searchResults]);
    }
}
