<?php

return [

    'connections' => [
        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 1800,
            'expire' => 300,
            'timeout' => 300,
            'after_commit' => false,
        ],
    ],

];
