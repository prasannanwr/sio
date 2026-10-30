<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    public const TYPE_BIKE = 'Bike';
    public const TYPE_CAR = 'Car';
    public const TYPE_VAN = 'Van';
    public const TYPE_TRUCK = 'Truck';

    public const TYPE_AIR = 'Air Cargo';
    public const TYPE_SEA = 'Sea Cargo';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_DECOMMISIONED = 'decommisioned';

    protected $fillable = [
        'type',
        'brand',
        'model',
        'plate_number',
        'color',
        'year',
        'documents',
        'delivery_man_id',
        'shipping_cost_id',
        'courier_id',
        'origin_location',
        'destination_location',
        'weight_range',
        'rate_per_km_per_kg',
        'rate_eur',
        'rate_naira',
        'distance_km',
        'status',
    ];

    protected $casts = [
        'documents' => 'array',
        'year' => 'integer',
        'rate_per_km_per_kg' => 'decimal:2',
        'rate_eur' => 'decimal:2',
        'rate_naira' => 'decimal:2',
    ];

    public static function types(): array
    {
        return [
            self::TYPE_BIKE,
            self::TYPE_CAR,
            self::TYPE_VAN,
            self::TYPE_TRUCK,
            self::TYPE_AIR,
            self::TYPE_SEA
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
            self::STATUS_DECOMMISIONED,
        ];
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class, 'courier_id', 'id');
    }

    public function deliveryMan()
    {
        return $this->belongsTo(User::class, 'delivery_man_id', 'id');
    }

    public function shippingCost()
    {
        return $this->belongsTo(ShippingCost::class, 'shipping_cost_id', 'id');
    }

    public function originCity()
    {
        return $this->belongsTo(City::class, 'origin_location', 'id');
    }

    public function destinationCity()
    {
        return $this->belongsTo(City::class, 'destination_location', 'id');
    }
}
