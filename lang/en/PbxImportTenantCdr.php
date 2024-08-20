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
        'service_int' => 'The Id: :id has a incorrect format, it must be int',
        'service' => [
            'call_rating' => 'The call rating is inactive',
            'status' => 'The package status is not active',
            'rate_per_minutes' => 'The rate per minutes is null',
            'minutes_increments' => 'The minutes increments is null',
            'not_found' => 'The service id is not a valid id for Pbx Service',
            'empty' => 'There was not valid service for the process',
            'timezone' => 'The Timezone is no valid',
        ],
        'server' => [
            'null' => 'PbxServer Not found for tenant :id',
            'deleted' => 'PbxServer for tenant :id is deleted',
            'inactive' => 'PbxServer for tenant :id is inactive',
        ],
        'pbx_service' => 'The pbx service With Id: :id have the following error:',
        'date' => [
            'start_date' => 'Wrong format for Start Date, format should be Y-m-d H:i:s',
            'end_date' => 'Wrong format for End Date, format should be Y-m-d H:i:s',
            'start_greater' => 'Start date :start cannot be Greater than End Date :end',
        ],
        'start_date' => 'Wrong format for Start Date, format should be Y-m-d H:i:s',
        'end_date' => 'Wrong format for End Date, format should be Y-m-d H:i:s',
        'tenant' => [
            'empty' => '- There was not match found for tenants',
            'invalid' => '- The tenant :id does not have a valid service.',
            'disable' => '- The tenant :id is inactive.',
            'active_job' => '- The tenant :id already has active jobs.',
            'empty_after' => '- There was not match found for tenants after validation.',
            'active_old_job' => '- The tenant :id has a active job for 2 days, it is now going back to queue.',

        ],

    ],
    'calculate' => [
        'start' => '----------Calculating Totals----------',
        'end' => '----------Totals ready----------',
        'totals' => '-------Total service calculated: :total----------',
    ],

    'log' => [
        'block' => [
            'end' => ' \\--------------------------------------------------------------/',
            'start' => ' /--------------------------------------------------------------\\',

        ],
        'command' => [
            'start' => '|---------------Command PbxTenantImportCdrs Start----------------|',
            'end' => '|---------------Command PbxTenantImportCdrs End------------------|',

        ],
        'validatiom' => [
            'start' => '<=====================Validation block start=====================>',
            'end' => '<======================Validation block end======================>'
        ],
        'success' => [
            'validation' => 'It goes beyond the validation'
        ],
        'tenant' => [
            'start' => '<=======================Tenants Loop Start=======================>',
            'end' => '<========================Tenants Loop End========================>',
        ],
        'batch' => [
            'start' => '<=======================Queue Batch Start=======================>',
            'end' => '<========================Queue Batch End========================>',
        ],
    ],

];
