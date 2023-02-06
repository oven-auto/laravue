<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

    'trusted' => [
        'http://192.168.1.130',
        'http://192.168.1.130:8080',
        'http://192.168.1.130:8082',
        'http://localhost',
        'http://localhost:8080',
        'http://localhost:8082',
        'http://192.168.1.98:8280',
        'http://192.168.1.6:8080',
        'http://192.168.1.6:8082',
        'http://192.168.1.6',

        'http://192.168.1.11:8080',
        'http://192.168.1.11:8082',
        'http://192.168.1.11',
    ],
];
