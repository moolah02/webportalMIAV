<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Report Builder Settings
    |--------------------------------------------------------------------------
    */

    'enabled' => env('REPORT_BUILDER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Query Limits
    |--------------------------------------------------------------------------
    */

    'limits' => [
        'preview_max_rows' => env('REPORT_PREVIEW_LIMIT', 10000),
        'preview_default_rows' => env('REPORT_PREVIEW_DEFAULT', 100),
        'export_max_rows' => env('REPORT_EXPORT_LIMIT', null),
        'query_timeout' => env('REPORT_QUERY_TIMEOUT', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Settings
    |--------------------------------------------------------------------------
    */

    'exports' => [
        'formats' => ['csv', 'xlsx', 'pdf'],

        'csv' => [
            'delimiter' => ',',
            'enclosure' => '"',
            'encoding' => 'UTF-8',
        ],

        'pdf' => [
            'orientation' => 'landscape',
            'paper_size' => 'A4',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */

    'security' => [
        'rate_limit' => [
            'enabled' => true,
            'max_attempts' => 10,
            'decay_minutes' => 1,
        ],

        'audit' => [
            'enabled' => true,
            'log_previews' => true,
            'log_exports' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Timezone Settings
    |--------------------------------------------------------------------------
    */

    'timezone' => env('REPORT_TIMEZONE', 'Africa/Harare'),
    'week_start' => env('REPORT_WEEK_START', 'monday'),
];
