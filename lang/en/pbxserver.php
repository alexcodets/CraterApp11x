<?php

return [
    'name' => 'PbxServer',
    'description' => 'PbxServer Service',

    'error' => [
        'client' => 'Client Error (local)',
        'server' => 'Server Error (Avira server error)',
        'general' => 'General error',
        'no_expected' => 'There was a unexpected error',
        'server_down' => 'The server is Down',
        'tenant_configuration' => 'Tenant Configuration failed',
        'pcode' => 'There was no match PCode',
        'check' => 'Peace was never a option',
        'tenant' => [
            'suspend' => 'tenant :id unsuspend',
        ]
    ],
    'success' => [
        'tenants.index' => 'Tenant Index',
        'check' => 'Is alive!',
        'pcode' => 'Pcode Found',
        'commit' => 'The Doc was committed successful',
        'uncommit' => 'The Doc was uncommitted successful',
        'tenant_configuration' => 'Tenant Configuration',
        'tenant_package' => 'Tenant Packages',
        'routes' => 'Routes',
        'apps' => 'Apps List',
        'license' =>
            [
                'info' => 'License Information'
            ],
        'tenant' => [
            'suspend' => 'Tenant :id Suspended'
        ]
    ],

];
