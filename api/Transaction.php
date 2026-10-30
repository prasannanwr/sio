<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    public const STATUS_PENDING        = 'PENDING';
    public const STATUS_PAID           = 'PAID';
    public const STATUS_OVERPAID       = 'OVERPAID';
    public const STATUS_PARTIALLY_PAID = 'PARTIALLY_PAID';
    public const STATUS_FAILED         = 'FAILED';
    public const STATUS_EXPIRED        = 'EXPIRED';
    public const STATUS_CANCELLED      = 'USER_CANCELLED';

    protected $fillable = [
        'payment_reference',
        'transaction_reference',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'amount',
        'amount_paid',
        'fee',
        'currency',
        'payment_description',
        'payment_method',
        'status',
        'checkout_url',
        'metadata',
        'gateway_response',
        'paid_at',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'amount_paid'      => 'decimal:2',
        'fee'              => 'decimal:2',
        'metadata'         => 'array',
        'gateway_response' => 'array',
        'paid_at'          => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSuccessful(): bool
    {
        return in_array($this->status, [self::STATUS_PAID, self::STATUS_OVERPAID], true);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
