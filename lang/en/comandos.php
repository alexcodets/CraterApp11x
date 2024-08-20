<?php

return [
    'updateExtensionStatus' => [
        'email' => [
            'enabled' => '',
            'disabled' => ''
        ],
        'logs' => [
            'change' => '     La extension :name had status: :status, y now will change to :newStatus'
        ],
        'errors' => [
            'email' => [
                'not_send' => 'Error the message could not be send, error: :error',
                'receipt_null' => 'The email is required to send a email..',
                'body_null' => 'The Email Body/Message is required.'
            ],
            'api' => 'There was a unexpected error with the api: :error'
        ]
    ],
    'PbxServiceMainUpdate' => [
        'api' => [
            'connection' => 'Handle: with PbxServices id: :serviceId have a connection problem with the error: :message',
            'did' => 'processDid: with PbxServices id: :serviceId have a problem with the error: :message',
            'ext' => 'Command:pbx:serviceMainUpdate with PbxServices id: :serviceId have a getExtensions problem with the error: :message'

        ]
    ],
    'checkConnection' => [
        'errors' => [
            'mail' => [
                'notification_deactivated' => 'notification are deactivated',
                'no_email' => 'The notifications are activated but there is not valid email direction registered',
            ],
            'no_log' => [
                'up' => 'Was created with I status',
            ]
        ],
        'up' => 'Server is Up',
        'down' => 'Server Is Down',
    ],
    'general' => [
        'notifications' => [
            'disabled' => 'Notifications are disabled for the company',
            'no_email' => 'The notifications are enabled but there is not a valid email direction registered'
        ]
    ],
    'creditCardReminder' => [
        'info' => [
            'not_15' => 'Today is not 15th of the month',
            'run' => "  The company with id: :company has a PaymentAccount with id: :account that will expire next month"
        ],
    ]
];
