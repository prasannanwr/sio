<?php

namespace App\Constraints;

class OrderStatus
{
    public const NEW_ORDER = 'new_order';
    public const UNPAID = 'unpaid';
    public const PLACED = 'placed';
    public const ASSIGNED = 'assigned';
    public const IN_DELIVERY_MAN = 'in_delivery_man';
    public const PROCESSING = 'processing';
    public const IN_TRANSIT = 'in_transit';
    public const DELIVERED = 'delivered';
    public const ACCEPT_BY_AGENT = 'Accepted By Agent';
    public const CUSTOMER_RECEIVED = 'Customer Received';
    public const CANCELLED = 'cancelled';
}
