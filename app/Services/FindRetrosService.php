<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

/* Credits to Kani for this */

class FindRetrosService
{
    /**
     * The findretros verification uri.
     */
    public const FIND_RETROS_VERIFY_URI = '%s/validate.php?user=%s&ip=%s';

    /**
     * The findretros redirect uri.
     */
    public const FIND_RETROS_REDIRECT_URI = '%s/servers/%s/vote?minimal=1&return=1';

    public const FIND_RETROS_CACHE_KEY = 'voted.%s';

    /**
     * The guzzle client instance
     */
    protected Client $client;

    /**
     * Initialise Find Retros Service
     */
    public function __construct()
    {
        $this->client = new Client(['verify' => false]);
    }

    /**
     * Check the user has voted.
     */
    public function checkHasVoted(): bool
    {
        if (! config('habbo.findretros.enabled')) {
            return true;
        }

        $cacheKey = sprintf(self::FIND_RETROS_CACHE_KEY, request()->ip());
        if (request()->ip() === '127.0.0.1') {
            return true;
        }

        if (request()->has('novote')) {
            return true;
        }

        if (Cache::has($cacheKey)) {
            return true;
        }

        $uri = sprintf(self::FIND_RETROS_VERIFY_URI, config('habbo.findretros.api'), config('habbo.findretros.name'), request()->ip());
        $request = $this->client->get($uri);
        $response = $request->getBody()->getContents();

        if (in_array($response, ['1', '2'])) {
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
        return sprintf(self::FIND_RETROS_REDIRECT_URI, config('habbo.findretros.api'), config('habbo.findretros.name'));
    }
}
