<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'beneficiary_id',
        'beneficiary_account_number',
        'beneficiary_account_name',
        'beneficiary_bank_name',
        'beneficiary_bank_code',
        'extra_datas'
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
