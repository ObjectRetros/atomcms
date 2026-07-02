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
        if ($request->is('logout')) {
            return $next($request);
        }

        $ipBan = $this->hasActiveIpBan($request);
        $onBannedPage = $request->is('banned');

        if (! Auth::check()) {
            if ($ipBan && ! $onBannedPage) {
                return to_route('banned.show');
            }

            return $onBannedPage && ! $ipBan ? to_route('login') : $next($request);
        }

        $accountBan = $request->user()?->ban;

        if (($ipBan || $accountBan) && ! $onBannedPage) {
            return to_route('banned.show');
        }

        if (! $ipBan && ! $accountBan && $onBannedPage) {
            return to_route('me.show');
        }

        return $next($request);
    }

    private function hasActiveIpBan(Request $request): bool
    {
        return Ban::where('ip', $request->ip())
            ->where('ban_expire', '>', time())
            ->whereIn('type', ['ip', 'machine'])
            ->exists();
    }
}
