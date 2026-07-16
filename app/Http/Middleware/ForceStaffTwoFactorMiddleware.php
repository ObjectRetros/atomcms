<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceStaffTwoFactorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! setting('force_staff_2fa')) {
            return $next($request);
        }

        $allowedRoutes = [
            'settings.two-factor',
            'user.two-factor.enable',
            'two-factor.verify',
            'user.two-factor.disable',
        ];

        if (
            $user->rank >= (int) setting('min_staff_rank')
            && ! $user->hasEnabledTwoFactorAuthentication()
            && ! $request->routeIs(...$allowedRoutes)
        ) {
            return to_route('settings.two-factor');
        }

        return $next($request);
    }
}
