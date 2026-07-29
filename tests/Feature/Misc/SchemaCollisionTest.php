<?php

use App\Database\Schema\SchemaReclaimer;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DDL commits the transaction RefreshDatabase wraps each test in, so all
 * schema work here goes through a clone of the default connection that the
 * test transaction does not manage, and every scratch table is dropped
 * explicitly.
 */
function reclaimConnection(): Connection
{
    return DB::connection('reclaim_test');
}

function reclaimSchema(): Builder
{
    return Schema::connection('reclaim_test');
}

function dropReclaimScratch(): void
{
    $connection = reclaimConnection();

    $tables = $connection->select(
        "SELECT TABLE_NAME AS name FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
            AND (TABLE_NAME LIKE 'reclaim\\_%' OR TABLE_NAME LIKE 'old\\_%\\_reclaim\\_%')",
    );

    $connection->statement('SET FOREIGN_KEY_CHECKS=0');

    foreach ($tables as $table) {
        $connection->statement(sprintf('DROP TABLE IF EXISTS `%s`', $table->name));
    }

    $connection->statement('SET FOREIGN_KEY_CHECKS=1');
}

beforeEach(function () {
    config()->set(
        'database.connections.reclaim_test',
        config('database.connections.' . config('database.default')),
    );

    dropReclaimScratch();
});

afterEach(function () {
    dropReclaimScratch();

    DB::purge('reclaim_test');
});

test('a colliding table is archived with its data and foreign key names freed', function () {
    config()->set('habbo.migrations.rename_tables', true);

    $connection = reclaimConnection();

    reclaimSchema()->create('reclaim_parent', function (Blueprint $table) {
        $table->id();
    });

    $connection->statement(
        'CREATE TABLE `reclaim_child` (
            `id` BIGINT UNSIGNED PRIMARY KEY,
            `parent_id` BIGINT UNSIGNED,
            KEY `reclaim_child_parent_id_index` (`parent_id`),
            CONSTRAINT `reclaim_child_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `reclaim_parent` (`id`)
        )',
    );
    $connection->table('reclaim_parent')->insert(['id' => 1]);
    $connection->table('reclaim_child')->insert(['id' => 5, 'parent_id' => 1]);

    reclaimSchema()->create('reclaim_child', function (Blueprint $table) {
        $table->id();
        $table->foreignId('parent_id')
            ->constrained('reclaim_parent');
    });

    $archived = SchemaReclaimer::prefix() . 'reclaim_child';

    // The new table claimed the name (and the conventional FK name) while
    // the archive kept its row behind renamed constraints.
    expect(reclaimSchema()->hasTable('reclaim_child'))->toBeTrue()
        ->and($connection->table('reclaim_child')->count())->toBe(0)
        ->and(reclaimSchema()->hasTable($archived))->toBeTrue()
        ->and($connection->table($archived)->count())->toBe(1);

    $archivedConstraints = collect($connection->select(
        'SELECT CONSTRAINT_NAME AS name FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_TYPE = ?',
        [$archived, 'FOREIGN KEY'],
    ))->pluck('name');

    expect($archivedConstraints)->toHaveCount(1)
        ->and($archivedConstraints->first())->toStartWith('old_');
});

test('a colliding column is archived with its data, index and foreign key', function () {
    config()->set('habbo.migrations.rename_tables', true);

    $connection = reclaimConnection();

    reclaimSchema()->create('reclaim_parent', function (Blueprint $table) {
        $table->id();
    });

    $connection->statement(
        'CREATE TABLE `reclaim_users` (
            `id` BIGINT UNSIGNED PRIMARY KEY,
            `parent_id` BIGINT UNSIGNED,
            `nick` VARCHAR(64),
            UNIQUE KEY `reclaim_users_nick_unique` (`nick`),
            KEY `reclaim_users_parent_id_index` (`parent_id`),
            CONSTRAINT `reclaim_users_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `reclaim_parent` (`id`)
        )',
    );
    $connection->table('reclaim_parent')->insert(['id' => 1]);
    $connection->table('reclaim_users')->insert(['id' => 9, 'parent_id' => 1, 'nick' => 'Dennis']);

    reclaimSchema()->table('reclaim_users', function (Blueprint $table) {
        $table->string('nick', 32)->nullable()->unique();
        $table->foreignId('parent_id')
            ->nullable()
            ->constrained('reclaim_parent');
    });

    $prefix = SchemaReclaimer::prefix();
    $columns = reclaimSchema()->getColumnListing('reclaim_users');

    expect($columns)->toContain('nick', 'parent_id', $prefix . 'nick', $prefix . 'parent_id')
        ->and($connection->table('reclaim_users')->value($prefix . 'nick'))->toBe('Dennis')
        ->and($connection->table('reclaim_users')->value('nick'))->toBeNull();

    $indexes = collect($connection->select(
        'SELECT DISTINCT INDEX_NAME AS name FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?',
        ['reclaim_users'],
    ))->pluck('name');

    $constraints = collect($connection->select(
        'SELECT CONSTRAINT_NAME AS name FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_TYPE = ?',
        ['reclaim_users', 'FOREIGN KEY'],
    ))->pluck('name');

    // The archived column kept its unique index and foreign key under old_
    // names, while the replacements claimed the conventional ones.
    expect($indexes)->toContain($prefix . 'reclaim_users_nick_unique')
        ->and($indexes)->toContain('reclaim_users_nick_unique')
        ->and($constraints)->toContain($prefix . 'reclaim_users_parent_id_foreign')
        ->and($constraints)->toContain('reclaim_users_parent_id_foreign');
});

test('a collision without the rename flag fails with an actionable error', function () {
    config()->set('habbo.migrations.rename_tables', false);

    reclaimSchema()->create('reclaim_existing', function (Blueprint $table) {
        $table->id();
    });

    expect(fn () => reclaimSchema()->create('reclaim_existing', function (Blueprint $table) {
        $table->id();
    }))->toThrow(RuntimeException::class, 'RENAME_COLLIDING_TABLES');

    expect(fn () => reclaimSchema()->table('reclaim_existing', function (Blueprint $table) {
        $table->bigInteger('id');
    }))->toThrow(RuntimeException::class, 'RENAME_COLLIDING_TABLES');

    // The colliding table was left untouched.
    expect(reclaimSchema()->hasTable('reclaim_existing'))->toBeTrue();
});
