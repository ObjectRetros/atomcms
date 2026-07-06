<?php

use App\Emulator\Contracts\BadgeRepository;
use App\Emulator\Data\OwnedBadge;
use App\Emulator\Drivers\Arcturus\ArcturusBadgeRepository;
use App\Emulator\Drivers\Plus\PlusBadgeRepository;
use App\Models\User;

/**
 * Conformance tests every badge driver must pass against its own schema.
 */
dataset('badge drivers', [
    'arcturus' => [fn (): BadgeRepository => new ArcturusBadgeRepository],
    'plus' => [fn (): BadgeRepository => new PlusBadgeRepository],
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
