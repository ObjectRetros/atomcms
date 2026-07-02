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
        $maintenanceEnabled = (bool) setting('maintenance_enabled');
        $isPostRequest = $request->method() === 'POST';
        $isMaintenanceRequest = $request->is('maintenance');

        // Let guests POST (to log in) and finish the 2FA challenge during maintenance.
        if ($maintenanceEnabled && (($isPostRequest && ! Auth::check()) || $this->isTwoFactorRoute($request))) {
            return $next($request);
        }

        // Staff above the threshold bypass maintenance; keep them off the notice page.
        if ($this->canBypassMaintenance()) {
            return $isMaintenanceRequest ? to_route('me.show') : $next($request);
        }

        if ($maintenanceEnabled && ! $isMaintenanceRequest && ! $isPostRequest) {
            return to_route('maintenance.show');
        }

        if (! $maintenanceEnabled && $isMaintenanceRequest && ! $isPostRequest) {
            return to_route('welcome');
        }

        if ($maintenanceEnabled && ! $isMaintenanceRequest && Auth::check()) {
            return to_route('maintenance.show');
        }

        return $next($request);
    }

    private function canBypassMaintenance(): bool
    {
        return Auth::check() && Auth::user()->rank >= setting('min_maintenance_login_rank');
    }

    private function isTwoFactorRoute(Request $request): bool
    {
        return in_array($request->route()?->getName(), ['two-factor.login', 'two-factor.confirm'], true);
    }
}
