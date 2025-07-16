<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Qirolab\Theme\Theme;
use Symfony\Component\HttpFoundation\Response;

class SetThemeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $theme = setting('theme', 'atom'); // Default to 'atom' if empty
        Theme::set($theme);

        return $next($request);
    }
}
