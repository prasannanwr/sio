<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'currency',
        'min_order_value',
        'usage_limit',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public static function discountTypes(): array
    {
        return [
            self::TYPE_PERCENTAGE,
            self::TYPE_FIXED,
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function hasStarted(): bool
    {
        return $this->starts_at === null || ! $this->starts_at->isFuture();
    }

    public function hasRemainingUses(): bool
    {
        return $this->usage_limit === null || $this->used_count < $this->usage_limit;
    }

    public function remainingUses(): ?int
    {
        return $this->usage_limit === null ? null : max(0, $this->usage_limit - $this->used_count);
    }

    /**
     * Validate the coupon against an order's amount and currency.
     * Returns null when valid, otherwise a human-readable reason.
     */
    public function validationError(float $orderAmount, string $currency): ?string
    {
        if (! $this->is_active) {
            return 'This coupon is not active.';
        }

        if (! $this->hasStarted()) {
            return 'This coupon is not yet valid.';
        }

        if ($this->isExpired()) {
            return 'This coupon has expired.';
        }

        if (! $this->hasRemainingUses()) {
            return 'This coupon has reached its usage limit.';
        }

        // Strict active-currency match: the coupon only applies to orders in its currency.
        if (strtoupper($currency) !== strtoupper($this->currency)) {
            return 'This coupon can only be used for ' . $this->currency . ' orders.';
        }

        if ($this->min_order_value !== null && $orderAmount < (float) $this->min_order_value) {
            return 'A minimum order value of ' . $this->min_order_value . ' ' . $this->currency . ' is required for this coupon.';
        }

        return null;
    }

    public function discountFor(float $orderAmount): float
    {
        $discount = $this->discount_type === self::TYPE_PERCENTAGE
            ? $orderAmount * ((float) $this->discount_value / 100)
            : (float) $this->discount_value;

        return round(min($discount, $orderAmount), 2);
    }
}
