<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ApiLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $fallback = (string) config('app.locale', 'en');
        $locales = array_values(array_unique([$fallback, ...array_map('basename', glob(lang_path('*'), GLOB_ONLYDIR) ?: [])]));
        app()->setLocale($request->getPreferredLanguage($locales) ?: $fallback);

        return $next($request);
    }
}
