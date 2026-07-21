<?php

namespace App\Http\Middleware;

use App\Emulator\Contracts\BanRepository;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
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

        $user = $request->user();

        if (! $user instanceof User) {
            if ($ipBan && ! $onBannedPage) {
                return to_route('banned.show');
            }

            return $onBannedPage && ! $ipBan ? to_route('login') : $next($request);
        }

        $accountBan = $this->bans->activeAccountBan($user) !== null;

        if (($ipBan || $accountBan) && ! $onBannedPage) {
            return to_route('banned.show');
        }

        if (! $ipBan && ! $accountBan && $onBannedPage) {
            return to_route('me.show');
        }

        return $next($request);
    }
}
