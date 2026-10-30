<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryManPayout extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_PROCESSING = 'PROCESSING';
    public const STATUS_PAID = 'PAID';
    public const STATUS_FAILED = 'FAILED';
    public const STATUS_CANCELLED = 'CANCELLED';

    protected $fillable = [
        'reference',
        'delivery_man_id',
        'processed_by',
        'total_amount',
        'orders_count',
        'currency',
        'status',
        'payout_method',
        'payment_reference',
        'notes',
        'metadata',
        'requested_at',
        'paid_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'metadata' => 'array',
        'requested_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function deliveryMan()
    {
        return $this->belongsTo(User::class, 'delivery_man_id', 'id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'id');
    }

    public function items()
    {
        return $this->hasMany(DeliveryManPayoutItem::class, 'delivery_man_payout_id', 'id');
    }

    public function logs()
    {
        return $this->hasMany(DeliveryManPayoutLog::class, 'delivery_man_payout_id', 'id');
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }
}
