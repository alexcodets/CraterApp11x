<?php

return [

    'successfuly' => 'Process Finished Successfully',
    'error' => '-----Error-----',
    'separator' => '---------------',
    'time' => '----------Time----------',
    'process' => [
        'main' => 'Main process: ',
        'start' => 'The process has started',
        'successfuly' => 'Process Finished Successfully',
    ],

    'cdr' => [
        'get' => 'getting cdr from api: ',
        'store' => 'Storing Cdrs into Database: ',
    ],
    'database' => [
        'start' => '----------Database Time----------',
    ],
    'errors' => [
        'title' => '-----Error-----',
        'general' => 'Error',
        'days_int' => 'The format for days must be int',
        'hours_int' => 'The format for hours must be int',
        'minutes_int' => 'The format for minutes must be int',
        'service_int' => 'The format for service must be int',
        'service' => [
            'call_rating' => 'The call rating is inactive',
            'status' => 'The package status is not active',
            'rate_per_minutes' => 'The rate per minutes is null',
            'minutes_increments' => 'The minutes increments is null',
            'not_found' => 'The service id is not a valid id for Pbx Service',
            'empty' => 'There was not valid service for the process',
            'timezone' => 'The Timezone is no valid',
        ],
        'pbx_service' => 'The pbx service With Id: :id have the following error:',
        'date' => [
            'start_date' => 'Wrong format for Start Date, format should be Y-m-d H:i:s',
            'end_date' => 'Wrong format for End Date, format should be Y-m-d H:i:s',
            'start_greater' => 'Start date :start cannot be Greater than End Date :end',
        ],
        'start_date' => 'Wrong format for Start Date, format should be Y-m-d H:i:s',
        'end_date' => 'Wrong format for End Date, format should be Y-m-d H:i:s',
        'api' => [
            'connection' => 'There was a error with the PBXConnectionCheck, the process will end',
            'connection_null' => 'The status for the pbxServer can not be null',
            'tenant_id' => 'Invalid Tenant ID',
            'tenant_id_null' => 'The tenant ID is required',
            'tenant_code_null' => 'The tenant CODE is required',
            'tenant_code' => 'invalid Tenant code',

        ],
        'mail' => [
            'subject' => 'Error: Import Cdr Tenant',
            'body' => 'A error has been found while attempting to import CDR from Tenant. \n ### Tenant \n -TenantId: :tenantId \n -Code: :code  \n\n ### PbxServer \n -Name: :serverLabel \n\n Error: ## :extraBody',
        ]

    ],
    'calculate' => [
        'start' => '----------Calculating Totals----------',
        'end' => '----------Totals ready----------',
        'totals' => '-------Total service calculated: :total----------',

    ],

];
