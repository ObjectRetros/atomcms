<?php

namespace App\Http\Middleware;

use App\Support\FrontendUrls;
use Closure;
use Fruitcake\Cors\CorsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class HandleCors
{
    public function handle(Request $request, Closure $next): Response
    {
        $credentialed = $request->is(
            'api/v1', 'api/v1/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register',
            'two-factor-challenge', 'forgot-password', 'reset-password/*',
            'user/settings/two-factor-authentication', 'user/settings/two-factor-authentication/*',
            'user/confirm-password', 'user/confirmed-password-status', 'user/two-factor-*',
            'user/confirmed-two-factor-authentication',
        );

        if (! $credentialed && ! $request->is('api/*')) {
            return $next($request);
        }

        $options = config('cors');
        if ($credentialed) {
            $options['allowed_origins'] = array_values(array_filter((array) config('atom.cors_origins', []), static function ($origin): bool {
                if (! is_string($origin) || str_contains($origin, '*')) {
                    return false;
                }
                try {
                    return FrontendUrls::origin($origin) === $origin;
                } catch (\InvalidArgumentException) {
                    return false;
                }
            }));
            $options['allowed_origins_patterns'] = [];
            $options['supports_credentials'] = true;
            $options['exposed_headers'] = ['Retry-After'];
        }
        $cors = new CorsService($options);

        if ($credentialed && $cors->isCorsRequest($request) && ! $cors->isOriginAllowed($request)) {
            return $cors->isPreflightRequest($request) ? response('', 204) : $next($request);
        }

        if ($cors->isPreflightRequest($request)) {
            $response = $cors->handlePreflightRequest($request);
            $cors->varyHeader($response, 'Access-Control-Request-Method');

            return $response;
        }

        return $cors->addActualRequestHeaders($next($request), $request);
    }
}
