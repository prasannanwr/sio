<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batchlog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ship_from_country_id',
        'ship_from_city_id',
        'ship_to_country_id',
        'ship_to_city_id',
        'current_location_county_id',
        'current_location_city_id',
        'batch_id',
    ];

    public function batch()
    {
        return $this->belongsTo(OrderBatch::class, 'batch_id');
    }

    public function shipFromCountry()
    {
        return $this->belongsTo(Country::class, 'ship_from_country_id');
    }

    public function shipFromCity()
    {
        return $this->belongsTo(City::class, 'ship_from_city_id');
    }
    public function shipToCountry()
    {
        return $this->belongsTo(Country::class, 'ship_to_country_id');
    }
    public function shipToCity()
    {
        return $this->belongsTo(City::class, 'ship_to_city_id');
    }

    public function shipCurrentCountry()
    {
        return $this->belongsTo(Country::class, 'current_location_county_id');
    }
    public function shipCurrentCity()
    {
        return $this->belongsTo(City::class, 'current_location_city_id');
    }
}
