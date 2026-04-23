<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Configure this when the application runs behind Cloudflare, a load
    | balancer, or another reverse proxy. Do not blindly trust all forwarded
    | headers from the public internet.
    |
    */

    'proxies' => env('TRUSTED_PROXIES'),
];
