<?php

return [
    'dev' => [
        'base_url' => env('PBX_SERVER_HOST_DEV'),
        'private_key' => env('PBX_SERVER_KEY_DEV'),
        'tenant' => [
            'code' => env('PBX_SERVER_TENANT_CODE_DEV'),
            'name' => env('PBX_SERVER_TENANT_NAME_DEV'),
            'details' => env('PBX_SERVER_TENANT_PACKAGE_DEV'),
            'tenantid' => env('PBX_SERVER_TENANT_ID_DEV'),
        ],
    ],
    'prod' => [
        'base_url' => env('PBX_SERVER_HOST_PROD'),
        'private_key' => env('PBX_SERVER_KEY_PROD'),
        'tenant' => [
            'code' => env('PBX_SERVER_TENANT_CODE_PROD'),
            'name' => env('PBX_SERVER_TENANT_NAME_PROD'),
            'details' => env('PBX_SERVER_TENANT_PACKAGE_PROD'),
            'tenantid' => '',

        ],
    ],
    'selected' => env('PBX_SERVER_SELECTED', 'dev'),

];
