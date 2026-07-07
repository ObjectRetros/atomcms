<?php

use App\Filament\Resources\Hotel\ChatlogRooms\ChatlogRoomResource;
use App\Filament\Resources\Hotel\PlusChatlogs\PlusChatlogResource;
use App\Filament\Resources\User\Bans\BanResource;
use App\Filament\Resources\User\PlusBans\PlusBanResource;
use App\Models\Plus\PlusBan;
use App\Models\Plus\PlusChatlog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    installHotel();
});

afterEach(function () {
    // These tests shadow the real Arcturus tables for this connection.
    DB::statement('DROP TEMPORARY TABLE IF EXISTS bans');
    DB::statement('DROP TEMPORARY TABLE IF EXISTS chatlogs');
});

function shadowPlusHousekeepingTables(): void
{
    DB::statement(<<<'SQL'
        CREATE TEMPORARY TABLE bans (
            id INT AUTO_INCREMENT PRIMARY KEY,
            bantype VARCHAR(10) NOT NULL,
            value VARCHAR(255) NOT NULL,
            reason VARCHAR(255) NOT NULL DEFAULT '',
            expire INT NOT NULL DEFAULT 0,
            added_by VARCHAR(50) NOT NULL DEFAULT '',
            added_date INT NOT NULL DEFAULT 0
        )
    SQL);

    DB::statement(<<<'SQL'
        CREATE TEMPORARY TABLE chatlogs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            room_id INT NOT NULL,
            hour INT NOT NULL DEFAULT 0,
            minute INT NOT NULL DEFAULT 0,
            timestamp INT NOT NULL DEFAULT 0,
            message VARCHAR(255) NOT NULL DEFAULT ''
        )
    SQL);
}

test('plus bans can be managed through the model', function () {
    shadowPlusHousekeepingTables();

    $ban = PlusBan::create([
        'bantype' => 'user',
        'value' => 'Griefer',
        'reason' => 'Testing',
        'expire' => time() + 3600,
        'added_by' => 'Staff',
        'added_date' => time(),
    ]);

    expect(PlusBan::count())->toBe(1)
        ->and($ban->bantype)->toBe('user');

    $ban->update(['reason' => 'Updated reason']);
    expect(PlusBan::whereKey($ban->id)->value('reason'))->toBe('Updated reason');

    $ban->delete();
    expect(PlusBan::count())->toBe(0);
});

test('plus chatlogs list newest first with their sender', function () {
    shadowPlusHousekeepingTables();

    $user = User::factory()->create();

    DB::table('chatlogs')->insert([
        ['user_id' => $user->id, 'room_id' => 5, 'timestamp' => time() - 60, 'message' => 'older line'],
        ['user_id' => $user->id, 'room_id' => 5, 'timestamp' => time(), 'message' => 'newest line'],
    ]);

    $logs = PlusChatlog::with('user')->orderByDesc('timestamp')->get();

    expect($logs)->toHaveCount(2)
        ->and($logs->first()->message)->toBe('newest line')
        ->and($logs->first()->user->username)->toBe($user->username);
});

test('driver-bound housekeeping resources swap with the configured driver', function () {
    config(['emulator.driver' => 'arcturus']);

    expect(BanResource::shouldRegisterNavigation())->toBeTrue()
        ->and(ChatlogRoomResource::shouldRegisterNavigation())->toBeTrue()
        ->and(PlusBanResource::shouldRegisterNavigation())->toBeFalse()
        ->and(PlusChatlogResource::shouldRegisterNavigation())->toBeFalse();

    config(['emulator.driver' => 'plus']);

    expect(BanResource::shouldRegisterNavigation())->toBeFalse()
        ->and(ChatlogRoomResource::shouldRegisterNavigation())->toBeFalse()
        ->and(PlusBanResource::shouldRegisterNavigation())->toBeTrue()
        ->and(PlusChatlogResource::shouldRegisterNavigation())->toBeTrue();
});
