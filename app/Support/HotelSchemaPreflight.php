<?php

namespace App\Support;

use App\Enums\HotelSchemaState;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;

/**
 * Inspects a database before Atom writes anything into it.
 *
 * The bundled Arcturus dump opens with `DROP TABLE IF EXISTS` for all 122 tables
 * it ships, so importing it into a database that already belongs to an emulator -
 * Polaris runs its own migrations and owns the whole schema up front - destroys a
 * live hotel. This is the gate that stops that, and it mirrors the equivalent
 * check Polaris runs before it migrates (`SchemaPreflight`).
 *
 * The hotel signature is deliberately compact rather than an exact dump match:
 * hotels add plugin tables and custom columns all the time, and those must still
 * be recognised as a hotel instead of being treated as a stranger's database.
 */
final class HotelSchemaPreflight
{
    /**
     * Characteristic tables per emulator family, and the columns that identify
     * them. A signature entry with no columns is matched on the table name
     * alone. Matching any one signature in full makes the database a hotel.
     *
     * @var array<string, array<string, list<string>>>
     */
    private const HOTEL_SIGNATURES = [
        'Arcturus' => [
            'users' => ['id', 'username', 'password', 'auth_ticket'],
            'rooms' => ['id', 'owner_id', 'model'],
            'items' => ['id', 'user_id', 'room_id', 'item_id'],
            'items_base' => ['id', 'interaction_type'],
            'catalog_pages' => ['id', 'page_layout'],
            'emulator_settings' => ['key', 'value'],
            'permissions' => ['id', 'rank_name'],
            'users_currency' => ['user_id', 'type', 'amount'],
        ],
        // Ada builds these through Entity Framework. Only the table names are
        // checked: its columns move between EF migrations, and a false negative
        // here costs a live hotel.
        'Ada' => [
            'players' => [],
            'player_data' => [],
            'player_avatar_data' => [],
            'player_website_data' => [],
            'roles' => [],
            'badges' => [],
        ],
    ];

    /**
     * Where emulators record their own migration history - Polaris through
     * Flyway, Ada through Entity Framework. Either one means an emulator
     * already manages this schema, whatever state the rest of it is in.
     *
     * @var list<string>
     */
    private const EMULATOR_MIGRATION_TABLES = ['flyway_schema_history', '__efmigrationshistory'];

    /** @var list<string> */
    private const ATOM_TABLE_PREFIXES = ['website_', 'user_home_', 'telescope_'];

    /**
     * Tables Atom creates itself, either through its own migrations or through
     * the framework and packages it ships.
     *
     * @var list<string>
     */
    private const ATOM_TABLES = [
        'activity_log',
        'cache',
        'cache_locks',
        'claimed_referral_logs',
        'failed_jobs',
        'home_categories',
        'home_items',
        'job_batches',
        'jobs',
        'migrations',
        'password_reset_tokens',
        'personal_access_tokens',
        'referrals',
        'sessions',
        'taggables',
        'tags',
        'user_referrals',
        'users_session_logs',
    ];

    /**
     * @param  list<string>  $tables  Every base table found in the database.
     * @param  list<string>  $unrecognised  Tables that belong to neither Atom nor a hotel.
     * @param  list<string>  $missingSignature  Hotel tables/columns that were looked for and not found.
     */
    private function __construct(
        public readonly HotelSchemaState $state,
        public readonly array $tables,
        public readonly array $unrecognised,
        public readonly array $missingSignature,
    ) {}

    public static function inspect(?string $connection = null): self
    {
        $schema = Schema::connection($connection);

        $tables = array_values(array_map(
            fn (array $table): string => strtolower((string) $table['name']),
            $schema->getTables(),
        ));

        if ($tables === []) {
            return new self(HotelSchemaState::Fresh, [], [], []);
        }

        foreach (self::EMULATOR_MIGRATION_TABLES as $table) {
            if (in_array($table, $tables, true)) {
                return new self(HotelSchemaState::Hotel, $tables, [], []);
            }
        }

        $missing = self::missingSignature($schema, $tables);

        if ($missing === []) {
            return new self(HotelSchemaState::Hotel, $tables, [], []);
        }

        $unrecognised = array_values(array_filter(
            $tables,
            fn (string $table): bool => ! self::isAtomTable($table),
        ));

        return new self(
            $unrecognised === [] ? HotelSchemaState::AtomOnly : HotelSchemaState::Unknown,
            $tables,
            $unrecognised,
            $missing,
        );
    }

    /**
     * What the database is missing from the signature it comes closest to. Empty
     * when it matches one in full, in which case it is a hotel.
     *
     * @param  list<string>  $tables
     *
     * @return list<string>
     */
    private static function missingSignature(Builder $schema, array $tables): array
    {
        $closest = [];
        $closestFound = -1;

        foreach (self::HOTEL_SIGNATURES as $signature) {
            $missing = self::missingFrom($schema, $tables, $signature);

            if ($missing === []) {
                return [];
            }

            // Whichever family the database has the most tables of is the one
            // worth reporting against - a half-built Arcturus is missing fewer
            // Ada tables than Arcturus ones purely because Ada's list is shorter.
            $found = count(array_intersect(array_keys($signature), $tables));

            if ($found > $closestFound) {
                $closest = $missing;
                $closestFound = $found;
            }
        }

        return $closest;
    }

    /**
     * @param  list<string>  $tables
     * @param  array<string, list<string>>  $signature
     *
     * @return list<string>
     */
    private static function missingFrom(Builder $schema, array $tables, array $signature): array
    {
        $missing = [];

        foreach ($signature as $table => $columns) {
            if (! in_array($table, $tables, true)) {
                $missing[] = $table;

                continue;
            }

            foreach ($columns as $column) {
                if (! $schema->hasColumn($table, $column)) {
                    $missing[] = "{$table}.{$column}";
                }
            }
        }

        return $missing;
    }

    private static function isAtomTable(string $table): bool
    {
        if (in_array($table, self::ATOM_TABLES, true)) {
            return true;
        }

        foreach (self::ATOM_TABLE_PREFIXES as $prefix) {
            if (str_starts_with($table, $prefix)) {
                return true;
            }
        }

        // Left behind by the 2014_10_12_100000 migration's collision rename.
        return (bool) preg_match('/^password_resets(_\d+)?$/', $table);
    }
}
