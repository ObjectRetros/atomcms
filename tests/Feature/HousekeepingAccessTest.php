<?php

use App\Models\User;
use App\Models\User\Ban;
use App\Models\WebsiteHousekeepingPermission;
use Illuminate\Support\Facades\Gate;

function grantHousekeepingPermission(string $permission, int $minRank): void
{
    WebsiteHousekeepingPermission::query()->create([
        'permission' => $permission,
        'min_rank' => $minRank,
        'description' => "Testing {$permission}",
    ]);
}

test('guests are redirected away from the housekeeping panel', function () {
    installHotel();

    $this->get('/housekeeping')->assertRedirect();
});

test('users without the housekeeping permission are forbidden', function () {
    installHotel();

    $user = User::factory()->create();

    $this->actingAs($user)->get('/housekeeping')->assertForbidden();
});

test('forced staff two-factor authentication also protects housekeeping', function () {
    installHotel();
    grantHousekeepingPermission('can_access_housekeeping', 6);
    setSetting('force_staff_2fa', '1');
    setSetting('min_staff_rank', '4');

    $staff = User::factory()->create(['rank' => 6]);

    $this->actingAs($staff)
        ->get('/housekeeping')
        ->assertRedirect(route('settings.two-factor'));
});

test('banned staff cannot access housekeeping', function () {
    installHotel();
    grantHousekeepingPermission('can_access_housekeeping', 6);
    setSetting('force_staff_2fa', '0');

    $staff = User::factory()->create(['rank' => 6]);
    Ban::create([
        'user_id' => $staff->id,
        'ip' => '',
        'machine_id' => '',
        'user_staff_id' => $staff->id,
        'timestamp' => time(),
        'ban_expire' => time() + 3600,
        'ban_reason' => 'Testing',
        'type' => 'account',
    ]);

    $this->actingAs($staff)
        ->get('/housekeeping')
        ->assertRedirect(route('banned.show'));
});

test('maintenance restrictions also protect housekeeping', function () {
    installHotel();
    grantHousekeepingPermission('can_access_housekeeping', 6);
    setSetting('maintenance_enabled', '1');
    setSetting('min_maintenance_login_rank', '7');
    setSetting('force_staff_2fa', '0');

    $staff = User::factory()->create(['rank' => 6]);

    $this->actingAs($staff)
        ->get('/housekeeping')
        ->assertRedirect(route('maintenance.show'));
});

test('staff can only manage users below their own rank', function () {
    installHotel();
    grantHousekeepingPermission('edit_user', 6);

    $actor = User::factory()->create(['rank' => 6]);
    $lower = User::factory()->create(['rank' => 5]);
    $equal = User::factory()->create(['rank' => 6]);
    $higher = User::factory()->create(['rank' => 7]);

    expect(Gate::forUser($actor)->allows('update', $lower))->toBeTrue()
        ->and(Gate::forUser($actor)->allows('update', $actor))->toBeFalse()
        ->and(Gate::forUser($actor)->allows('update', $equal))->toBeFalse()
        ->and(Gate::forUser($actor)->allows('update', $higher))->toBeFalse();
});

test('housekeeping policies evaluate the supplied actor', function () {
    installHotel();
    grantHousekeepingPermission('edit_user', 6);

    $authenticated = User::factory()->create(['rank' => 1]);
    $actor = User::factory()->create(['rank' => 6]);
    $this->actingAs($authenticated);

    expect(Gate::forUser($actor)->allows('viewAny', User::class))->toBeTrue();
});

test('the user editor only resolves records below the actor rank', function () {
    installHotel();
    grantHousekeepingPermission('can_access_housekeeping', 6);
    grantHousekeepingPermission('edit_user', 6);
    grantHousekeepingPermission('reset_user_password', 6);
    setSetting('force_staff_2fa', '0');

    $actor = User::factory()->create(['rank' => 6]);
    $lower = User::factory()->create(['rank' => 5]);
    $higher = User::factory()->create(['rank' => 7]);

    $this->actingAs($actor)
        ->get("/housekeeping/user-management/users/{$lower->id}/edit")
        ->assertOk();

    $this->actingAs($actor)
        ->get("/housekeeping/user-management/users/{$higher->id}/edit")
        ->assertNotFound();
});
