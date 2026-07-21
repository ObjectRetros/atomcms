<?php

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PragmaRX\Google2FA\Google2FA;

beforeEach(function () {
    installHotel();
    $this->user = User::factory()->create();
});

test('a user can enable two-factor authentication', function () {
    $this->actingAs($this->user)
        ->post(route('user.two-factor.enable'), ['current_password' => 'password'])
        ->assertRedirect(route('settings.two-factor'))
        ->assertSessionHas('success');

    $user = $this->user->fresh();

    expect($user->two_factor_secret)->not->toBeNull()
        ->and($user->two_factor_recovery_codes)->not->toBeNull();
});

test('a valid code confirms two-factor authentication', function () {
    $this->actingAs($this->user)->post(route('user.two-factor.enable'), ['current_password' => 'password']);

    $secret = decrypt($this->user->fresh()->two_factor_secret);
    $code = app(Google2FA::class)->getCurrentOtp($secret);

    $this->actingAs($this->user)
        ->post(route('two-factor.verify'), ['code' => $code])
        ->assertSessionHas('success');

    expect($this->user->fresh()->two_factor_confirmed_at)->not->toBeNull();
});

test('an invalid code is rejected', function () {
    $this->actingAs($this->user)->post(route('user.two-factor.enable'), ['current_password' => 'password']);

    $this->actingAs($this->user)
        ->post(route('two-factor.verify'), ['code' => '000000'])
        ->assertSessionHasErrorsIn('confirmTwoFactorAuthentication', 'code');

    expect($this->user->fresh()->two_factor_confirmed_at)->toBeNull();
});

test('an empty code is rejected without a server error', function () {
    $this->actingAs($this->user)->post(route('user.two-factor.enable'));

    $this->actingAs($this->user)
        ->post(route('two-factor.verify'), ['code' => ''])
        ->assertSessionHasErrors();

    expect((bool) $this->user->fresh()->two_factor_confirmed)->toBeFalse();
});

test('two-factor authentication can be disabled', function () {
    $this->actingAs($this->user)->post(route('user.two-factor.enable'), ['current_password' => 'password']);

    $this->actingAs($this->user)
        ->delete(route('user.two-factor.disable'), ['current_password' => 'password'])
        ->assertSessionHas('success');

    expect($this->user->fresh()->two_factor_secret)->toBeNull();
});

test('enabling and disabling two-factor authentication requires the current password', function () {
    $this->actingAs($this->user)
        ->post(route('user.two-factor.enable'), ['current_password' => 'incorrect'])
        ->assertSessionHasErrors('current_password');

    expect($this->user->fresh()->two_factor_secret)->toBeNull();

    $this->actingAs($this->user)
        ->post(route('user.two-factor.enable'), ['current_password' => 'password']);

    $this->actingAs($this->user)
        ->delete(route('user.two-factor.disable'), ['current_password' => 'incorrect'])
        ->assertSessionHasErrors('current_password');

    expect($this->user->fresh()->two_factor_secret)->not->toBeNull();
});

test('a confirmed user is challenged for two-factor authentication on login', function () {
    $this->actingAs($this->user)
        ->post(route('user.two-factor.enable'), ['current_password' => 'password']);

    $secret = decrypt($this->user->fresh()->two_factor_secret);
    $code = app(Google2FA::class)->getCurrentOtp($secret);

    $this->actingAs($this->user)
        ->post(route('two-factor.verify'), ['code' => $code]);

    auth()->logout();

    $this->post(route('login.store'), [
        'username' => $this->user->username,
        'password' => 'password',
    ])
        ->assertRedirect(route('two-factor.login'))
        ->assertSessionHas('login.id', $this->user->id);
});

test('two-factor confirmation attempts are throttled', function () {
    $this->actingAs($this->user)
        ->post(route('user.two-factor.enable'), ['current_password' => 'password']);

    foreach (range(1, 5) as $attempt) {
        $this->post(route('two-factor.verify'), ['code' => '000000'])
            ->assertSessionHasErrorsIn('confirmTwoFactorAuthentication', 'code');
    }

    $this->post(route('two-factor.verify'), ['code' => '000000'])
        ->assertTooManyRequests();
});

test('legacy confirmations migrate to the canonical timestamp', function () {
    Schema::table('users', function (Blueprint $table): void {
        $table->boolean('two_factor_confirmed')->default(false)->after('two_factor_recovery_codes');
    });

    DB::table('users')->where('id', $this->user->id)->update([
        'two_factor_secret' => encrypt('test-secret'),
        'two_factor_confirmed' => true,
        'two_factor_confirmed_at' => null,
    ]);

    $migration = require database_path('migrations/2026_07_16_010000_migrate_two_factor_confirmation_state.php');
    $migration->up();

    expect($this->user->fresh()->two_factor_confirmed_at)->not->toBeNull()
        ->and(Schema::hasColumn('users', 'two_factor_confirmed'))->toBeFalse();
});

test('two-factor settings routes do not duplicate the user settings prefix', function () {
    expect(route('user.two-factor.enable', absolute: false))->toBe('/user/settings/two-factor-authentication')
        ->and(route('two-factor.verify', absolute: false))->toBe('/user/settings/two-factor-authentication/confirm')
        ->and(route('user.two-factor.disable', absolute: false))->toBe('/user/settings/two-factor-authentication');
});
