<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Illuminate\Http\Request;

class AuthenticateHousekeepingSession extends AuthenticateSession
{
    protected function redirectTo(Request $request): ?string
    {
        return Filament::getLoginUrl();
    }
}
