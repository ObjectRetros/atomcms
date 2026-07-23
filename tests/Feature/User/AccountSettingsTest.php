<?php

use App\Models\Miscellaneous\WebsiteWordfilter;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    installHotel();
});

function userWithNameChangeFlag(string $flag): User
{
    $user = User::factory()->create(['online' => '0']);
    $user->settings()->updateOrCreate([], ['allow_name_change' => $flag]);

    return $user->refresh();
}

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

test('the settings page only offers a rename when the emulator granted one', function () {
    $user = userWithNameChangeFlag('1');

    $this->actingAs($user)
        ->get(route('settings.account.show'))
        ->assertOk()
        ->assertSee('name="username"', false);

    $user->settings->update(['allow_name_change' => '0']);

    $this->actingAs($user)
        ->get(route('settings.account.show'))
        ->assertOk()
        ->assertDontSee('name="username"', false);
});

test('a granted rename is persisted and consumes the single-use flag', function () {
    $user = userWithNameChangeFlag('1');

    $this->actingAs($user)
        ->put(route('settings.account.update'), [
            'username' => 'FreshName',
            'mail' => $user->mail,
            'motto' => $user->motto,
        ])
        ->assertRedirect(route('settings.account.show'))
        ->assertSessionHas('success');

    $user->refresh();

    expect($user->username)->toBe('FreshName')
        ->and($user->settings->allow_name_change)->toBe('0');
});

test('a rename is rejected server-side without the emulator flag', function () {
    $user = userWithNameChangeFlag('0');
    $originalName = $user->username;

    $this->actingAs($user)
        ->put(route('settings.account.update'), [
            'username' => 'SneakyRename',
            'mail' => $user->mail,
            'motto' => $user->motto,
        ])
        ->assertSessionHasErrors('username');

    expect($user->refresh()->username)->toBe($originalName);
});

test('a rename still enforces username uniqueness', function () {
    User::factory()->create(['username' => 'TakenName']);
    $user = userWithNameChangeFlag('1');
    $originalName = $user->username;

    $this->actingAs($user)
        ->put(route('settings.account.update'), [
            'username' => 'TakenName',
            'mail' => $user->mail,
            'motto' => $user->motto,
        ])
        ->assertSessionHasErrors('username');

    expect($user->refresh()->username)->toBe($originalName)
        ->and($user->settings->allow_name_change)->toBe('1');
});

test('a rename still enforces the website wordfilter', function () {
    setSetting('website_wordfilter_enabled', '1');
    WebsiteWordfilter::create(['word' => 'bobba']);

    $user = userWithNameChangeFlag('1');
    $originalName = $user->username;

    $this->actingAs($user)
        ->put(route('settings.account.update'), [
            'username' => 'BobbaFan',
            'mail' => $user->mail,
            'motto' => $user->motto,
        ])
        ->assertSessionHasErrors('username');

    expect($user->refresh()->username)->toBe($originalName)
        ->and($user->settings->allow_name_change)->toBe('1');
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

test('a user cannot change to a weak password', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put(route('settings.password.update'), [
            'current_password' => 'password',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
        ->assertSessionHasErrors('password');

    expect(Hash::check('password', $user->refresh()->password))->toBeTrue();
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
