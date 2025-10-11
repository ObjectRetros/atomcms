<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $isPostRequest = $request->method() === 'POST';
        $isMaintenanceRequest = $request->is('maintenance');
        $maintenanceEnabled = setting('maintenance_enabled');

        $fortify2faRoutes = [
            'two-factor.login',
            'two-factor.confirm',
        ];

        if ($maintenanceEnabled && $isPostRequest && ! Auth::check()) {
            return $next($request);
        }

        $isFortify2faRoute = in_array($request->route()?->getName(), $fortify2faRoutes, true);
        if ($maintenanceEnabled && $isFortify2faRoute) {
            return $next($request);
        }

        if (Auth::check() && Auth::user()->rank >= setting('min_maintenance_login_rank')) {
            if ($isMaintenanceRequest) {
                return to_route('me.show');
            }

            return $next($request);
        }

        if (Auth::check() && Auth::user()->rank >= setting('min_maintenance_login_rank') && $isMaintenanceRequest) {
            return to_route('me.show');
        }

        if ($maintenanceEnabled && ! $isMaintenanceRequest && ! $isPostRequest) {
            return to_route('maintenance.show');
        }

        if (! $maintenanceEnabled && $isMaintenanceRequest && ! $isPostRequest) {
            return to_route('welcome');
        }

        if ($maintenanceEnabled && ! $isMaintenanceRequest && Auth::check() && Auth::user()->rank < setting('min_maintenance_login_rank')) {
            return to_route('maintenance.show');
        }

        return $next($request);
    }
}
