<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\Auth;

class StoreSessionPasswordHash
{
    public function handle(Login $event): void
    {
        $guard = Auth::guard($event->guard);

        if ($guard instanceof SessionGuard) {
            $guard->getSession()->put(
                'password_hash_' . $event->guard,
                $guard->hashPasswordForCookie($event->user->getAuthPassword()),
            );
        }
    }
}
