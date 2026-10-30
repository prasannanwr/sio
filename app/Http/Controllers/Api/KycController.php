<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MobileAppUser;
use App\Models\Kyc;
use App\Models\KycDocumentType;
use App\Models\KycAddressProofType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KycController extends Controller
{
    public function documentTypes()
    {
        $document_types = KycDocumentType::orderBy('id', 'ASC')->get();

        return response()->json(['status' => true, 'message' => 'Document types retrieved successfully', 'data' => $document_types]);
    }

    public function addressProofTypes()
    {
        $proof_types = KycAddressProofType::orderBy('id', 'ASC')->get();

        return response()->json(['status' => true, 'message' => 'Proof of address types retrieved successfully', 'data' => $proof_types]);
    }

    public function submitPersonalInformation(Request $request)
    {
        $validated_data = $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'othername' => 'nullable|string',
            'email' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'nationality' => 'required|string',
            'place_of_birth' => 'required|string',
            'occupation' => 'required|string',
            'fiscal_code' => 'required|string',
            'gender' => 'required|string'
        ]);

        // $kycData = $request->all();

        $kycData['user_id'] = Auth::id();
        $kycData['personal_information'] = json_encode($validated_data);

        Kyc::create($kycData);

        Auth::user()->kyc()->update(['kyc_level' => 1]);

        return response()->json(['status' => true, 'message' => 'Personal submitted successfully']);
    }

    public function submitDocumentVerification(Request $request)
    {
        $request->validate([
            'document_type_id' => 'required|exists:kyc_document_type,id',
            'document_front' => 'required',
            'document_back' => 'required',
        ]);

        $document_front = $request->file('document_front')->store('documents', 'public');
        $document_back = $request->file('document_back')->store('documents', 'public');

        $kycData['user_id'] = Auth::id();
        $kycData['document_front'] = $document_front;
        $kycData['document_back'] = $document_back;
        $kycData['document_type_id'] = $request->document_type_id;

        Auth::user()->kyc()->update($kycData);

        Auth::user()->kyc()->update(['kyc_level' => 2]);

        return response()->json(['status' => true, 'message' => 'Document verification submitted successfully']);
    }

    public function submitSelfie(Request $request)
    {
        // Validate and store selfie
        $request->validate([
            'selfie' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);

        Auth::user()->kyc()->update(['selfie' => $request->file('selfie')->store('selfies', 'public')]);

        // Set kyc_level to 2 after document verification
        Auth::user()->kyc()->update(['kyc_level' => 3]);

        return response()->json(['status' => true, 'message' => 'Selfie submitted successfully']);
    }

    public function submitProofOfAddress(Request $request)
    {
        // Validate and store proof of address
        $request->validate([
            'proof_of_address' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        Auth::user()->kyc()->update(['proof_of_address_type_id' => $request->input('proof_of_address_type_id'), 'proof_of_address' => $request->file('proof_of_address')->store('proofs_of_address', 'public')]);

        // Set kyc_level to 3 after document verification
        Auth::user()->kyc()->update(['kyc_level' => 4]);

        return response()->json(['status' => true, 'message' => 'Proof of address submitted successfully']);
    }
}
