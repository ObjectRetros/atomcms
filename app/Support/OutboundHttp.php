<?php

namespace App\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

final class OutboundHttp
{
    public static function request(): PendingRequest
    {
        return Http::connectTimeout(3)
            ->timeout(10)
            ->retry([200, 500], throw: false)
            ->withOptions(['verify' => true]);
    }
}
