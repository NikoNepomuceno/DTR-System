<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloud Environment Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file contains settings specific to cloud deployment
    | environments like Laravel Cloud, Forge, etc.
    |
    */

    'session' => [
        /*
        |--------------------------------------------------------------------------
        | Cloud Session Configuration
        |--------------------------------------------------------------------------
        |
        | These settings help ensure sessions work properly in cloud environments
        | where HTTPS is enforced and domain configurations may differ.
        |
        */
        'secure_cookie' => env('APP_ENV') === 'production',
        'same_site' => 'lax',
        'http_only' => true,
        'lifetime' => 120, // 2 hours
        'expire_on_close' => false,
    ],

    'auth' => [
        /*
        |--------------------------------------------------------------------------
        | Authentication Configuration for Cloud
        |--------------------------------------------------------------------------
        |
        | Additional authentication settings for cloud environments.
        |
        */
        'session_regenerate' => true,
        'force_session_save' => true,
        'debug_auth' => env('APP_DEBUG', false),
    ],

    'security' => [
        /*
        |--------------------------------------------------------------------------
        | Security Headers for Cloud
        |--------------------------------------------------------------------------
        |
        | Security headers that should be set in cloud environments.
        |
        */
        'force_https' => env('APP_ENV') === 'production',
        'hsts_max_age' => 31536000, // 1 year
    ],
];
