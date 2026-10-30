<?php

return [

    /*
    |--------------------------------------------------------------------------
    | VTU Provider
    |--------------------------------------------------------------------------
    |
    | Use the mock driver for local/mobile integration while the provider
    | account is not ready. Set VTU_PROVIDER_DRIVER=monnify to sell through
    | Monnify Bills Payment, or VTU_PROVIDER_DRIVER=http and configure the
    | base URL/endpoints when connecting another VTU aggregator.
    |
    */

    'currency' => env('VTU_CURRENCY', 'NGN'),
    'require_successful_payment' => env('VTU_REQUIRE_SUCCESSFUL_PAYMENT', true),

    /*
    |--------------------------------------------------------------------------
    | Monnify Bills Payment (VTU_PROVIDER_DRIVER=monnify)
    |--------------------------------------------------------------------------
    |
    | Uses the Monnify credentials from config/monnify.php. Purchases are paid
    | from the merchant's Monnify wallet, which must be funded.
    |
    */

    'monnify' => [
        // Services sold through Monnify. The rest respond "not available yet".
        'services' => array_values(array_filter(array_map('trim', explode(',', (string) env('VTU_MONNIFY_SERVICES', 'airtime'))))),

        // User IDs who can use every service, including ones not switched on above, so a
        // service can be tested live before customers see it. Example: VTU_MONNIFY_TEST_USERS=42,57
        'test_users' => array_values(array_filter(array_map('intval', explode(',', (string) env('VTU_MONNIFY_TEST_USERS', ''))))),

        'categories' => [
            'airtime' => 'AIRTIME',
            'data' => 'DATA_BUNDLE',
            'cable_tv' => 'CABLE_TV',
            'electricity' => 'ELECTRICITY',
            'bills' => 'EDUCATION',
        ],

        // The billers offered for each service, keyed by the code the app uses. The app's
        // provider lists come from here rather than Monnify, whose biller list ignores the
        // category filter and returns over 5,000 billers. Electricity companies are listed
        // twice, as prepaid and postpaid. 'product' pins the product for a biller that has
        // more than one.
        'billers' => [
            'airtime' => [
                'mtn' => ['code' => 'MTN', 'name' => 'MTN'],
                'airtel' => ['code' => 'AIRTEL', 'name' => 'Airtel'],
                'glo' => ['code' => 'GLO', 'name' => 'Glo'],
                '9mobile' => ['code' => '9MOBILE', 'name' => '9mobile'],
            ],
            'data' => [
                'mtn' => ['code' => 'MTN', 'name' => 'MTN'],
                'airtel' => ['code' => 'AIRTEL', 'name' => 'Airtel'],
                'glo' => ['code' => 'GLO', 'name' => 'Glo'],
                '9mobile' => ['code' => '9MOBILE', 'name' => '9mobile'],
            ],
            'cable_tv' => [
                'dstv' => ['code' => 'biller-dstv', 'name' => 'DStv'],
                'gotv' => ['code' => 'biller-gotv', 'name' => 'GOtv'],
                'startimes' => ['code' => 'biller-startimes', 'name' => 'StarTimes'],
                'showmax' => ['code' => 'biller-showmax', 'name' => 'Showmax'],
            ],
            'electricity' => [
                'ikeja-electric-prepaid' => ['code' => 'biller-ikedc-pre', 'name' => 'Ikeja Electric (Prepaid)'],
                'ikeja-electric-postpaid' => ['code' => 'biller-ikedc-post', 'name' => 'Ikeja Electric (Postpaid)'],
                'eko-electric-prepaid' => ['code' => 'biller-ekedc-pre', 'name' => 'Eko Electric (Prepaid)'],
                'eko-electric-postpaid' => ['code' => 'biller-ekedc-post', 'name' => 'Eko Electric (Postpaid)'],
                'abuja-electric-prepaid' => ['code' => 'biller-aedc-pre', 'name' => 'Abuja Electricity (Prepaid)'],
                'abuja-electric-postpaid' => ['code' => 'biller-aedc-post', 'name' => 'Abuja Electricity (Postpaid)'],
                'kano-electric-prepaid' => ['code' => 'biller-kedc-pre', 'name' => 'Kano Electricity (Prepaid)'],
                'kaduna-electric-prepaid' => ['code' => 'biller-knedc-pre', 'name' => 'Kaduna Electric (Prepaid)'],
                'kaduna-electric-postpaid' => ['code' => 'biller-knedc-post', 'name' => 'Kaduna Electric (Postpaid)'],
                'portharcourt-electric-prepaid' => ['code' => 'biller-phedc-pre', 'name' => 'Port Harcourt Electricity (Prepaid)'],
                'portharcourt-electric-postpaid' => ['code' => 'biller-phedc-post', 'name' => 'Port Harcourt Electricity (Postpaid)'],
                'enugu-electric-prepaid' => ['code' => 'biller-eedc-pre', 'name' => 'Enugu Electricity (Prepaid)'],
                'enugu-electric-postpaid' => ['code' => 'biller-eedc-post', 'name' => 'Enugu Electricity (Postpaid)'],
                'ibadan-electric-prepaid' => ['code' => 'biller-ibedc-pre', 'name' => 'Ibadan Electricity (Prepaid)'],
                'ibadan-electric-postpaid' => ['code' => 'biller-ibedc-post', 'name' => 'Ibadan Electricity (Postpaid)'],
                'jos-electric-prepaid' => ['code' => 'biller-jedc-pre', 'name' => 'Jos Electricity (Prepaid)'],
                'jos-electric-postpaid' => ['code' => 'biller-jedc-post', 'name' => 'Jos Electricity (Postpaid)'],
                'benin-electric-postpaid' => ['code' => 'bedc', 'name' => 'Benin Electricity (Postpaid)', 'product' => 'bedc_postpaid'],
                'yola-electric-prepaid' => ['code' => 'biller-yola-pre', 'name' => 'Yola Electricity (Prepaid)'],
                'yola-electric-postpaid' => ['code' => 'biller-yola-post', 'name' => 'Yola Electricity (Postpaid)'],
                'aba-power-prepaid' => ['code' => 'biller-aba-pre', 'name' => 'Aba Power (Prepaid)'],
                'aba-power-postpaid' => ['code' => 'biller-aba-post', 'name' => 'Aba Power (Postpaid)'],
            ],
            'bills' => [
                'waec' => ['code' => 'biller-ed-waec', 'name' => 'WAEC'],
                'jamb' => ['code' => 'biller-jamb', 'name' => 'JAMB'],
            ],
        ],

        // Validate the customer with Monnify before the customer is charged.
        'validate_before_payment' => env('VTU_MONNIFY_VALIDATE_BEFORE_PAYMENT', true),

        'cache_minutes' => env('VTU_MONNIFY_CACHE_MINUTES', 60),

        // Pin a product code per network if the automatic "Mobile Top up" choice is ever wrong.
        'products' => [
            'airtime' => [
                'mtn' => env('VTU_MONNIFY_AIRTIME_MTN'),
                'airtel' => env('VTU_MONNIFY_AIRTIME_AIRTEL'),
                'glo' => env('VTU_MONNIFY_AIRTIME_GLO'),
                '9mobile' => env('VTU_MONNIFY_AIRTIME_9MOBILE'),
            ],
        ],
    ],

    'provider' => [
        'driver' => env('VTU_PROVIDER_DRIVER', env('VTU_PROVIDER_BASE_URL') ? 'http' : 'mock'),
        'base_url' => env('VTU_PROVIDER_BASE_URL'),
        'api_key' => env('VTU_PROVIDER_API_KEY'),
        'secret_key' => env('VTU_PROVIDER_SECRET_KEY'),
        'auth_scheme' => env('VTU_PROVIDER_AUTH_SCHEME', 'Bearer'),
        'api_key_header' => env('VTU_PROVIDER_API_KEY_HEADER', 'Authorization'),
        'timeout' => env('VTU_PROVIDER_TIMEOUT', 30),

        'endpoints' => [
            'providers' => [
                'method' => env('VTU_PROVIDER_PROVIDERS_METHOD', 'GET'),
                'path' => env('VTU_PROVIDER_PROVIDERS_PATH', '/vtu/{service_type}/providers'),
            ],
            'products' => [
                'method' => env('VTU_PROVIDER_PRODUCTS_METHOD', 'GET'),
                'path' => env('VTU_PROVIDER_PRODUCTS_PATH', '/vtu/{service_type}/{provider_code}/products'),
            ],
            'validate_customer' => [
                'method' => env('VTU_PROVIDER_VALIDATE_METHOD', 'POST'),
                'path' => env('VTU_PROVIDER_VALIDATE_PATH', '/vtu/validate-customer'),
            ],
            'purchase' => [
                'method' => env('VTU_PROVIDER_PURCHASE_METHOD', 'POST'),
                'path' => env('VTU_PROVIDER_PURCHASE_PATH', '/vtu/purchase'),
            ],
            'status' => [
                'method' => env('VTU_PROVIDER_STATUS_METHOD', 'GET'),
                'path' => env('VTU_PROVIDER_STATUS_PATH', '/vtu/transactions/{reference}'),
            ],
        ],
    ],

    'services' => [
        'airtime' => [
            'label' => 'Airtime',
            'description' => 'Buy mobile airtime top-ups.',
            'customer_label' => 'Phone number',
            'requires_product' => false,
            'supports_validation' => false,
            // Monnify's top-up minimum on every network.
            'min_amount' => 100,
        ],
        'data' => [
            'label' => 'Data',
            'description' => 'Buy mobile data bundles.',
            'customer_label' => 'Phone number',
            'requires_product' => true,
            'supports_validation' => false,
            'min_amount' => 50,
        ],
        'cable_tv' => [
            'label' => 'Cable TV',
            'description' => 'Pay TV subscriptions and renewals.',
            'customer_label' => 'Smartcard/IUC number',
            'requires_product' => true,
            'supports_validation' => true,
            'min_amount' => 100,
        ],
        'electricity' => [
            'label' => 'Electricity',
            'description' => 'Pay prepaid and postpaid power bills.',
            'customer_label' => 'Meter number',
            'requires_product' => false,
            'supports_validation' => true,
            'min_amount' => 100,
        ],
        'bills' => [
            'label' => 'Bills',
            'description' => 'Pay education, government, and other bills.',
            'customer_label' => 'Customer reference',
            'requires_product' => true,
            'supports_validation' => true,
            'min_amount' => 100,
        ],
    ],

    'providers' => [
        'airtime' => [
            ['code' => 'mtn', 'name' => 'MTN'],
            ['code' => 'airtel', 'name' => 'Airtel'],
            ['code' => 'glo', 'name' => 'Glo'],
            ['code' => '9mobile', 'name' => '9mobile'],
        ],
        'data' => [
            ['code' => 'mtn', 'name' => 'MTN'],
            ['code' => 'airtel', 'name' => 'Airtel'],
            ['code' => 'glo', 'name' => 'Glo'],
            ['code' => '9mobile', 'name' => '9mobile'],
        ],
        'cable_tv' => [
            ['code' => 'dstv', 'name' => 'DStv'],
            ['code' => 'gotv', 'name' => 'GOtv'],
            ['code' => 'startimes', 'name' => 'StarTimes'],
        ],
        'electricity' => [
            ['code' => 'ikeja-electric', 'name' => 'Ikeja Electric'],
            ['code' => 'eko-electric', 'name' => 'Eko Electric'],
            ['code' => 'abuja-electric', 'name' => 'Abuja Electricity'],
            ['code' => 'kano-electric', 'name' => 'Kano Electricity'],
            ['code' => 'portharcourt-electric', 'name' => 'Port Harcourt Electricity'],
            ['code' => 'enugu-electric', 'name' => 'Enugu Electricity'],
        ],
        'bills' => [
            ['code' => 'waec', 'name' => 'WAEC'],
            ['code' => 'jamb', 'name' => 'JAMB'],
            ['code' => 'education', 'name' => 'Education Bills'],
            ['code' => 'government', 'name' => 'Government Bills'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mock Products
    |--------------------------------------------------------------------------
    |
    | These are only used by the mock driver so the mobile app can be developed
    | before provider plan codes are finalized.
    |
    */

    'products' => [
        'airtime' => [
            'mtn' => [
                ['code' => 'airtime', 'name' => 'Airtime Top-up', 'amount' => null, 'is_variable_amount' => true],
            ],
            'airtel' => [
                ['code' => 'airtime', 'name' => 'Airtime Top-up', 'amount' => null, 'is_variable_amount' => true],
            ],
            'glo' => [
                ['code' => 'airtime', 'name' => 'Airtime Top-up', 'amount' => null, 'is_variable_amount' => true],
            ],
            '9mobile' => [
                ['code' => 'airtime', 'name' => 'Airtime Top-up', 'amount' => null, 'is_variable_amount' => true],
            ],
        ],
        'data' => [
            'mtn' => [
                ['code' => 'mtn-demo-1gb-30d', 'name' => 'Demo 1GB - 30 Days', 'amount' => 350, 'is_variable_amount' => false],
                ['code' => 'mtn-demo-2gb-30d', 'name' => 'Demo 2GB - 30 Days', 'amount' => 700, 'is_variable_amount' => false],
            ],
            'airtel' => [
                ['code' => 'airtel-demo-1gb-30d', 'name' => 'Demo 1GB - 30 Days', 'amount' => 350, 'is_variable_amount' => false],
                ['code' => 'airtel-demo-2gb-30d', 'name' => 'Demo 2GB - 30 Days', 'amount' => 700, 'is_variable_amount' => false],
            ],
            'glo' => [
                ['code' => 'glo-demo-1gb-30d', 'name' => 'Demo 1GB - 30 Days', 'amount' => 350, 'is_variable_amount' => false],
                ['code' => 'glo-demo-2gb-30d', 'name' => 'Demo 2GB - 30 Days', 'amount' => 700, 'is_variable_amount' => false],
            ],
            '9mobile' => [
                ['code' => '9mobile-demo-1gb-30d', 'name' => 'Demo 1GB - 30 Days', 'amount' => 350, 'is_variable_amount' => false],
                ['code' => '9mobile-demo-2gb-30d', 'name' => 'Demo 2GB - 30 Days', 'amount' => 700, 'is_variable_amount' => false],
            ],
        ],
        'cable_tv' => [
            'dstv' => [
                ['code' => 'dstv-demo-access', 'name' => 'Demo Access Bouquet', 'amount' => 2500, 'is_variable_amount' => false],
            ],
            'gotv' => [
                ['code' => 'gotv-demo-smallie', 'name' => 'Demo Smallie Bouquet', 'amount' => 1500, 'is_variable_amount' => false],
            ],
            'startimes' => [
                ['code' => 'startimes-demo-basic', 'name' => 'Demo Basic Bouquet', 'amount' => 1200, 'is_variable_amount' => false],
            ],
        ],
        'electricity' => [],
        'bills' => [
            'waec' => [
                ['code' => 'waec-demo-pin', 'name' => 'Demo Result Checker PIN', 'amount' => 1500, 'is_variable_amount' => false],
            ],
            'jamb' => [
                ['code' => 'jamb-demo-epin', 'name' => 'Demo JAMB e-PIN', 'amount' => 5000, 'is_variable_amount' => false],
            ],
        ],
    ],
];
