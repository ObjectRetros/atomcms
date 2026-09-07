<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\SessionGuard;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession as Middleware;

class AuthenticateSession extends Middleware
{
    /**
     * @param  Request  $request
     */
    public function handle($request, Closure $next): mixed
    {
        $guard = $this->auth->guard();

        // Existing sessions predate password tracking. Only a fresh login can
        // establish their fingerprint safely after a password may have changed.
        if ($request->hasSession()
            && $request->user() !== null
            && $guard instanceof SessionGuard
            && $request->session()->has($guard->getName())
            && ! $request->session()->has('password_hash_' . $this->auth->getDefaultDriver())) {
            $this->logout($request);
        }

        return parent::handle($request, $next);
    }
}
