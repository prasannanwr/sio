<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyExchangeRate extends Model
{
    use HasFactory;
    protected $fillable = [
        'country_id',
        'exchange_rate',
    ];

    public function country(){
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

}
