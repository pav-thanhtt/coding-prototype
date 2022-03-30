<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'db_doc' => [
        'headers' => [
            'table_name' => [
                'header' => ['Table'],
                'bgColor' => 'F9CB9C',
                'coordinate' => ['A']
            ],
            'fields' => [
                'header' => [
                    'Field',
                    'Type',
                    'Default Value',
                    'Unsigned',
                    'Nullable',
                    'Charset',
                    'Collation',
                    'Comments',
                    'Note'
                ],
                'bgColor' => 'F9CB9C',
                'coordinate' => range('A', 'I')
            ],
            'indexes' => [
                'header' => ['Indexes'],
                'bgColor' => 'C9DAF8',
                'coordinate' => ['A']
            ],
            'indexes_detail' => [
                'header' => ['Key', 'Type', 'Unique', 'Columns'],
                'bgColor' => 'C9DAF8',
                'coordinate' => range('A', 'D')
            ],
            'script' => [
                'header' => ['Table Script'],
                'bgColor' => 'FFF2CC',
                'coordinate' => ['A']
            ]
        ],
        'coordinate' => range('A', 'I')
    ]

];
