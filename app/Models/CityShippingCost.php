<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityShippingCost extends Model
{
    use HasFactory;
    use HasFactory;

    protected $fillable = [
        'day',
        'country_id',
        'city_id',
        'origin_city_id',
        'destination_city_id',
        'freight_mode',
        'weight',
        'weight_range',
        'cost',
        'cost_eur',
        'cost_naira',
        'rate_per_km_per_kg',
        'distance_km',
        'invoice_document',
        'packing_list_document',
        'id_document',
        'permit_document',
        'percentage',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function originCity()
    {
        return $this->belongsTo(City::class, 'origin_city_id');
    }

    public function destinationCity()
    {
        return $this->belongsTo(City::class, 'destination_city_id');
    }
}
