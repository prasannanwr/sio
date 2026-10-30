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

    'api_key'        => env('MONNIFY_API_KEY', env('MONNIFY_CLIENT_ID')),
    'secret_key'     => env('MONNIFY_SECRET_KEY', env('MONNIFY_CLIENT_SECRET')),
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
    | Secret used to validate the signature on incoming webhooks. Monnify signs
    | webhook payloads with the merchant client secret, but this can be
    | overridden if your dashboard provides a separate webhook secret.
    |--------------------------------------------------------------------------
    */

    'webhook_secret' => env('MONNIFY_WEBHOOK_SECRET', env('MONNIFY_SECRET_KEY')),

    /*
    |--------------------------------------------------------------------------
    | Redirect URL after payment (used by the mobile app deep link or web).
    |--------------------------------------------------------------------------
    */

    'redirect_url' => env('MONNIFY_REDIRECT_URL'),

    /*
    |--------------------------------------------------------------------------
    | Disbursement / Payout Settings
    |--------------------------------------------------------------------------
    */

    'disbursement' => [
        'source_account_number' => env('MONNIFY_DISBURSEMENT_SOURCE_ACCOUNT_NUMBER'),
        'async' => env('MONNIFY_DISBURSEMENT_ASYNC', true),
        'verify_account_before_transfer' => env('MONNIFY_VERIFY_ACCOUNT_BEFORE_TRANSFER', true),
    ],
];
