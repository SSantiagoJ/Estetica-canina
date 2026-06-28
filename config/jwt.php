<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Pet Grooming
    |--------------------------------------------------------------------------
    |
    | La API REST usa estos valores para emitir tokens Bearer. Si JWT_SECRET
    | no existe, se reutiliza APP_KEY para mantener compatibilidad local.
    |
    */

    'secret' => env('JWT_SECRET', env('APP_KEY')),

    'ttl' => (int) env('JWT_TTL', 60),

    'leeway' => (int) env('JWT_LEEWAY', 30),

    'issuer' => env('APP_URL', 'http://127.0.0.1:8000'),

    'audience' => env('APP_NAME', 'Pet Grooming'),
];
