<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Monnify API Credentials
    |--------------------------------------------------------------------------
    |
    | Get these from your Monnify dashboard at https://app.monnify.com.
    | Use the SANDBOX keys for testing and switch to LIVE keys in production.
    |
    */

    'api_key'        => env('MONNIFY_API_KEY'),
    'secret_key'     => env('MONNIFY_SECRET_KEY'),
    'contract_code'  => env('MONNIFY_CONTRACT_CODE'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | Sandbox: https://sandbox.monnify.com
    | Live:    https://api.monnify.com
    |
    */

    'base_url' => env('MONNIFY_BASE_URL', 'https://sandbox.monnify.com'),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

    'currency' => env('MONNIFY_CURRENCY', 'NGN'),

    /*
    |--------------------------------------------------------------------------
    | Webhook secret used to validate the signature on incoming webhooks.
    |--------------------------------------------------------------------------
    */

    'webhook_secret' => env('MONNIFY_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Redirect URL after payment (used by the mobile app deep link or web).
    |--------------------------------------------------------------------------
    */

    'redirect_url' => env('MONNIFY_REDIRECT_URL'),
];
