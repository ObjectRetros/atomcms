<?php

use App\Enums\HotelSchemaState;
use App\Support\HotelSchemaPreflight;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;

/**
 * The classifications run against a scratch connection so a database can be
 * built up table by table. The hotel case is checked against the real test
 * database, which is the imported Arcturus base.
 */
beforeEach(function () {
    config(['database.connections.preflight' => ['driver' => 'sqlite', 'database' => ':memory:']]);

    $this->scratch = Schema::connection('preflight');
});

function createHotelSignature(Builder $schema): void
{
    $schema->create('users', function (Blueprint $table) {
        $table->id();
        $table->string('username');
        $table->string('password');
        $table->string('auth_ticket');
    });

    $schema->create('rooms', function (Blueprint $table) {
        $table->id();
        $table->integer('owner_id');
        $table->string('model');
    });

    $schema->create('items', function (Blueprint $table) {
        $table->id();
        $table->integer('user_id');
        $table->integer('room_id');
        $table->integer('item_id');
    });

    $schema->create('items_base', function (Blueprint $table) {
        $table->id();
        $table->string('interaction_type');
    });

    $schema->create('catalog_pages', function (Blueprint $table) {
        $table->id();
        $table->integer('page_layout');
    });

    $schema->create('emulator_settings', function (Blueprint $table) {
        $table->string('key');
        $table->string('value');
    });

    $schema->create('permissions', function (Blueprint $table) {
        $table->id();
        $table->string('rank_name');
    });

    $schema->create('users_currency', function (Blueprint $table) {
        $table->integer('user_id');
        $table->integer('type');
        $table->integer('amount');
    });
}

test('an empty database is safe to import into', function () {
    $inspection = HotelSchemaPreflight::inspect('preflight');

    expect($inspection->state)->toBe(HotelSchemaState::Fresh)
        ->and($inspection->state->safeToImport())->toBeTrue();
});

test('a database holding only atom tables is safe to import into', function () {
    $this->scratch->create('migrations', fn (Blueprint $table) => $table->id());
    $this->scratch->create('website_settings', fn (Blueprint $table) => $table->id());
    $this->scratch->create('user_home_items', fn (Blueprint $table) => $table->id());
    $this->scratch->create('website_password_resets', fn (Blueprint $table) => $table->id());

    $inspection = HotelSchemaPreflight::inspect('preflight');

    expect($inspection->state)->toBe(HotelSchemaState::AtomOnly)
        ->and($inspection->state->safeToImport())->toBeTrue();
});

test('a full hotel schema is recognised and never imported over', function () {
    createHotelSignature($this->scratch);

    $inspection = HotelSchemaPreflight::inspect('preflight');

    expect($inspection->state)->toBe(HotelSchemaState::Hotel)
        ->and($inspection->state->safeToImport())->toBeFalse();
});

test('a hotel keeps being recognised when it carries extra plugin tables', function () {
    createHotelSignature($this->scratch);
    $this->scratch->create('plugin_lottery', fn (Blueprint $table) => $table->id());

    expect(HotelSchemaPreflight::inspect('preflight')->state)->toBe(HotelSchemaState::Hotel);
});

test('a database migrated by an emulator is recognised by its migration history alone', function () {
    // Polaris records its Flyway history here before the rest of the schema exists.
    $this->scratch->create('flyway_schema_history', fn (Blueprint $table) => $table->id());

    expect(HotelSchemaPreflight::inspect('preflight')->state)->toBe(HotelSchemaState::Hotel);
});

test('an entity framework database is recognised by its migration history alone', function () {
    // Ada creates this before the rest of its schema exists.
    $this->scratch->create('__EFMigrationsHistory', fn (Blueprint $table) => $table->string('MigrationId'));

    expect(HotelSchemaPreflight::inspect('preflight')->state)->toBe(HotelSchemaState::Hotel);
});

test('an ada schema is recognised without its migration history', function () {
    foreach (['players', 'player_data', 'player_avatar_data', 'player_website_data', 'roles', 'badges'] as $table) {
        $this->scratch->create($table, fn (Blueprint $blueprint) => $blueprint->id());
    }

    $inspection = HotelSchemaPreflight::inspect('preflight');

    expect($inspection->state)->toBe(HotelSchemaState::Hotel)
        ->and($inspection->state->safeToImport())->toBeFalse();
});

test('a half-built hotel is refused instead of being overwritten', function () {
    // An emulator that died partway through creating its schema.
    $this->scratch->create('users', function (Blueprint $table) {
        $table->id();
        $table->string('username');
        $table->string('password');
        $table->string('auth_ticket');
    });
    $this->scratch->create('rooms', fn (Blueprint $table) => $table->id());

    $inspection = HotelSchemaPreflight::inspect('preflight');

    expect($inspection->state)->toBe(HotelSchemaState::Unknown)
        ->and($inspection->state->safeToImport())->toBeFalse()
        ->and($inspection->unrecognised)->toContain('users', 'rooms')
        ->and($inspection->missingSignature)->toContain('items', 'emulator_settings');
});

test('an unrelated database is refused', function () {
    $this->scratch->create('wp_posts', fn (Blueprint $table) => $table->id());
    $this->scratch->create('wp_options', fn (Blueprint $table) => $table->id());

    $inspection = HotelSchemaPreflight::inspect('preflight');

    expect($inspection->state)->toBe(HotelSchemaState::Unknown)
        ->and($inspection->unrecognised)->toEqualCanonicalizing(['wp_posts', 'wp_options']);
});

test('the installed test database reads as an existing hotel', function () {
    expect(HotelSchemaPreflight::inspect()->state)->toBe(HotelSchemaState::Hotel);
});
