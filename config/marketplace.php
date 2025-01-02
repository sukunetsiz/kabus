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
    'require_reference_code' => env('MARKETPLACE_REQUIRE_REFERENCE', true),

    /*
    |--------------------------------------------------------------------------
    | JavaScript Warning Display Settings
    |--------------------------------------------------------------------------
    |
    | This option controls the visibility of the JavaScript warning in the footer.
    | When set to true, the warning will be displayed to users.
    | When false, the warning will be hidden.
    |
    */
    'show_javascript_warning' => env('MARKETPLACE_SHOW_JS_WARNING', true),
];
