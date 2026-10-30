<?php

namespace App\Http\Controllers\Api;

use App\Constraints\PreferredCurrency;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * GET /api/coupons — active, currently-valid coupons for the app's "Best Offers" list.
     * Defaults to the user's preferred currency; pass ?currency=EUR|NGN|ALL to override.
     */
    public function index(Request $request): JsonResponse
    {
        $currency = strtoupper(trim((string) $request->query('currency', '')));

        if ($currency === '') {
            $currency = $request->user()->preferred_currency ?: PreferredCurrency::DEFAULT;
        }

        $query = Coupon::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            });

        // Strict currency match (unless ALL is requested).
        if ($currency !== 'ALL') {
            $query->where('currency', $currency);
        }

        $coupons = $query->latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Coupons retrieved successfully.',
            'total' => $coupons->count(),
            'currency' => $currency === 'ALL' ? null : $currency,
            'data' => $coupons->map(fn (Coupon $coupon) => $this->couponPayload($coupon))->values(),
        ]);
    }

    protected function couponPayload(Coupon $coupon): array
    {
        return [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'discount_label' => $this->discountLabel($coupon),
            'description' => $coupon->description,
            'discount_type' => $coupon->discount_type,
            'discount_value' => (float) $coupon->discount_value,
            'currency' => $coupon->currency,
            'min_order_value' => $coupon->min_order_value !== null ? (float) $coupon->min_order_value : null,
            'expires_at' => optional($coupon->expires_at)->toIso8601String(),
            'usage_limit' => $coupon->usage_limit,
            'remaining_uses' => $coupon->remainingUses(),
        ];
    }

    protected function discountLabel(Coupon $coupon): string
    {
        $value = rtrim(rtrim(number_format((float) $coupon->discount_value, 2, '.', ''), '0'), '.');

        if ($coupon->discount_type === Coupon::TYPE_PERCENTAGE) {
            return 'Get ' . $value . '% OFF';
        }

        return 'Get ' . $value . ' ' . $coupon->currency . ' OFF';
    }
}
