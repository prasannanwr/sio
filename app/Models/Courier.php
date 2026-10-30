<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'phone_alt',
        'current_location_id',
        'vehicle_type',
        'origin_location',
        'destination_location',
        'weight_range',
        'rate_per_km_per_kg',
        'rate_eur',
        'rate_naira',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function current_location()
    {
        return $this->belongsTo(Location::class, 'current_location_id', 'id');
    }
}
