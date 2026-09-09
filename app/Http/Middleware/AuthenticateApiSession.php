<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\SessionGuard;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\AuthenticateSession;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiSession extends AuthenticateSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $default = $this->auth->getDefaultDriver();
        foreach ((array) config('sanctum.guard', ['web']) as $name) {
            $guard = $this->auth->guard($name);
            if ($request->hasSession() && $guard instanceof SessionGuard && $guard->user() !== null
                && $request->session()->has($guard->getName())
                && ! $request->session()->has('password_hash_' . $name)) {
                $guard->logoutCurrentDevice();
                $request->session()->flush();
                throw new AuthenticationException('Unauthenticated.', [$name, 'sanctum']);
            }
        }

        try {
            return parent::handle($request, $next);
        } finally {
            $this->auth->shouldUse($default);
        }
    }
}
