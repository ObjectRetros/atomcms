<?php

use App\Emulator\Contracts\PlayerSettingsRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Renaming is now enforced server-side against the emulator's single-use
 * grant. Ada has no schema for that grant, so its driver answers false and the
 * rename must be refused rather than reaching for an Arcturus-only table.
 *
 * The motto and email paths through the same action are covered in
 * tests/Ada/AdaSupportTest.php.
 */
beforeEach(function () {
    installHotel();
});

test('ada refuses a rename it never granted', function () {
    $user = User::factory()->create(['online' => false]);
    $original = $user->username;

    expect(app(PlayerSettingsRepository::class)->canChangeName($user))->toBeFalse();

    $this->actingAs($user)
        ->put(route('settings.account.update'), [
            'mail' => $user->mail,
            'motto' => $user->motto,
            'username' => 'RenamedOnAda',
        ])
        ->assertSessionHasErrors('username');

    expect($user->fresh()->username)->toBe($original)
        ->and(DB::table('players')->where('id', $user->id)->value('username'))->toBe($original);
});

test('an online ada player cannot change settings without a bridge', function () {
    // No RCON means no way to tell a live session, so the player has to be
    // offline. The guard must not call the bridge to work that out.
    $user = User::factory()->create();

    DB::table('player_data')->where('player_id', $user->id)->update(['is_online' => true]);

    $this->actingAs($user->fresh())
        ->put(route('settings.account.update'), [
            'mail' => 'ada-online@example.com',
            'motto' => 'Should not stick',
        ])
        ->assertSessionHasErrors();

    expect($user->fresh()->mail)->not->toBe('ada-online@example.com');
});
