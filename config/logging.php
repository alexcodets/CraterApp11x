<?php


return [

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'tap' => env('LOG_EXTRA_DETAILS', false) ? [\Crater\Logging\CustomizeFormatter::class] : null,
            'channels' => ['daily'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'tap' => env('LOG_EXTRA_DETAILS', false) ? [\Crater\Logging\CustomizeFormatter::class] : null,
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'authorize' => [
            'driver' => 'single',
            'tap' => env('LOG_EXTRA_DETAILS', false) ? [\Crater\Logging\CustomizeFormatter::class] : null,
            'path' => storage_path('logs/authorize.log'),
            'level' => 'debug',
        ],

        'payment' => [
            'driver' => 'single',
            'tap' => env('LOG_EXTRA_DETAILS', false) ? [\Crater\Logging\CustomizeFormatter::class] : null,
            'path' => storage_path('logs/payment.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'tap' => env('LOG_EXTRA_DETAILS', false) ? [\Crater\Logging\CustomizeFormatter::class] : null,
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],

        'deprecation' => [
            'driver' => 'single',
            'path' => storage_path('logs/warnings.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],
    ],

];
