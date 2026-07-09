<?php

namespace App\Http\Middleware;

use App\Emulator\Data\Feature;
use App\Emulator\Emulator;
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
            return to_route('welcome')->withErrors([
                'message' => __('This feature is not available on this hotel.'),
            ]);
        }

        return $next($request);
    }
}
