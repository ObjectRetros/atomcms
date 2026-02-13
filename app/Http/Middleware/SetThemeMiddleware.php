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
            Theme::set('atom');
        } else {
            Theme::set($theme);
        }

        return $next($request);
    }
}
