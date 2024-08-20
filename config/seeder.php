<?php

return [
    'payments' => [
        'paypal' => [
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret' => env('PAYPAL_SECRET'),
            'environment' => env('PAYPAL_ENVIRONMENT'),
            'merchant_id' => env('PAYPAL_MERCHANT_ID'),
            'currency' => env('PAYPAL_CURRENCY'),
            'public_key' => env('PAYPAL_PUBLIC_KEY'),
            'private_key' => env('PAYPAL_PRIVATE_KEY'),
            'email' => env('PAYPAL_EMAIL'),
        ],
        'authorize' => [
            'login_id' => env('AUTHORIZE_LOGIN_ID'),
            'transaction_key' => env('AUTHORIZE_TRANSACTION_KEY'),
        ]
    ],
];
