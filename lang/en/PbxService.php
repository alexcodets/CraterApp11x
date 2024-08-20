<?php

return [
    'extension' => [
        'suspend' => [
            'status' => ['Disabled', 'Enabled'],
            'errors' => [
                'extensionid' => 'The ExtensionId for the extension :extension is required for this process',
                'tenantcode' => 'The Tenant Code for the extension :extension is required for this process',
                'check_connection' => 'There is a problem with the connection to the PbxWareApi - ',
                'check_config' => 'There was a problem with the extension check - ',
                'status' =>
                [
                    'disabled' => 'The Extension :extension is already disabled',
                    'enabled' => 'The Extension :extension is already Enabled',
                ],
                'restoring_codecs' => 'Error while Restoring Codecs - ',
            ],
            'success' =>
            [
                'suspend' => 'The Extension :extension was Disabled',
                'unsuspend' => 'The Extension :extension was Enabled',
            ],
        ],
    ],
];
