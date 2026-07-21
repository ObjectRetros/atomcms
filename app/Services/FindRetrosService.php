<?php

namespace App\Services;

use App\Support\OutboundHttp;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/* Credits to Kani for this */

class FindRetrosService
{
    public const FIND_RETROS_CACHE_KEY = 'voted.%s';

    /**
     * Check the user has voted.
     */
    public function checkHasVoted(Request $request): bool
    {
        if (! config('habbo.findretros.enabled')) {
            return true;
        }

        $ip = $request->ip();
        $cacheKey = sprintf(self::FIND_RETROS_CACHE_KEY, $ip);

        if ($ip === '127.0.0.1') {
            return true;
        }

        if ($request->has('novote')) {
            return true;
        }

        if (Cache::has($cacheKey)) {
            return true;
        }

        try {
            $response = OutboundHttp::request()
                ->accept('text/plain')
                ->get(rtrim((string) config('habbo.findretros.api'), '/') . '/validate.php', [
                    'user' => config('habbo.findretros.name'),
                    'ip' => $ip,
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('FindRetros verification was unavailable.', [
                'exception_class' => $exception::class,
            ]);

            return true;
        }

        if (! $response->successful()) {
            Log::warning('FindRetros verification returned an error.', [
                'status' => $response->status(),
            ]);

            return true;
        }

        if (in_array(trim($response->body()), ['1', '2'], true)) {
            Cache::put($cacheKey, true, now()->addMinutes(30));

            return true;
        }

        return false;
    }

    /**
     * Retrieve the find retros redirect url.
     */
    public function getRedirectUri(): string
    {
        return sprintf(
            '%s/servers/%s/vote?minimal=1&return=1',
            rtrim((string) config('habbo.findretros.api'), '/'),
            rawurlencode((string) config('habbo.findretros.name')),
        );
    }
}
