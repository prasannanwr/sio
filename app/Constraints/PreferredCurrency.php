<?php

namespace App\Constraints;

class PreferredCurrency
{
    public const EUR = 'EUR';
    public const NGN = 'NGN';

    public const DEFAULT = self::EUR;

    public static function values(): array
    {
        return [
            self::EUR,
            self::NGN,
        ];
    }
}
