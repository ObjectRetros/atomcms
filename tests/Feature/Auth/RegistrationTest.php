<?php

use App\Jobs\SendRegisteredUserWebhook;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
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
