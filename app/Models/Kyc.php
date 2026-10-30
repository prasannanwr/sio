<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kyc extends Model
{
    use HasFactory;

    protected $table = 'kyc';
    protected $fillable = [
        'user_id',
        'document_type_id',
        'document_front',
        'document_back',
        'selfie',
        'proof_of_address_type_id',
        'proof_of_address',
        'personal_information',
        'kyc_level',
        'status',
        'rejection_reason'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documentType()
    {
        return $this->belongsTo(KycDocumentType::class);
    }

    public function proofOfAddressType()
    {
        return $this->belongsTo(KycAddressProofType::class);
    }
}
