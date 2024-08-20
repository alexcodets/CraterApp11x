<?php

return [
    'validation' => [
        'extension' => [
            'pin' => [
                'digits' => 'The Pin must have exactly 4 digits',
                'integer' => 'The Pin must have only digits',
            ],
            'protocol' => [
                'in' => 'The selected protocol is invalid, valid values: sip, iax.',
            ],
            'extension' => [
                'digits' => 'The Extension must have exactly 4 digits',
                'integer' => 'The Extension must have only digits',
            ],
            'ua_id' => [
                'integer' => 'The UA must have only digits',
            ],
            'auto_provisioning' => [
                'boolean' => 'The Auto provisioning must be true or false',
            ],
            'mac_address' => [
                'regex' => 'The mac address format is invalid it cannot have special characters, must only contain numbers and letters',
            ]
        ],
        'did' => [
            'did' => [
                'in' => 'The selected did is invalid, valid values: sip, iax',
            ],
            'dest_type' => [
                'between' => 'The selected dest_type is invalid, valid values: 0, 1, 2, 3, 4, 5, 6',
                'integer' => 'The selected dest_type is invalid, the value must be a number between 0 and 6',
            ],
            'disabled' => [
                'between' => 'The selected disabled is invalid, valid values: 0 (enabled), 1 (disabled)',
                'integer' => 'The selected disabled is invalid, the value must be a number between 0 and 1',
            ],
        ],
    ],
];
