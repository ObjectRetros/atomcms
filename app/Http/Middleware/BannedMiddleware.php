<?php

namespace App\Http\Middleware;

use App\Emulator\Contracts\BanRepository;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BannedMiddleware
{
    public function __construct(private readonly BanRepository $bans) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('logout')) {
            return $next($request);
        }

        $ipBan = $this->bans->activeIpBan((string) $request->ip()) !== null;
        $onBannedPage = $request->is('banned');

        if (! Auth::check()) {
            if ($ipBan && ! $onBannedPage) {
                return to_route('banned.show');
            }

            return $onBannedPage && ! $ipBan ? to_route('login') : $next($request);
        }

        $accountBan = $this->bans->activeAccountBan($request->user()) !== null;

        if (($ipBan || $accountBan) && ! $onBannedPage) {
            return to_route('banned.show');
        }

        if (! $ipBan && ! $accountBan && $onBannedPage) {
            return to_route('me.show');
        }

        return $next($request);
    }
}
