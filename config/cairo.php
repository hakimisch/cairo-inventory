<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Asset Classification Threshold (RM)
    |--------------------------------------------------------------------------
    |
    | Items with unit_price >= this value are classified as 'fixed_asset' (PA-3).
    | Items below are 'inventory' (PA-2).
    | Per Pekeliling Perbendaharaan — KEW.PA-2 vs KEW.PA-3 boundary.
    |
    */
    'asset_threshold' => 2000,

    /*
    |--------------------------------------------------------------------------
    | Asset Tag Format
    |--------------------------------------------------------------------------
    */
    'asset_tag_prefix' => 'AST',

    /*
    |--------------------------------------------------------------------------
    | Receiving Number Format
    |--------------------------------------------------------------------------
    */
    'receive_no_prefix' => 'REC',

    /*
    |--------------------------------------------------------------------------
    | Public Self-Registration
    |--------------------------------------------------------------------------
    | When false (default), the /register routes are not registered and the
    | Register link is hidden. Set REGISTRATION_ENABLED=true in .env only if
    | campus users genuinely need open self-service (ideally with an
    | email-allowlist / invite mechanism instead).
    */
    'registration_enabled' => env('REGISTRATION_ENABLED', false),
];
