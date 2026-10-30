<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'origin_id',
        'destination_id',
        'weight_start',
        'weight_end',
        'price',
        'length',
        'width',
        'height',
        'delivery_cost_per_km',
        'pickup_cost_per_km',
        'desc',
        'transit_days'
    ];

    public function origin()
    {
        return $this->belongsTo(Location::class, 'origin_id', 'id');
    }

    public function destination()
    {
        return $this->belongsTo(Location::class, 'destination_id', 'id');
    }
}
