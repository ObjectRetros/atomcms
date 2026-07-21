<?php

use App\Jobs\SendRegisteredUserWebhook;
use App\Models\Miscellaneous\WebsiteBetaCode;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;

function register(array $overrides = []): TestResponse
{
    return test()->post('/register', [
        'username' => 'Tester',
        'mail' => 'tester@example.com',
        'password' => 'Sup3rSecret!',
        'password_confirmation' => 'Sup3rSecret!',
        'terms' => 'on',
        ...$overrides,
    ]);
}

test('a visitor can register an account', function () {
    installHotel();

    register()->assertRedirect();

    $this->assertAuthenticated();

    $user = User::where('username', 'Tester')->first();

    expect($user)->not->toBeNull()
        ->and($user->mail)->toBe('tester@example.com')
        ->and($user->referral_code)->not->toBeEmpty();
});

test('registration rejects a weak password', function () {
    installHotel();

    register([
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('password');

    expect(User::where('username', 'Tester')->exists())->toBeFalse();
});

test('registration is blocked when disabled', function () {
    installHotel();
    setSetting('disable_registration', '1');

    register()->assertSessionHasErrors('registration');

    expect(User::where('username', 'Tester')->exists())->toBeFalse();
});

test('the per-IP account cap is enforced', function () {
    installHotel();
    setSetting('max_accounts_per_ip', '1');

    User::factory()->create(); // occupies 127.0.0.1

    register()->assertSessionHasErrors('registration');

    expect(User::where('username', 'Tester')->exists())->toBeFalse();
});

test('the discord webhook is dispatched after the response', function () {
    installHotel();
    setSetting('enable_discord_webhook', '1');

    Bus::fake();

    register()->assertRedirect();

    Bus::assertDispatchedAfterResponse(SendRegisteredUserWebhook::class);
});

test('a required beta code cannot be omitted or reused', function () {
    installHotel();
    setSetting('requires_beta_code', '1');

    register()->assertSessionHasErrors('beta_code');

    $betaCode = WebsiteBetaCode::create(['code' => 'PRIVATE-BETA']);

    register(['beta_code' => $betaCode->code])->assertRedirect();

    expect($betaCode->fresh()->user_id)
        ->toBe(User::where('username', 'Tester')->value('id'))
        ->and(DB::table('website_registration_locks')->count())
        ->toBe(0);

    auth()->logout();

    register([
        'username' => 'SecondTester',
        'mail' => 'second@example.com',
        'beta_code' => $betaCode->code,
    ])->assertSessionHasErrors('beta_code');

    expect(User::where('username', 'SecondTester')->exists())->toBeFalse();
});

test('registration and observer side effects roll back together', function () {
    installHotel();
    $this->withoutExceptionHandling();

    User::created(function (User $user): void {
        if ($user->username === 'RollbackTester') {
            throw new RuntimeException('Simulated initialization failure.');
        }
    });

    expect(fn () => register([
        'username' => 'RollbackTester',
        'mail' => 'rollback@example.com',
    ]))->toThrow(RuntimeException::class, 'Simulated initialization failure.');

    expect(User::where('username', 'RollbackTester')->exists())
        ->toBeFalse()
        ->and(DB::table('website_registration_locks')->count())
        ->toBe(0);
});
