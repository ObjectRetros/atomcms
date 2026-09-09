<?php

return [
    'mode' => env('ATOM_MODE', 'full'),
    'frontend_url' => env('ATOM_FRONTEND_URL', ''),
    'cors_origins' => array_values(array_filter(array_map('trim', explode(',', env('ATOM_CORS_ORIGINS', ''))))),
    'frontend_paths' => [
        'welcome' => '/',
        'login' => '/login',
        'me.show' => '/me',
        'banned.show' => '/banned',
        'maintenance.show' => '/maintenance',
        'settings.two-factor' => '/settings/two-factor',
        'forgot.password.get' => '/forgot-password',
        'reset.password.get' => '/reset-password/{token}',
        'shop.index' => '/shop',
        'shop.paypal.index' => '/shop',
    ],
];
