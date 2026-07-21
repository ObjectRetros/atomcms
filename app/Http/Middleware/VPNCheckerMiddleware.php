<?php

namespace App\Http\Middleware;

use App\Models\Miscellaneous\WebsiteIpBlacklist;
use App\Models\Miscellaneous\WebsiteIpWhitelist;
use App\Services\IpLookupService;
use Closure;
use Illuminate\Database\Eloquent\Model;
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

        $apiResponse = $this->ipLookup->ipLookup($ip, $apiKey);
        $asn = $apiResponse['asn']['asn'] ?? '';

        if ($this->asnListed($asn, WebsiteIpWhitelist::class, 'whitelist_asn')) {
            return $next($request);
        }

        if ($this->asnListed($asn, WebsiteIpBlacklist::class, 'blacklist_asn')) {
            return $this->restrict();
        }

        if ($this->isThreat($apiResponse)) {
            WebsiteIpBlacklist::firstOrCreate(['ip_address' => $ip], ['asn' => null]);

            return $this->restrict();
        }

        return $next($request);
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
