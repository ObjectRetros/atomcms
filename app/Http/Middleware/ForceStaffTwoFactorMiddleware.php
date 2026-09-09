<?php

namespace App\Http\Middleware;

use App\Http\Responses\AccessResponse;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceStaffTwoFactorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('logout', 'housekeeping/logout') || $request->routeIs('logout', 'filament.*.auth.logout', 'api.v1.me.two-factor')) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user instanceof User || ! setting('force_staff_2fa')) {
            return $next($request);
        }

        $allowedRoutes = [
            'settings.two-factor',
            'user.two-factor.enable',
            'two-factor.verify',
            'user.two-factor.disable',
            'filament.housekeeping.pages.two-factor-authentication',
        ];

        if (
            $user->rank >= (int) setting('min_staff_rank')
            && ! $user->hasEnabledTwoFactorAuthentication()
            && ! $request->routeIs(...$allowedRoutes)
        ) {
            if ($request->routeIs('filament.housekeeping.*')) {
                return to_route('filament.housekeeping.pages.two-factor-authentication');
            }

            return AccessResponse::make($request, 'two_factor_required', 'Two-factor authentication must be configured.', 403, 'settings.two-factor');
        }

        return $next($request);
    }
}
