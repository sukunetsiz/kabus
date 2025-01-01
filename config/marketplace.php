<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Reference Code Settings
    |--------------------------------------------------------------------------
    |
    | This option controls whether a reference code is required during registration.
    | When set to true, users must provide a valid reference code to register.
    | When false, the reference code becomes optional.
    |
    */
    'require_reference_code' => env('MARKETPLACE_REQUIRE_REFERENCE', false),
];
