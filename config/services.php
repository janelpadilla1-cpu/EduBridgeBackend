<?php

return [
    'directorio' => [
        'gateway' => env('DIRECTORIO_GATEWAY', 'fake'),
        'allowed_email_domain' => env('DIRECTORIO_ALLOWED_EMAIL_DOMAIN', 'edu.bo'),
        'base_url' => env('DIRECTORIO_BASE_URL'),
        'api_key' => env('DIRECTORIO_API_KEY'),
    ],
    'aulas' => [
        'gateway' => env('AULA_GATEWAY', 'fake'),
        'base_url' => env('AULA_BASE_URL'),
        'api_key' => env('AULA_API_KEY'),
    ],
    'notifications' => [
        'gateway' => env('NOTIFICATION_GATEWAY', 'log'),
    ],
];
