<?php

return [
    'aulas' => [
        'gateway' => env('AULA_GATEWAY', 'fake'),
        'base_url' => env('AULA_BASE_URL'),
        'api_key' => env('AULA_API_KEY'),
    ],
    'notifications' => [
        'gateway' => env('NOTIFICATION_GATEWAY', 'log'),
    ],
];
