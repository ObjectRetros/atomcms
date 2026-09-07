<?php

use App\Models\User;

beforeEach(function () {
    installHotel();

    config([
        'habbo.site.recaptcha_site_key' => 'google-test-site-key',
        'services.turnstile.key' => 'turnstile-test-site-key',
    ]);
});

test('captcha forms load the enabled google widget script once', function (string $theme, string $route) {
    setSetting('theme', $theme);
    setSetting('google_recaptcha_enabled', '1');

    if ($route === 'settings.password.show') {
        $this->actingAs(User::factory()->create());
    }

    $response = $this->get(route($route))
        ->assertOk()
        ->assertSee('data-sitekey="google-test-site-key"', escape: false);

    expect(substr_count($response->getContent(), 'https://www.google.com/recaptcha/api.js'))->toBe(1);
})->with(['atom', 'dusk'])->with(['register', 'settings.password.show']);

test('disabling google leaves turnstile available on registration forms', function (string $theme) {
    setSetting('theme', $theme);
    setSetting('google_recaptcha_enabled', '0');
    setSetting('cloudflare_turnstile_enabled', '1');

    $this->get(route('register'))
        ->assertOk()
        ->assertDontSee('https://www.google.com/recaptcha/api.js', escape: false)
        ->assertDontSee('data-sitekey="google-test-site-key"', escape: false)
        ->assertSee('https://challenges.cloudflare.com/turnstile/v0/api.js', escape: false)
        ->assertSee('turnstile-test-site-key', escape: false);
})->with(['atom', 'dusk']);
