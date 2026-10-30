<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryShippingCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'day',
        'country_id',
        'city_id',
        'cost',
        'percentage',
    ];
}
