<?php

namespace App\Http\Middleware;

use App\Http\Responses\AccessResponse;
use App\Models\User;
use App\Support\FrontendUrls;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('logout', 'housekeeping/logout') || $request->routeIs('logout', 'filament.*.auth.logout')) {
            return $next($request);
        }

        $maintenanceEnabled = (bool) setting('maintenance_enabled');
        $isMaintenanceRequest = $request->is('maintenance');

        // Keep authentication entry points reachable so eligible staff can log in.
        if ($maintenanceEnabled && ($this->isLoginRoute($request) || $this->isTwoFactorRoute($request))) {
            return $next($request);
        }

        // Staff above the threshold bypass maintenance; keep them off the notice page.
        if ($this->canBypassMaintenance($request)) {
            return $isMaintenanceRequest ? redirect(app(FrontendUrls::class)->route('me.show')) : $next($request);
        }

        if ($maintenanceEnabled && ! $isMaintenanceRequest) {
            return AccessResponse::make($request, 'maintenance', 'The hotel is under maintenance.', 503, 'maintenance.show');
        }

        if (! $maintenanceEnabled && $isMaintenanceRequest) {
            return redirect(app(FrontendUrls::class)->route('welcome'));
        }

        return $next($request);
    }

    private function canBypassMaintenance(Request $request): bool
    {
        // Default to rank 5 when unset; a missing setting must not let every
        // logged-in user through ("rank >= null" is always true).
        $user = $request->user();

        return $user instanceof User
            && $user->rank >= (int) (setting('min_maintenance_login_rank') ?: 5);
    }

    private function isTwoFactorRoute(Request $request): bool
    {
        return $request->routeIs('two-factor.login', 'two-factor.login.store');
    }

    private function isLoginRoute(Request $request): bool
    {
        return $request->routeIs('login', 'login.store', 'filament.*.auth.login');
    }
}
