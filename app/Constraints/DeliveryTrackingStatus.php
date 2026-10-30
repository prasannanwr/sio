<?php

namespace App\Constraints;

class DeliveryTrackingStatus
{
    public const PENDING = 'PENDING';
    public const ACCEPTED = 'ACCEPTED';
    public const ON_THE_WAY = 'ON_THE_WAY';
    public const DELIVERED = 'DELIVERED';

    public static function values(): array
    {
        return [
            self::PENDING,
            self::ACCEPTED,
            self::ON_THE_WAY,
            self::DELIVERED,
        ];
    }
}
