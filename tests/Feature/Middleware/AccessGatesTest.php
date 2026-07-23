<?php

use App\Models\User;
use App\Models\User\Ban;

function banUser(User $user, string $type, string $ip = ''): Ban
{
    return Ban::create([
        'user_id' => $user->id,
        'ip' => $ip,
        'machine_id' => '',
        'user_staff_id' => $user->id,
        'timestamp' => time(),
        'ban_expire' => time() + 3600,
        'ban_reason' => 'Testing',
        'type' => $type,
    ]);
}

test('an IP ban redirects guests to the banned page', function () {
    installHotel();

    banUser(User::factory()->create(), 'ip', '127.0.0.1');

    $this->get(route('welcome'))->assertRedirect(route('banned.show'));
});

test('an account ban redirects the user to the banned page', function () {
    installHotel();

    $user = User::factory()->create();
    banUser($user, 'account');

    $this->actingAs($user)->get(route('me.show'))->assertRedirect(route('banned.show'));
});

test('an expired ban does not block the user', function () {
    installHotel();

    $user = User::factory()->create();
    $ban = banUser($user, 'account');
    $ban->update(['ban_expire' => time() - 10]);

    $this->actingAs($user)->get(route('me.show'))->assertOk();
});

test('maintenance sends guests to the maintenance page', function () {
    installHotel();
    setSetting('maintenance_enabled', '1');

    $this->get(route('welcome'))->assertRedirect(route('maintenance.show'));
});

test('maintenance does not allow arbitrary guest posts', function () {
    installHotel();
    setSetting('maintenance_enabled', '1');

    $this->post(route('register.store'), [])->assertRedirect(route('maintenance.show'));
});

test('the housekeeping login remains available during maintenance', function () {
    installHotel();
    setSetting('maintenance_enabled', '1');

    $this->get('/housekeeping/login')->assertOk();
});

test('staff above the maintenance rank bypass maintenance', function () {
    installHotel();
    setSetting('maintenance_enabled', '1');
    setSetting('min_maintenance_login_rank', '5');
    setSetting('force_staff_2fa', '0');

    $staff = User::factory()->create(['rank' => 7]);

    $this->actingAs($staff)->get(route('me.show'))->assertOk();
    $this->actingAs($staff)->get(route('maintenance.show'))->assertRedirect(route('me.show'));
});

test('regular users are held on the maintenance page during maintenance', function () {
    installHotel();
    setSetting('maintenance_enabled', '1');

    $user = User::factory()->create(['rank' => 1]);

    $this->actingAs($user)->get(route('me.show'))->assertRedirect(route('maintenance.show'));
});
