<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    installHotel();
});

test('security and economy fields cannot be mass assigned', function () {
    $user = new User;

    $user->fill([
        'username' => 'AllowedName',
        'website_balance' => 500,
        'machine_id' => 'attacker-controlled',
        'two_factor_secret' => 'attacker-controlled',
    ]);

    expect($user->username)->toBe('AllowedName')
        ->and($user->isDirty('website_balance'))->toBeFalse()
        ->and($user->isDirty('machine_id'))->toBeFalse()
        ->and($user->isDirty('two_factor_secret'))->toBeFalse();
});

test('an offline user can update their motto', function () {
    $user = User::factory()->create(['motto' => 'Old motto', 'online' => '0']);

    $this->actingAs($user)
        ->put(route('settings.account.update'), [
            'mail' => $user->mail,
            'motto' => 'Fresh motto',
        ])
        ->assertRedirect(route('settings.account.show'))
        ->assertSessionHas('success');

    expect($user->refresh()->motto)->toBe('Fresh motto');
});

test('changing the email requires re-authentication', function () {
    $user = User::factory()->create(['online' => '0']);

    $this->actingAs($user)
        ->put(route('settings.account.update'), [
            'mail' => 'new-address@example.com',
            'motto' => $user->motto,
        ])
        ->assertSessionHasErrors('current_password');

    expect($user->refresh()->mail)->not->toBe('new-address@example.com');

    $this->actingAs($user)
        ->put(route('settings.account.update'), [
            'mail' => 'new-address@example.com',
            'motto' => $user->motto,
            'current_password' => 'password',
        ])
        ->assertSessionHas('success');

    expect($user->refresh()->mail)->toBe('new-address@example.com');
});

test('an online user cannot change settings while rcon is down', function () {
    $user = User::factory()->create(['online' => '1']);

    $this->actingAs($user)
        ->put(route('settings.account.update'), [
            'mail' => $user->mail,
            'motto' => 'Should not stick',
        ])
        ->assertSessionHasErrors();

    expect($user->refresh()->motto)->not->toBe('Should not stick');
});

test('a user can change their password with their current one', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put(route('settings.password.update'), [
            'current_password' => 'password',
            'password' => 'N3wSecret!pass',
            'password_confirmation' => 'N3wSecret!pass',
        ])
        ->assertRedirect(route('settings.password.show'))
        ->assertSessionHas('success');

    expect(Hash::check('N3wSecret!pass', $user->refresh()->password))->toBeTrue();
});

test('a wrong current password is rejected', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put(route('settings.password.update'), [
            'current_password' => 'not-my-password',
            'password' => 'N3wSecret!pass',
            'password_confirmation' => 'N3wSecret!pass',
        ])
        ->assertSessionHasErrors();

    expect(Hash::check('password', $user->refresh()->password))->toBeTrue();
});
