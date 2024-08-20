<?php

return [
    'tenant' => [
        'status' => ['Disabled', 'Enabled'],
        'errors' => [
            'tenantid' => 'The TenantId is required for this process',
            'check_connection' => 'There is a problem with the connection to the PbxWareApi - ',
            'status' => [
                'The tenant is already disabled',
                'The tenant is already Enabled',
            ],
            'restoring_codecs' => 'Error while Restoring Codecs - ',
        ],
        'success' =>
            [
                'suspend' => 'The Tenant was Suspended',
                'unsuspend' => 'The tenant was Unsuspended',
                'unsuspendOld' => 'The tenant was Unsuspended but was previously suspended with a older version of pbxSystem so the codecs from the tenants could be have change. Previous codecs: :codecs. Previous Channels: :channels '
            ],
    ],

];
