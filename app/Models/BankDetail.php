<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_name',
        'bank_code',
        'account_name',
        'iban',
        'country_id',
        'city_id',
        'status',
        'routing_number',
        'acount_number',
    ];


    public function country(){
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function city(){
        return $this->belongsTo(City::class, 'city_id');
    }

    public function paymentRequests(){
        return $this->hasMany(PaymentRequest::class, 'bank_detail_id');
    }
}
