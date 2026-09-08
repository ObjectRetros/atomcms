<?php

namespace App\Http\Middleware;

use App\Emulator\Data\Feature;
use App\Emulator\Emulator;
use App\Http\Responses\AccessResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Early-returns routes for features the configured emulator driver does not
 * support. Usage: ->middleware('emulator.feature:rare-values').
 */
class EnsureEmulatorFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $case = Feature::tryFrom($feature);

        abort_if($case === null, 500, "Unknown emulator feature [{$feature}]");

        if (! Emulator::supports($case)) {
            return AccessResponse::make($request, 'feature_unavailable', 'This feature is not available on this hotel.', 404, 'welcome', true);
        }

        return $next($request);
    }
}
