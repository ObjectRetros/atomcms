<?php

use App\Emulator\Contracts\BadgeRepository;
use App\Emulator\Data\OwnedBadge;
use App\Emulator\Drivers\Arcturus\ArcturusBadgeRepository;
use App\Models\User;

/**
 * Arcturus half of the badge conformance suite. Ada owns overlapping table
 * names so it cannot share this database; its half is in tests/Ada.
 */
dataset('badge drivers', [
    'arcturus' => [fn (): BadgeRepository => new ArcturusBadgeRepository],
]);

beforeEach(function () {
    installHotel();
});

test('granted badges are listed by code', function (BadgeRepository $badges) {
    $user = User::factory()->create();

    $badges->grant($user, 'ACH_Login1');
    $badges->grant($user, 'ACH_RoomEntry1');

    expect($badges->codes($user))->toEqualCanonicalizing(['ACH_Login1', 'ACH_RoomEntry1']);
})->with('badge drivers');

test('granting an owned badge is a no-op', function (BadgeRepository $badges) {
    $user = User::factory()->create();

    $badges->grant($user, 'ACH_Login1');
    $badges->grant($user, 'ACH_Login1');

    expect($badges->codes($user))->toBe(['ACH_Login1']);
})->with('badge drivers');

test('a badge can be revoked', function (BadgeRepository $badges) {
    $user = User::factory()->create();

    $badges->grant($user, 'ACH_Login1');
    $badges->revoke($user, 'ACH_Login1');

    expect($badges->codes($user))->toBe([]);
})->with('badge drivers');

test('badges paginate as normalised entries, newest first', function (BadgeRepository $badges) {
    $user = User::factory()->create();

    $badges->grant($user, 'ACH_Older');
    $badges->grant($user, 'ACH_Newer');

    $page = $badges->paginate($user, 16, 'badges_page');

    expect($page->total())->toBe(2)
        ->and($page->items()[0])->toBeInstanceOf(OwnedBadge::class)
        ->and($page->items()[0]->badge_code)->toBe('ACH_Newer');
})->with('badge drivers');

test('the active badge relation follows the selected driver schema', function (BadgeRepository $badges) {
    $user = User::factory()->create();

    $badges->grant($user, 'ACH_Relation');

    expect($badges->relation($user)->count())->toBe(1)
        ->and($user->emulatorBadges()->count())->toBe(1);
})->with('badge drivers');

test('granting a badge twice never duplicates the row', function (BadgeRepository $badges) {
    // Neither emulator constrains this in the database, so the driver has to
    // guarantee it. Two players owning the same badge must still be allowed.
    $user = User::factory()->create();
    $other = User::factory()->create();

    $badges->grant($user, 'ACH_Once');
    $badges->grant($user, 'ACH_Once');
    $badges->grant($other, 'ACH_Once');

    expect($badges->codes($user))->toBe(['ACH_Once'])
        ->and($badges->relation($user)->count())->toBe(1)
        ->and($badges->relation($other)->count())->toBe(1);
})->with('badge drivers');

test('revoking a badge leaves other owners alone', function (BadgeRepository $badges) {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $badges->grant($user, 'ACH_Shared');
    $badges->grant($other, 'ACH_Shared');
    $badges->revoke($user, 'ACH_Shared');

    expect($badges->codes($user))->toBe([])
        ->and($badges->codes($other))->toBe(['ACH_Shared']);
})->with('badge drivers');

test('deleting a badge model removes only that row', function () {
    // users_badges carries a real auto-increment key again, so Eloquent can
    // address a single row; a keyed delete used to wipe every badge the user
    // owned. The relation comes from the driver - User has no badges() of its
    // own, because the table differs per emulator.
    $badges = new ArcturusBadgeRepository;
    $user = User::factory()->create();

    $keep = $badges->relation($user)->create(['slot_id' => 0, 'badge_code' => 'ACH_Keep1']);
    $delete = $badges->relation($user)->create(['slot_id' => 0, 'badge_code' => 'ACH_Delete1']);

    $delete->delete();

    expect($badges->relation($user)->pluck('badge_code')->all())->toBe(['ACH_Keep1'])
        ->and($keep->fresh())->not->toBeNull();
});
