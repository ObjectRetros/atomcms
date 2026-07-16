<?php

use App\Jobs\SendRegisteredUserWebhook;
use App\Rules\GoogleRecaptchaRule;
use App\Services\FindRetrosService;
use App\Services\IpLookupService;
use App\Support\DiscordWebhookUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    installHotel();
    Cache::flush();
});

test('discord webhook URLs are restricted to official HTTPS endpoints', function () {
    expect(DiscordWebhookUrl::isValid('https://discord.com/api/webhooks/123/token_value'))
        ->toBeTrue()
        ->and(DiscordWebhookUrl::isValid('https://canary.discord.com/api/v10/webhooks/123/token-value'))
        ->toBeTrue()
        ->and(DiscordWebhookUrl::isValid('http://discord.com/api/webhooks/123/token'))
        ->toBeFalse()
        ->and(DiscordWebhookUrl::isValid('https://discord.com.evil.test/api/webhooks/123/token'))
        ->toBeFalse()
        ->and(DiscordWebhookUrl::isValid('https://discord.com@127.0.0.1/api/webhooks/123/token'))
        ->toBeFalse();
});

test('findretros uses encoded HTTPS requests and fails open on service errors', function () {
    config()->set('habbo.findretros.enabled', true);
    config()->set('habbo.findretros.name', 'Atom Hotel');
    config()->set('habbo.findretros.api', 'https://findretros.com');
    $request = Request::create('/', 'GET', server: ['REMOTE_ADDR' => '203.0.113.10']);

    Http::fake([
        'findretros.com/*' => Http::response('service unavailable', 503),
    ]);

    expect(app(FindRetrosService::class)->checkHasVoted($request))->toBeTrue();

    expect(app(FindRetrosService::class)->getRedirectUri())
        ->toBe('https://findretros.com/servers/Atom%20Hotel/vote?minimal=1&return=1');

    Http::assertSent(fn ($outbound) => $outbound->url() === 'https://findretros.com/validate.php?user=Atom%20Hotel&ip=203.0.113.10');
});

test('recaptcha rejects unsuccessful verification responses', function () {
    setSetting('google_recaptcha_enabled', '1');
    Http::fake([
        'google.com/recaptcha/*' => Http::response(['success' => false]),
    ]);
    $failure = null;

    (new GoogleRecaptchaRule)('g-recaptcha-response', 'invalid-token', function (string $message) use (&$failure): void {
        $failure = $message;
    });

    expect($failure)->not->toBeNull();
    Http::assertSent(fn ($request) => $request->method() === 'POST'
        && $request->url() === 'https://www.google.com/recaptcha/api/siteverify'
        && $request['response'] === 'invalid-token');
});

test('IP reputation lookups validate addresses and handle upstream failure', function () {
    Http::fake([
        'api.ipdata.co/*' => Http::response(['message' => 'rate limited'], 429),
    ]);
    $lookups = app(IpLookupService::class);

    expect($lookups->ipLookup('not-an-ip', 'secret'))
        ->toBe(['message' => 'Invalid IP address.', 'status' => 422])
        ->and($lookups->ipLookup('203.0.113.10', 'secret'))
        ->toBe(['message' => 'rate limited', 'status' => 429]);

    Http::assertSentCount(3);
});

test('discord failures log only status metadata and throw a retryable error', function () {
    setSetting('discord_webhook_url', 'https://discord.com/api/webhooks/123/token_value');
    Http::fake([
        'discord.com/*' => Http::response('sensitive response body', 500),
    ]);
    Log::spy();

    $job = new SendRegisteredUserWebhook('Tester', '203.0.113.10', 'tester@example.com');

    expect(fn () => $job->handle())
        ->toThrow(RuntimeException::class, 'Discord registration webhook delivery failed.');

    Log::shouldHaveReceived('warning')
        ->once()
        ->with('Discord registration webhook returned an error.', ['status' => 500]);
});
