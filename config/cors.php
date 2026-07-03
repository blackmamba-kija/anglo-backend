<?php

return [
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
    ],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        // Local development
        'http://localhost:8080',
        'http://localhost:8081',
        'http://127.0.0.1:8080',
        'http://127.0.0.1:8081',
        'http://localhost:5173',
        // Production frontend
        'https://anglo-store.kija.co.tz',
        'http://anglo-store.kija.co.tz',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
