<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycAddressProofType extends Model
{
    use HasFactory;

    protected $table = 'kyc_address_proof_types';
    protected $fillable = ['name'];

    public function kyc()
    {
        return $this->hasMany(Kyc::class);
    }
}
