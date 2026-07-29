<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The mirror of tests/Feature/Misc/TestDatabaseIsolationTest: this suite must
 * be on Ada's own database, not the Arcturus one.
 */
test('the ada suite runs against the ada database', function () {
    expect(DB::connection()->getDatabaseName())->toBe(env('DB_ADA_DATABASE', 'testing_ada'));
});

test('the ada database holds the ef schema plus the compatibility mirror', function () {
    expect(Schema::hasTable('players'))->toBeTrue('The Ada suite is pointed at an Arcturus database')
        ->and(Schema::hasTable('player_data'))->toBeTrue()
        ->and(Schema::hasTable('roles'))->toBeTrue()
        // Atom keeps a users row on Ada so its own foreign keys stay valid.
        ->and(Schema::hasTable('users'))->toBeTrue();
});

test('the ada suite resolves the ada driver', function () {
    expect(config('emulator.driver'))->toBe('ada');
});
