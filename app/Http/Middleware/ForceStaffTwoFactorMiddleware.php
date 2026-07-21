<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceStaffTwoFactorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User || ! setting('force_staff_2fa')) {
            return $next($request);
        }

        $urls = [
            'user/settings/two-factor',
            'user/settings/2fa-verify',
        ];

        if (($user->rank >= setting('min_staff_rank') && ! $user->two_factor_confirmed) && ! in_array(request()->path(), $urls)) {
            return to_route('settings.two-factor');
        }

        return $next($request);
    }
}
