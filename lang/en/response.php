<?php

return [
    'modules' => [
        'avalara' => [
            'errors' => [
                'not_found' => 'The Avalara module is not installed',
                'no_config' => 'There is not a valid Avalara Config register'
            ],
            'success' => 'The Avalara Module is installed.',
        ],
        'general' => [
            'errors' => [
                'not_found' => 'the :name module is not installed'
            ],
            'success' => [
                'found' => 'the :name module is installed'
            ]
        ]
    ]
];
