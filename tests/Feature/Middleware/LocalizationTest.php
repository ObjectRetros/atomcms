<?php

use Database\Seeders\WebsiteLanguageSeeder;

beforeEach(function () {
    installHotel();

    $this->seed(WebsiteLanguageSeeder::class);
});

test('a cloudflare country code is mapped to a locale', function () {
    $this->get('/', ['CF-IPCountry' => 'DK']);

    expect(app()->getLocale())->toBe('da')
        ->and(session('locale'))->toBe('da');
});

test('an unsupported country falls back to the default locale', function () {
    $this->get('/', ['CF-IPCountry' => 'JP']);

    expect(app()->getLocale())->toBe(config('habbo.site.default_language'));
});

test('the browser language is used when no country header is present', function () {
    $this->get('/', ['Accept-Language' => 'sv-SE,sv;q=0.9']);

    expect(app()->getLocale())->toBe('se');
});
