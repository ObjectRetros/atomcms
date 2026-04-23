<?php

namespace App\Http\Middleware;

use App\Models\Miscellaneous\WebsiteIpBlacklist;
use App\Models\Miscellaneous\WebsiteIpWhitelist;
use App\Services\IpLookupService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VPNCheckerMiddleware
{
    public function __construct(private readonly IpLookupService $ipLookupService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = setting('ipdata_api_key');

        // Skip check if vpn checker is disabled
        if (setting('vpn_block_enabled') === '0' || blank($apiKey) || $apiKey === 'ADD-API-KEY-HERE') {
            return $next($request);
        }

        // Skip check if the rank is allowed to bypass the checker
        if (hasPermission('bypass_vpn')) {
            return $next($request);
        }

        // Skip check if the IP is in the whitelist table
        if (WebsiteIpWhitelist::where('ip_address', $request->ip())->exists()) {
            return $next($request);
        }

        // Restrict user if IP is blacklisted
        if (WebsiteIpBlacklist::where('ip_address', $request->ip())->exists()) {
            return to_route('me.show')->withErrors([
                'message' => __('Your IP have been restricted - If you think this is a mistake, you can contact us on our Discord.'),
            ]);
        }

        $userIp = $request->ip();
        $apiResponse = $this->ipLookupService->ipLookup($userIp, (string) $apiKey);

        $asn = $apiResponse['asn']['asn'] ?? '';
        $asnWhitelisted = WebsiteIpWhitelist::where('asn', $asn)
            ->where('whitelist_asn', '=', '1')
            ->exists();

        if ($asnWhitelisted) {
            return $next($request);
        }

        // Fetch all blacklisted ASNs
        $asnBlacklisted = WebsiteIpBlacklist::where('asn', $asn)
            ->where('blacklist_asn', '=', '1')
            ->exists();

        // Restrict the user if their ASN is within the blacklist table
        if ($asnBlacklisted) {
            return to_route('me.show')->withErrors([
                'message' => __('Your IP have been restricted - If you think this is a mistake, you can contact us on our Discord.'),
            ]);
        }

        if (isset($apiResponse['threat']) && is_array($apiResponse['threat'])) {
            $filteredThreats = array_diff_key($apiResponse['threat'], array_flip(['blocklists', 'is_icloud_relay', 'is_datacenter', 'is_tor', 'is_proxy']));

            if (in_array(true, array_values($filteredThreats), true)) {
                WebsiteIpBlacklist::create([
                    'ip_address' => $userIp,
                    'asn' => null,
                ]);

                return to_route('me.show')->withErrors([
                    'message' => __('Your IP has been restricted - If you think this is a mistake, you can contact us on our Discord.'),
                ]);
            }
        }

        return $next($request);
    }
}
