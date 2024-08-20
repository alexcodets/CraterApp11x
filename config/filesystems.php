<?php

return [

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    'disks' => [
        'reports' => [
            'driver' => 'local',
            'root' => storage_path('app/reports'),
            'visibility' => 'public',
            'permissions' => [
                'file' => [
                    'public' => 0666,
                    'private' => 0600,
                ],
                'dir' => [
                    'public' => 0755,
                    'private' => 0700,
                ],
            ],
        ],

        'seed' => [
            'driver' => 'local',
            'root' => storage_path('app/seed'),
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'root' => env('AWS_ROOT'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

        'media' => [
            'driver' => 'local',
            'root' => public_path('media'),
        ],

        'uploads' => [
            'driver' => 'local',
            'root' => public_path('uploads/settings'),
        ],

        'doSpaces' => [
            'type' => 'AwsS3',
            'driver' => 's3',
            'key' => env('DO_SPACES_KEY'),
            'secret' => env('DO_SPACES_SECRET'),
            'region' => env('DO_SPACES_REGION'),
            'bucket' => env('DO_SPACES_BUCKET'),
            'root' => env('DO_SPACES_ROOT'),
            'endpoint' => env('DO_SPACES_ENDPOINT'),
            'use_path_style_endpoint' => false,
        ],

        'dropbox' => [
            'driver' => 'dropbox',
            'type' => 'DropboxV2',
            'token' => env('DROPBOX_TOKEN'),
            'key' => env('DROPBOX_KEY'),
            'secret' => env('DROPBOX_SECRET'),
            'app' => env('DROPBOX_APP'),
            'root' => env('DROPBOX_ROOT'),
        ],
    ],

];
