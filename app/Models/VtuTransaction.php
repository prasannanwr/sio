<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VtuTransaction extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_PROCESSING = 'PROCESSING';
    public const STATUS_SUCCESSFUL = 'SUCCESSFUL';
    public const STATUS_FAILED = 'FAILED';
    public const STATUS_REVERSED = 'REVERSED';

    protected $fillable = [
        'user_id',
        'reference',
        'payment_reference',
        'provider_reference',
        'service_type',
        'provider_code',
        'product_code',
        'customer_reference',
        'customer_name',
        'customer_email',
        'customer_phone',
        'amount',
        'fee',
        'currency',
        'status',
        'description',
        'metadata',
        'request_payload',
        'provider_response',
        'fulfilled_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'metadata' => 'array',
        'request_payload' => 'array',
        'provider_response' => 'array',
        'fulfilled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Transaction::class, 'payment_reference', 'payment_reference');
    }

    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESSFUL;
    }

    /**
     * What the customer received from the provider, such as an electricity token or exam PINs.
     *
     * @return array<string,mixed>|null
     */
    public function purchaseDetails(): ?array
    {
        $response = is_array($this->provider_response) ? $this->provider_response : [];

        $details = array_filter([
            'token' => $response['token'] ?? null,
            'units' => $response['units'] ?? null,
            'pins' => $response['pins'] ?? null,
        ], fn ($value) => $value !== null && $value !== '' && $value !== []);

        return $details ?: null;
    }

    public function isPending(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING], true);
    }
}
