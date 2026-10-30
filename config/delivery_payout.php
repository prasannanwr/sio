<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Delivery Man Payout Settings
    |--------------------------------------------------------------------------
    */

    'currency' => env('DELIVERY_PAYOUT_CURRENCY', env('MONNIFY_CURRENCY', 'NGN')),

    'default_commission_percentage' => env('DELIVERY_PAYOUT_DEFAULT_COMMISSION_PERCENTAGE', 1),

    'payment_provider' => env('DELIVERY_PAYOUT_PAYMENT_PROVIDER', 'monnify'),

    'eligible_order_statuses' => [
        'delivered',
        'Customer Received',
        'Accepted By Agent',
    ],

    'eligible_delivery_man_status' => 'Delivered',
];
