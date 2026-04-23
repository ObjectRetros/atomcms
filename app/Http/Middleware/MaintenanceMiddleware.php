<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $isPostRequest = $request->isMethod('POST');
        $isMaintenanceRequest = $request->is('maintenance');
        $maintenanceEnabled = setting('maintenance_enabled');
        $user = $request->user();

        if (! $maintenanceEnabled) {
            if ($isMaintenanceRequest && ! $isPostRequest) {
                return to_route('welcome');
            }

            return $next($request);
        }

        if ($this->shouldBypassMaintenance($request, $user)) {
            if ($isMaintenanceRequest) {
                return to_route('me.show');
            }

            return $next($request);
        }

        if ($isPostRequest && $user === null) {
            return $next($request);
        }

        if (! $isMaintenanceRequest) {
            return to_route('maintenance.show');
        }

        return $next($request);
    }

    private function shouldBypassMaintenance(Request $request, ?User $user): bool
    {
        $fortify2faRoutes = [
            'two-factor.login',
            'two-factor.confirm',
        ];

        if (in_array($request->route()?->getName(), $fortify2faRoutes, true)) {
            return true;
        }

        return $user !== null && $user->rank >= setting('min_maintenance_login_rank');
    }
}
