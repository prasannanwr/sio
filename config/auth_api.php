<?php

return [

    'defaults' => [
        'user_type' => env('AUTH_DEFAULT_USER_TYPE', 'user'),
        'country_id' => env('AUTH_DEFAULT_COUNTRY_ID', 1),
    ],

    'jwt' => [
        'secret' => env('AUTH_API_JWT_SECRET') ?: env('JWT_SECRET') ?: env('APP_KEY'),
        'ttl_minutes' => env('JWT_TTL_MINUTES', 60 * 24 * 7),
        'issuer' => env('APP_URL', 'http://localhost'),
    ],

    'otp' => [
        'ttl_minutes' => env('AUTH_OTP_TTL_MINUTES', 10),
        'max_attempts' => env('AUTH_OTP_MAX_ATTEMPTS', 5),
        'expose_debug_otp' => env('AUTH_EXPOSE_DEBUG_OTP', env('APP_ENV') !== 'production'),
    ],

];
