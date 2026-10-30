<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getKycInfo($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['stats' => false, 'error' => 'User not found'], 404);
        }

        $kycInfo = $user->kyc;

        if (!$kycInfo) {
            return response()->json([
                'stats' => false,
                'message' => 'User Kyc status not found',
                'data' => [
                    'info' => false,
                    'selfie' => false,
                    'document' => false,
                    'proof_of_address' => false,
                    'completed_verified' => false,
                    'rejected' => false,
                    'reason' => null,
                    'data' => [
                        'info' => null,
                        'selfie' => null,
                        'document_front' => null,
                        'document_back' => null,
                        'proof_of_address' => null
                    ]
                ]
            ]);
        }

        $transformedData = $this->transformKycInfo($kycInfo);

        return response()->json(['stats' => true, 'data' => $transformedData]);
    }


    protected function transformKycInfo($kycInfo)
    {
        $transformedData = [
            'info' => !is_null($kycInfo->personal_information),
            'selfie' => !is_null($kycInfo->selfie),
            'document' => !is_null($kycInfo->document_front) || !is_null($kycInfo->document_back),
            'proof_of_address' => !is_null($kycInfo->proof_of_address),
            'completed_verified' => $kycInfo->status == 'approved',
            'rejected' => $kycInfo->status == 'rejected',
            'reason' => $kycInfo->rejection_reason,
            'data' => [
                'info' => $kycInfo->personal_information,
                'selfie' => $kycInfo->selfie,
                'document_type_id' => $kycInfo->document_type_id,
                'proof_of_address_type_id' => $kycInfo->proof_of_address_type_id,
                'document_front' => $kycInfo->document_front,
                'document_back' => $kycInfo->document_back,
                'proof_of_address' => $kycInfo->proof_of_address
            ]
        ];

        return $transformedData;
    }
}
