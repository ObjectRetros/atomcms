<?php

namespace App\Http\Middleware;

use App\Emulator\Contracts\BanRepository;
use App\Http\Responses\AccessResponse;
use App\Models\User;
use App\Support\FrontendUrls;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class BannedMiddleware
{
    /**
     * How long a ban verdict may be served from cache. This middleware runs
     * on nearly every request, so the two emulator ban queries are cached
     * briefly; a fresh ban or unban can therefore take up to this many
     * seconds to be enforced on the website.
     */
    private const VERDICT_TTL_SECONDS = 45;

    public function __construct(private readonly BanRepository $bans) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('logout', 'housekeeping/logout') || $request->routeIs('logout', 'filament.*.auth.logout')) {
            return $next($request);
        }

        $ip = (string) $request->ip();
        $ipBan = Cache::remember(
            'ban_verdict:ip:' . $ip,
            self::VERDICT_TTL_SECONDS,
            fn (): bool => $this->bans->activeIpBan($ip) !== null,
        );

        $onBannedPage = $request->is('banned');

        $user = $request->user();

        if (! $user instanceof User) {
            if ($ipBan && ! $onBannedPage) {
                return AccessResponse::make($request, 'account_banned', 'This account or address is banned.', 403, 'banned.show');
            }

            return $onBannedPage && ! $ipBan ? redirect(app(FrontendUrls::class)->route('login')) : $next($request);
        }

        $accountBan = Cache::remember(
            'ban_verdict:user:' . $user->id,
            self::VERDICT_TTL_SECONDS,
            fn (): bool => $this->bans->activeAccountBan($user) !== null,
        );

        if (($ipBan || $accountBan) && ! $onBannedPage) {
            return AccessResponse::make($request, 'account_banned', 'This account or address is banned.', 403, 'banned.show');
        }

        if (! $ipBan && ! $accountBan && $onBannedPage) {
            return redirect(app(FrontendUrls::class)->route('me.show'));
        }

        return $next($request);
    }
}
