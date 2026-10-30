<?php

namespace App\Constraints;

class DeliveryManStatus
{
    public const PENDING = 'Pending';
    public const ACCEPTED = 'Accepted';
    public const REJECTED = 'Rejected';
    public const ENGAGE_WITH_PARCEL = 'engage with parcel';
    public const PICKED_UP = 'Picked up';
    public const ON_THE_WAY = 'On the way';
    public const ARRIVED = 'Arrived';
    public const DELIVERED = 'Delivered';
}
