<?php

return [
    'name' => 'Avalara Tax',
    'description' => 'Avalara Tax Service',
    'success' => [
        'server_up' => 'The server is Running just fine',
        'taxes' => 'Taxes calculated',
        'pcode' => 'Pcode Found',
        'commit' => 'The Doc was committed successful',
        'uncommit' => 'The Doc was uncommitted successful',
        'taxtypes' => 'TaxTypes:',
        'tspair' => 'Transaction Service List.'
    ],
    'error' => [
        'pcode' => 'There was no match PCode',
        'invoice' => [
            'date' => 'The Invoice Date Format Is incorrect.',
        ],
        'client' => 'Client Error (local)',
        'server' => 'Server Error (Avira server error)',
        'general' => 'General error',
        'no_expected' => 'There was a unexpected error',
        'server_down' => 'The server is Down',
        'models' => [
            'config' => [
                'not_found' => 'There is not a valid Avalara Configuration for the User/Company/Service'
            ],
            'company' => [
                'not_found' => 'There is not a valid Company for the the User'
            ]
        ],
        'not_avalara_item' => [
            'code' => 9001,
            'message' => 'The item is not avalara type',
        ],
        'ts_pair' => [
            'transaction' => [
                'required' => [
                    'code' => 9002,
                    'message' => 'The ID for ts_pair transaction (avalara_type) is required.',
                ],
                'invalid' => [
                    'code' => 9002,
                    'message' => 'The item has invalid id  for ts_pair transaction (avalara_type).',
                ],
            ],
            'service' => [
                'required' => [
                    'code' => 9002,
                    'message' => 'The ID for ts_pair Service (avalara_service_type) is required.',
                ],
                'invalid' => [
                    'code' => 9002,
                    'message' => 'The item has invalid id  for ts_pair Service (avalara_service_type).',
                ],
            ],
        ],
        'invalid_ts_pair_data' => [
            'code' => 9002,
            'message' => 'The item has invalid or incomplete data for: transaction or service data',
        ],
        'forbidden' => 'Forbidden',
        'unauthorized' => 'Unauthorized, The credentials are incorrect',
        'location' => [
            'address' => [
                'required' => [
                    'model' => 'The billing Address Data of the user is Required',
                    'state' => 'The billing Address State of the user is Required',
                    'country' => 'The billing Address Country of the user is required'
                ],
            ]
        ],
        'company_model' => [
            'items' => [
                'required' => [
                    'cdr' => 'Id for item Cdr Is required',
                    'did' => 'Id for item Did Is required',
                    'ext' => 'Id for item Extension Is required'

                ],
            ],
            'required' => ['base' => 'The Avalara Config data is incomplete, "bscl","svcl","fclt","reg","frch" fields are required'],
        ],
        'item' => [
            'required' => [
                'id' => 'Id for item Cdr Is required'
            ]
        ],

    ],
    'tax_services' => [
        'error' => [
            'avalara_bool' => [
                'null' => 'The item is not a avalara type'
            ],
            'payment_type' => [
                'null' => 'A payment type is required',
                'invalid' => 'A valid payment type is required',
            ],
            'not_avalara_item' => [
                'code' => 9001,
                'message' => 'The item is not avalara type',
            ],
            'service_type' => [
                'invalid' => 'Service type ID :id, associated to the Item :item is no Valid (not found)',
                'null' => 'The Service Type ID is required for the item: :item'
            ],
            'avalara_type' => [
                'invalid' => 'Avalara type ID :id, associated to the Item is no Valid (not found)',
                'null' => 'The Avalara Type ID is required for the item: :item'
            ],
            'invalid_ts_pair_data' => [
                'code' => 9002,
                'message' => 'The item has invalid or incomplete data for: transaction or service data',
            ],
        ]
    ]
];
