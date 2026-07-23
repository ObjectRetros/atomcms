<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    public function __construct()
    {
        $proxies = config('habbo.trusted_proxies');

        $this->proxies = match (true) {
            $proxies === '*' => '*',
            is_string($proxies) && $proxies !== '' => array_map('trim', explode(',', $proxies)),
            default => null,
        };
    }

    /**
     * The headers that should be used to detect proxies.
     *
     * X-Forwarded-Host is deliberately not trusted: with the permissive "*"
     * proxy default, honouring it would let anyone who can reach the origin
     * directly poison generated absolute URLs (e.g. password-reset links)
     * with an attacker-controlled host.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
