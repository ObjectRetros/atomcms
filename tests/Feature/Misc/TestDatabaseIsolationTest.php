<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The two suites own separate databases because Ada and Arcturus declare
 * overlapping table names. Running one against the other's schema produces a
 * wave of confusing query errors rather than a clear failure, so assert the
 * connection really is the one this suite expects.
 */
test('the feature suite runs against the arcturus database', function () {
    expect(DB::connection()->getDatabaseName())->toBe(env('DB_DATABASE', 'testing'));
});

test('the feature database holds the arcturus schema', function () {
    // users is Arcturus' own player table; Ada keeps players and mirrors users.
    expect(Schema::hasTable('users'))->toBeTrue()
        ->and(Schema::hasTable('players'))->toBeFalse('The Arcturus suite is pointed at an Ada database');
});

test('the two suites are configured for different databases', function () {
    expect(env('DB_ADA_DATABASE', 'testing_ada'))
        ->not->toBe(env('DB_DATABASE', 'testing'));
});
