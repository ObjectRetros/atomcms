<?php

namespace App\Http\Middleware;

use App\Models\User\Ban;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BannedMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $authenticated = Auth::check();
        $ipBan = Ban::where('ip', '=', $request->ip())
            ->where('ban_expire', '>', time())
            ->whereIn('type', ['ip', 'machine'])
            ->orderByDesc('id')
            ->exists();

        if ($request->is('logout')) {
            return $next($request);
        }

        if (! $authenticated && ! $ipBan && $request->is('banned')) {
            return to_route('login');
        }

        if ($ipBan && ! $request->is('banned')) {
            return to_route('banned.show');
        }

        if ($authenticated) {
            $accountBan = $request->user()?->ban;

            if ($accountBan && ! $request->is('banned')) {
                return to_route('banned.show');
            }

            if (! $ipBan && ! $accountBan && $request->is('banned')) {
                return to_route('me.show');
            }
        }

        return $next($request);
    }
}
