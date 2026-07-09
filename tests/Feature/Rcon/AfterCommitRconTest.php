<?php

use App\Models\User;
use App\Services\AfterCommitRcon;
use App\Services\FakeRcon;
use Illuminate\Support\Facades\DB;

test('rcon sends fire only after the transaction commits', function () {
    installHotel();

    $inner = new FakeRcon(connected: true);
    $rcon = new AfterCommitRcon($inner);
    $user = User::factory()->create();

    DB::transaction(function () use ($rcon, $user, $inner) {
        $rcon->giveBadge($user, 'ACH_Test1');

        expect($inner->calls)->toBeEmpty();
    });

    expect($inner->calls)->toHaveCount(1)
        ->and($inner->calls[0]['method'])->toBe('giveBadge');
});

test('rcon sends are dropped when the transaction rolls back', function () {
    installHotel();

    $inner = new FakeRcon(connected: true);
    $rcon = new AfterCommitRcon($inner);
    $user = User::factory()->create();

    try {
        DB::transaction(function () use ($rcon, $user) {
            $rcon->giveBadge($user, 'ACH_Test1');

            throw new RuntimeException('rollback');
        });
    } catch (RuntimeException) {
        // expected
    }

    expect($inner->calls)->toBeEmpty();
});

test('rcon sends outside a transaction run immediately', function () {
    installHotel();

    $inner = new FakeRcon(connected: true);
    $rcon = new AfterCommitRcon($inner);
    $user = User::factory()->create();

    $rcon->giveBadge($user, 'ACH_Test1');

    expect($inner->calls)->toHaveCount(1);
});
