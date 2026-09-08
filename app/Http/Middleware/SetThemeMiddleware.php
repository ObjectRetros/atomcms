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
        $theme = setting('theme');

        if (empty($theme) || $theme === '1') {
            Theme::set('atom', config('theme.parent'));
        } else {
            Theme::set($theme, config('theme.parent'));
        }

        return $next($request);
    }
}
