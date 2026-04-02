<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Timezone
    |--------------------------------------------------------------------------
    |
    | The default timezone for new schedules. When null, the app timezone is used.
    |
    */
    'timezone' => null,

    /*
    |--------------------------------------------------------------------------
    | Column Names
    |--------------------------------------------------------------------------
    |
    | The JSON column names expected on the model. These are convention defaults
    | used by the HasBusinessHours trait when no explicit column is specified.
    |
    */
    'columns' => [
        'hours' => 'business_hours',
        'exceptions' => 'business_hours_exceptions',
        'timezone' => 'business_hours_timezone',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Hours
    |--------------------------------------------------------------------------
    |
    | Default schedule applied when creating a new business hours entry.
    |
    */
    'defaults' => [
        'monday' => ['09:00-17:00'],
        'tuesday' => ['09:00-17:00'],
        'wednesday' => ['09:00-17:00'],
        'thursday' => ['09:00-17:00'],
        'friday' => ['09:00-17:00'],
        'saturday' => [],
        'sunday' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Display Settings
    |--------------------------------------------------------------------------
    |
    | Configure how business hours are displayed across form fields,
    | table columns, and infolist entries.
    |
    */
    'display' => [
        'time_format' => 'H:i',
        'closed_label' => 'Closed',
        'open_label' => 'Open',
    ],
];
