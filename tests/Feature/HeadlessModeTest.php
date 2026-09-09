<?php

use App\Filament\Widgets\ArticlesAggregateChart;
use App\Models\Miscellaneous\WebsiteInstallation;
use App\Models\User;
use App\Services\InstallationService;
use App\Services\OperatingMode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

it('returns a stable JSON error for missing versioned routes without accept headers', function () {
    installHotel();
    $this->get('/api/v1/does-not-exist')->assertNotFound()->assertJsonPath('code', 'not_found');
});

it('reports incomplete headless installation without starting the browser wizard', function () {
    WebsiteInstallation::query()->delete();
    Cache::flush();
    app()->forgetInstance(InstallationService::class);
    config(['atom.mode' => 'headless']);
    $this->get('/api/v1/me')->assertStatus(503)->assertJsonPath('code', 'installation_incomplete');
    expect(WebsiteInstallation::count())->toBe(0);
});

it('keeps wildcard CORS for legacy endpoints and credentials on allowed auth origins', function () {
    installHotel();
    config(['atom.cors_origins' => ['https://frontend.example.com']]);
    $this->withHeaders(['Origin' => 'https://unlisted.example.com'])
        ->get('/api/online-count')->assertHeader('Access-Control-Allow-Origin', '*')
        ->assertHeaderMissing('Access-Control-Allow-Credentials');
    $this->withHeaders([
        'Origin' => 'https://frontend.example.com',
        'Access-Control-Request-Method' => 'POST',
        'Access-Control-Request-Headers' => 'content-type,x-xsrf-token',
    ])->options('/login')->assertSuccessful()
        ->assertHeader('Access-Control-Allow-Origin', 'https://frontend.example.com')
        ->assertHeader('Access-Control-Allow-Credentials', 'true');
    $this->withHeaders(['Origin' => 'https://unlisted.example.com'])
        ->options('/login')->assertHeaderMissing('Access-Control-Allow-Origin');
});

it('enables two factor through the documented JSON management routes', function () {
    installHotel();
    $user = User::factory()->create(['password' => Hash::make('Example-password-42')]);
    $this->actingAs($user)->withHeader('Origin', 'http://localhost');
    $this->postJson('/user/settings/two-factor-authentication', ['current_password' => 'Example-password-42'])
        ->assertSuccessful()->assertJsonPath('data.enabled', false);
    $this->getJson('/api/v1/me/two-factor')->assertSuccessful()
        ->assertJsonStructure(['data' => ['enabled', 'qr_code', 'recovery_codes']]);
    $this->deleteJson('/user/settings/two-factor-authentication', ['current_password' => 'wrong'])->assertUnprocessable();
    $this->deleteJson('/user/settings/two-factor-authentication', ['current_password' => 'Example-password-42'])->assertNoContent();
    expect($user->fresh()->two_factor_secret)->toBeNull();
});

it('revokes stateful API access after the stored password changes', function () {
    installHotel();
    $user = User::factory()->create(['password' => Hash::make('Example-password-42')]);
    $this->withHeader('Origin', 'http://localhost');
    $this->postJson('/login', ['username' => $user->username, 'password' => 'Example-password-42'])->assertSuccessful();
    $this->getJson('/api/v1/me')->assertSuccessful();
    $user->forceFill(['password' => Hash::make('Replacement-password-42')])->save();
    auth()->forgetGuards();
    $this->getJson('/api/v1/me')->assertUnauthorized();
});

it('validates deployment origins before changing a mode', function () {
    config(['app.url' => 'https://api.example.com']);
    $mode = app(OperatingMode::class);
    expect(fn () => $mode->environment('headless', 'https://unrelated.test'))
        ->toThrow(InvalidArgumentException::class);
    expect(fn () => $mode->environment('headless', 'https://web.example.com/path', '.example.com'))
        ->toThrow(InvalidArgumentException::class);
    expect(fn () => $mode->environment('headless', 'http://web.example.com', '.example.com'))
        ->toThrow(InvalidArgumentException::class);
    expect($mode->environment('headless', 'https://web.example.com', '.example.com'))
        ->toMatchArray(['ATOM_MODE' => 'headless', 'SESSION_DOMAIN' => 'example.com', 'SESSION_SECURE_COOKIE' => 'true']);
    expect($mode->environment('full', null))->toBe(['ATOM_MODE' => 'full']);
});

it('returns a stable integration error when PayPal is not configured', function () {
    installHotel();
    config(['habbo.paypal.sandbox.client_id' => '', 'habbo.paypal.sandbox.client_secret' => '']);
    $this->actingAs(User::factory()->create())
        ->postJson('/api/v1/shop/paypal/orders', ['amount' => 10], ['Idempotency-Key' => 'unconfigured-order'])
        ->assertStatus(503)->assertJsonPath('code', 'integration_unavailable');
});

it('finishes CLI setup without promoting an existing ordinary user implicitly', function () {
    $user = User::factory()->create(['rank' => 1]);
    WebsiteInstallation::query()->delete();
    Cache::flush();
    $this->artisan('atom:setup', ['--complete' => true, '--no-interaction' => true])->assertSuccessful();
    expect($user->fresh()->rank)->toBe(1);
    expect(WebsiteInstallation::where('completed', true)->exists())->toBeTrue();
});

it('keeps housekeeping chart values usable after a file cache round trip', function () {
    $path = storage_path('framework/cache/chart-test-' . bin2hex(random_bytes(8)));
    config(['cache.default' => 'file', 'cache.stores.file.path' => $path]);
    Cache::purge('file');
    try {
        $widget = new ArticlesAggregateChart;
        $method = new ReflectionMethod($widget, 'getData');
        $first = $method->invoke($widget);
        expect($method->invoke($widget))->toBe($first);
        expect($first['labels'])->toBeArray()->not->toBeEmpty();
    } finally {
        File::deleteDirectory($path);
        Cache::purge('file');
    }
});
