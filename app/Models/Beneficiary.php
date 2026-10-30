<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'country_code',
        'relationship',
        'gender',
        'deleted',
        'extra_datas'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function beneficiaryAccounts()
    {
        return $this->hasMany(BeneficiaryAccount::class);
    }
}
