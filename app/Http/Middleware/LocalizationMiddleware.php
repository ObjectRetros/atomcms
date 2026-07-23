<?php

namespace App\Http\Middleware;

use App\Models\Miscellaneous\WebsiteLanguage;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    /**
     * Cloudflare's CF-IPCountry header carries an ISO country code, not a
     * language code. Map countries to the locales that exist in lang/; any
     * country not listed falls through to the database check and the
     * configured default. Countries whose lowercased code happens to equal a
     * locale (de, fr, it, es, nl, fi, no, se, tr, br) resolve via identity.
     */
    private const COUNTRY_LOCALES = [
        'gb' => 'en',
        'us' => 'en',
        'ie' => 'en',
        'au' => 'en',
        'nz' => 'en',
        'ca' => 'en',
        'dk' => 'da',
        'at' => 'de',
        'ch' => 'de',
        'be' => 'nl',
        'pt' => 'br',
    ];

    /**
     * Accept-Language uses ISO language codes, which differ from a few of the
     * locale directory names in lang/.
     */
    private const LANGUAGE_LOCALES = [
        'sv' => 'se',
        'pt' => 'br',
        'nb' => 'no',
        'nn' => 'no',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (Schema::hasTable('website_settings') && Session::has('locale')) {
            App::setLocale(Session::get('locale'));

            return $next($request);
        }

        $locale = $this->detectLocale($request);

        // Fall back to the default when the language is not enabled in the database
        if (Schema::hasTable('website_languages') && WebsiteLanguage::where('country_code', '=', $locale)->doesntExist()) {
            $locale = config('habbo.site.default_language');
        }

        App::setLocale($locale);
        Session::put('locale', $locale);

        return $next($request);
    }

    private function detectLocale(Request $request): string
    {
        $country = $request->header('CF-IPCountry');

        if (is_string($country) && $country !== '') {
            $country = strtolower($country);

            return self::COUNTRY_LOCALES[$country] ?? $country;
        }

        $preferred = $request->getPreferredLanguage();

        if (is_string($preferred) && $preferred !== '') {
            $language = strtolower(substr($preferred, 0, 2));

            return self::LANGUAGE_LOCALES[$language] ?? $language;
        }

        return config('habbo.site.default_language');
    }
}
