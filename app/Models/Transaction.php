<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_PAID = 'PAID';
    public const STATUS_OVERPAID = 'OVERPAID';
    public const STATUS_PARTIALLY_PAID = 'PARTIALLY_PAID';
    public const STATUS_FAILED = 'FAILED';
    public const STATUS_EXPIRED = 'EXPIRED';
    public const STATUS_CANCELLED = 'USER_CANCELLED';

    protected $table = 'transactions';
    protected $fillable = [
        'user_id',
        'order_id',
        'payment_reference',
        'transaction_type',
        'beneficiary_id',
        'beneficiary_account_id',
        'transaction_id',
        'transaction_amount',
        'amount',
        'amount_paid',
        'fee',
        'currency',
        'customer_name',
        'customer_email',
        'customer_phone',
        'payment_description',
        'payment_method',
        'status',
        'checkout_url',
        'metadata',
        'gateway_response',
        'paid_at',
        'beneficiary_account_number',
        'beneficiary_account_name',
        'beneficiary_bank_name',
        'beneficiary_bank_code',
        'payment_provider',
        'transaction_status',
        'transaction_reference',
        'payment_intent',
        'payment_intent_data',
        'sending_country_code',
        'receiving_country_code',
        'received_amount',
        'transaction_fee'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'fee' => 'decimal:2',
        'metadata' => 'array',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function beneficiaryAccount()
    {
        return $this->belongsTo(BeneficiaryAccount::class);
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
