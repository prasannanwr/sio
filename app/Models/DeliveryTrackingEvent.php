<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryTrackingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tracking_id',
        'delivery_man_id',
        'performed_by',
        'old_status',
        'new_status',
        'old_delivery_man_status',
        'new_delivery_man_status',
        'current_location_id',
        'current_location_country_id',
        'current_location_city_id',
        'note',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function deliveryMan()
    {
        return $this->belongsTo(User::class, 'delivery_man_id', 'id');
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by', 'id');
    }
}
