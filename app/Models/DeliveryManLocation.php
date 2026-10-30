<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryManLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_man_id',
        'latitude',
        'longitude',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'recorded_at' => 'datetime',
    ];

    public function deliveryMan()
    {
        return $this->belongsTo(User::class, 'delivery_man_id', 'id');
    }
}
