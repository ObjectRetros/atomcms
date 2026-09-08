<?php

namespace App\Http\Middleware;

use App\Support\FrontendUrls;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->is('api/v1', 'api/v1/*') || $request->expectsJson() ? null : app(FrontendUrls::class)->route('login');
    }
}
