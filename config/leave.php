<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Leave Management Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for leave management system
    |
    */

    'annual_leave' => [
        'max_days_per_year' => 20,
        'max_consecutive_days' => 30,
        'advance_notice_days' => 3,
    ],

    'sick_leave' => [
        'max_days_per_year' => 10,
        'requires_document' => true,
    ],

    'casual_leave' => [
        'max_days_per_year' => 5,
    ],

    'carry_forward' => [
        'enabled' => true,
        'max_days' => 5,
    ],
];