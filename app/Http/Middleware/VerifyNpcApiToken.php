<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyNpcApiToken
{
    /**
     * Verify the NPC API token sent by the Arcturus emulator plugin.
     *
     * The emulator plugin must include the token in the X-NPC-Token header.
     * This ensures only the game emulator can interact with the NPC chat API.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configuredToken = config('npc.api_token');

        // If no token is configured, allow all requests (development mode)
        if (empty($configuredToken)) {
            return $next($request);
        }

        $requestToken = $request->header('X-NPC-Token');

        if (empty($requestToken) || !hash_equals($configuredToken, $requestToken)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid or missing NPC API token.',
            ], 401);
        }

        return $next($request);
    }
}
