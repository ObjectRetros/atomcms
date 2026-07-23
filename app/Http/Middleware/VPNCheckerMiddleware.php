<?php

namespace App\Http\Middleware;

use App\Models\Miscellaneous\WebsiteIpBlacklist;
use App\Models\Miscellaneous\WebsiteIpWhitelist;
use App\Services\IpLookupService;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VPNCheckerMiddleware
{
    public function __construct(private readonly IpLookupService $ipLookup) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        if (WebsiteIpBlacklist::where('ip_address', $request->ip())->exists()) {
            return $this->restrict();
        }

        return $this->checkReputation($request, $next);
    }

    private function shouldSkip(Request $request): bool
    {
        return setting('vpn_block_enabled') === '0'
            || setting('ipdata_api_key') === 'ADD-API-KEY-HERE'
            || hasPermission('bypass_vpn')
            || WebsiteIpWhitelist::where('ip_address', $request->ip())->exists();
    }

    private function checkReputation(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $apiKey = setting('ipdata_api_key');

        if (! is_string($ip) || ! is_string($apiKey) || $apiKey === '') {
            return $next($request);
        }

        $reputation = $this->reputation($ip, $apiKey);
        $asn = $reputation['asn'];

        if ($this->asnListed($asn, WebsiteIpWhitelist::class, 'whitelist_asn')) {
            return $next($request);
        }

        if ($this->asnListed($asn, WebsiteIpBlacklist::class, 'blacklist_asn')) {
            return $this->restrict();
        }

        if ($reputation['threat']) {
            WebsiteIpBlacklist::firstOrCreate(['ip_address' => $ip], ['asn' => null]);

            return $this->restrict();
        }

        return $next($request);
    }

    /**
     * Look up the IP's reputation, caching the minimal derived payload so the
     * client routes do not make a blocking HTTPS call on every request. The
     * ASN white/blacklists are still evaluated per request against the
     * database, so admin list changes apply immediately.
     *
     * @return array{asn: string, threat: bool}
     */
    private function reputation(string $ip, string $apiKey): array
    {
        $cacheKey = "ip_reputation:{$ip}";

        $cached = Cache::get($cacheKey);

        if (is_array($cached) && isset($cached['asn'], $cached['threat'])) {
            return ['asn' => (string) $cached['asn'], 'threat' => (bool) $cached['threat']];
        }

        $apiResponse = $this->ipLookup->ipLookup($ip, $apiKey);

        $reputation = [
            'asn' => (string) ($apiResponse['asn']['asn'] ?? ''),
            'threat' => $this->isThreat($apiResponse),
        ];

        // Failed lookups fail open, but only briefly: a short TTL stops an
        // outage from hammering the API without whitelisting the IP for 12h.
        $ttl = $this->lookupFailed($apiResponse) ? now()->addMinute() : now()->addHours(12);

        Cache::put($cacheKey, $reputation, $ttl);

        return $reputation;
    }

    /**
     * IpLookupService flattens transport and API errors into a payload with a
     * top-level "status" key, which a successful ipdata body never carries.
     *
     * @param  array<string, mixed>  $apiResponse
     */
    private function lookupFailed(array $apiResponse): bool
    {
        return isset($apiResponse['status']);
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function asnListed(string $asn, string $model, string $flag): bool
    {
        return $model::where('asn', $asn)->where($flag, '1')->exists();
    }

    /**
     * @param  array<string, mixed>  $apiResponse
     */
    private function isThreat(array $apiResponse): bool
    {
        if (! isset($apiResponse['threat']) || ! is_array($apiResponse['threat'])) {
            return false;
        }

        $filtered = array_diff_key(
            $apiResponse['threat'],
            array_flip(['blocklists', 'is_icloud_relay', 'is_datacenter', 'is_tor', 'is_proxy']),
        );

        return in_array(true, array_values($filtered), true);
    }

    private function restrict(): RedirectResponse
    {
        return to_route('me.show')->withErrors([
            'message' => __('Your IP has been restricted - If you think this is a mistake, you can contact us on our Discord.'),
        ]);
    }
}
