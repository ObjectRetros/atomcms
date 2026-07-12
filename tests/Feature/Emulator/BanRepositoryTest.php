<?php

use App\Emulator\Drivers\Arcturus\ArcturusBanRepository;
use App\Emulator\Drivers\Plus\PlusBanRepository;
use App\Models\User;
use App\Models\User\Ban;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    installHotel();
});

afterEach(function () {
    // The Plus tests shadow the real bans table for this connection.
    DB::statement('DROP TEMPORARY TABLE IF EXISTS bans');
});

/**
 * Shadows the Arcturus bans table with the Plus EMU schema for this
 * connection only (temporary tables take name precedence per session).
 */
function usePlusBansTable(): void
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
}

test('arcturus resolves active ip and account bans', function () {
    $bans = new ArcturusBanRepository;
    $user = User::factory()->create();

    Ban::create([
        'user_id' => $user->id, 'ip' => '10.0.0.1', 'machine_id' => '', 'user_staff_id' => $user->id,
        'timestamp' => time(), 'ban_expire' => time() + 3600, 'ban_reason' => 'IP misuse', 'type' => 'ip',
    ]);
    Ban::create([
        'user_id' => $user->id, 'ip' => '', 'machine_id' => '', 'user_staff_id' => $user->id,
        'timestamp' => time(), 'ban_expire' => time() + 3600, 'ban_reason' => 'Account misuse', 'type' => 'account',
    ]);

    expect($bans->activeIpBan('10.0.0.1')?->ban_reason)->toBe('IP misuse')
        ->and($bans->activeIpBan('10.0.0.2'))->toBeNull()
        ->and($bans->activeAccountBan($user)?->ban_reason)->toBe('Account misuse');
});

test('arcturus ignores expired bans', function () {
    $bans = new ArcturusBanRepository;
    $user = User::factory()->create();

    Ban::create([
        'user_id' => $user->id, 'ip' => '10.0.0.1', 'machine_id' => '', 'user_staff_id' => $user->id,
        'timestamp' => time(), 'ban_expire' => time() - 10, 'ban_reason' => 'Old', 'type' => 'ip',
    ]);

    expect($bans->activeIpBan('10.0.0.1'))->toBeNull();
});

test('plus resolves active ip and account bans', function () {
    usePlusBansTable();

    $bans = new PlusBanRepository;
    $user = User::factory()->create();

    DB::table('bans')->insert([
        ['bantype' => 'ip', 'value' => '10.0.0.1', 'reason' => 'IP misuse', 'expire' => time() + 3600],
        ['bantype' => 'user', 'value' => $user->username, 'reason' => 'Account misuse', 'expire' => time() + 3600],
    ]);

    expect($bans->activeIpBan('10.0.0.1')?->ban_reason)->toBe('IP misuse')
        ->and($bans->activeIpBan('10.0.0.2'))->toBeNull()
        ->and($bans->activeAccountBan($user)?->ban_reason)->toBe('Account misuse')
        ->and($bans->activeAccountBan($user)?->ban_expire)->toBeGreaterThan(time());
});

test('plus ignores expired bans', function () {
    usePlusBansTable();

    $bans = new PlusBanRepository;
    $user = User::factory()->create();

    DB::table('bans')->insert([
        ['bantype' => 'user', 'value' => $user->username, 'reason' => 'Old', 'expire' => time() - 10],
    ]);

    expect($bans->activeAccountBan($user))->toBeNull();
});
