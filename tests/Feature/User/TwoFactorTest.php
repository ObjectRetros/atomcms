<?php

use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

beforeEach(function () {
    installHotel();
    $this->user = User::factory()->create();
});

test('a user can enable two-factor authentication', function () {
    $this->actingAs($this->user)
        ->post(route('user.two-factor.enable'))
        ->assertRedirect(route('settings.two-factor'))
        ->assertSessionHas('success');

    $user = $this->user->fresh();

    expect($user->two_factor_secret)->not->toBeNull()
        ->and($user->two_factor_recovery_codes)->not->toBeNull();
});

test('a valid code confirms two-factor authentication', function () {
    $this->actingAs($this->user)->post(route('user.two-factor.enable'));

    $secret = decrypt($this->user->fresh()->two_factor_secret);
    $code = app(Google2FA::class)->getCurrentOtp($secret);

    $this->actingAs($this->user)
        ->post(route('two-factor.verify'), ['code' => $code])
        ->assertSessionHas('success');

    // The CMS tracks confirmation in its own two_factor_confirmed column.
    expect((bool) $this->user->fresh()->two_factor_confirmed)->toBeTrue();
});

test('an invalid code is rejected', function () {
    $this->actingAs($this->user)->post(route('user.two-factor.enable'));

    $this->actingAs($this->user)
        ->post(route('two-factor.verify'), ['code' => '000000'])
        ->assertSessionHasErrors();

    expect((bool) $this->user->fresh()->two_factor_confirmed)->toBeFalse();
});

test('an empty code is rejected without a server error', function () {
    $this->actingAs($this->user)->post(route('user.two-factor.enable'));

    $this->actingAs($this->user)
        ->post(route('two-factor.verify'), ['code' => ''])
        ->assertSessionHasErrors();

    expect((bool) $this->user->fresh()->two_factor_confirmed)->toBeFalse();
});

test('two-factor authentication can be disabled', function () {
    $this->actingAs($this->user)->post(route('user.two-factor.enable'));

    $this->actingAs($this->user)
        ->delete(route('user.two-factor.disable'))
        ->assertSessionHas('success');

    expect($this->user->fresh()->two_factor_secret)->toBeNull();
});
