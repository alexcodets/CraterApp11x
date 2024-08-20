<?php

return [

    'sandbox' =>
        [
            'user_name' => env('AVALARA_SANDBOX_USERNAME'),
            'password' => env('AVALARA_SANDBOX_PASSWORD'),
            'client_id' => env('AVALARA_SANDBOX_CLIENT_ID'),
            'url' => env('AVALARA_SANDBOX_URL'),
            'host' => 'communicationsua.avalara.net',
        ],
    'production' =>
        [
            'user_name' => env('AVALARA_PRODUCTION_USERNAME'),
            'password' => env('AVALARA_PRODUCTION_PASSWORD'),
            'client_id' => env('AVALARA_PRODUCTION_CLIENT_ID'),
            'url' => env('AVALARA_PRODUCTION_URL'),
            'host' => 'communicationsua.avalara.net',
        ],
    'environment' => 'sandbox',
    'services' => [
        'enhanced_services' => 577,
        'features' => 30,
        'install' => 8,
        'interstate' => 49,
        'invoice' => 43,
        'lines' => 21,
        'pbx' => 578,
        'pbx_extension' => 41,
        'pbx_outbound_channel' => 566,
        'toll_free' => 635,
        'trunk' => 578,
        'wireless_access_charge' => 48,
        'access_charge' => 6,

    ],
    'transaction_type' => 19,
    'actions' => [
        'status' => 'HealthCheck',
        'health' => 'HealthCheck',
        'profile' => 'profiles/GetProfiles',
        'calcTaxes' => 'afc/CalcTaxes',
        'pcode' => 'afc/PCode/',
        'commit' => 'afc/Commit',
        'serviceinfo' => 'afc/serviceinfo',
        'tsPair' => 'afc/tspairs',
        'taxTypes' => 'afc/taxtype/*',
        'taxType' => 'afc/taxtype/',

    ],
    'errors' => [
        'not_avalara_item' => [
            'code' => 9001,
            'message' => 'The item is not avalara type',
        ],
        'invalid_ts_pair_data' => [
            'code' => 9002,
            'message' => 'The item has invalid or incomplete data for: transaction or service data',
        ],
    ],
];
