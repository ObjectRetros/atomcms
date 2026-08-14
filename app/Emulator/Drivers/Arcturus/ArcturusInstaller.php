<?php

namespace App\Emulator\Drivers\Arcturus;

use App\Emulator\Contracts\EmulatorInstaller;
use App\Enums\HotelSchemaState;
use App\Support\HotelSchemaPreflight;
use Generator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

class ArcturusInstaller implements EmulatorInstaller
{
    private const MARKER_TABLE = 'emulator_settings';

    /** A table only another emulator creates; importing over it destroys data. */
    private const FOREIGN_MARKER_TABLE = 'players';

    /**
     * Collations no portable dump should carry.
     *
     * The bundled dump was taken from MySQL 8, which is the only server that
     * has utf8mb4_0900_ai_ci: MySQL 5.7 and MariaDB before 11.5 reject it
     * outright with "Unknown collation", part-way through the import. Rewriting
     * to the collation the rest of the dump already uses makes it load on every
     * supported server, and leaves the schema less mixed than it was.
     *
     * @var array<string, string>
     */
    private const COLLATION_REPLACEMENTS = [
        'utf8mb4_0900_ai_ci' => 'utf8mb4_general_ci',
        'utf8mb4_0900_as_cs' => 'utf8mb4_bin',
    ];

    public function prepare(Command $command): bool
    {
        if ($command->option('skip-arcturus')) {
            return true;
        }

        if (Schema::hasTable(self::MARKER_TABLE)) {
            note('Arcturus tables already exist - skipping the base database and catalog import.');

            return true;
        }

        if (Schema::hasTable(self::FOREIGN_MARKER_TABLE)) {
            error('This database already belongs to another emulator. Point Atom at an empty database, or install with --emulator matching it.');

            return false;
        }

        $schema = HotelSchemaPreflight::inspect();

        if ($schema->state === HotelSchemaState::Hotel) {
            error('This database already belongs to an emulator that built it itself - importing the Arcturus base over it would destroy the hotel.');
            note(
                'Emulators that manage their own schema (Polaris through Flyway, Ada through Entity Framework)' . PHP_EOL
                . 'need no base import. Install with --skip-arcturus, or with the --emulator matching the one running here.',
            );

            return false;
        }

        if (! $this->ensureEmptyDatabase($command, $schema)) {
            return false;
        }

        $basePath = $command->option('sql') ?: database_path('arcturus/BaseDB-MS-3.5.5.sql.gz');

        if (! is_string($basePath) || ! $this->importDump($basePath, 'Arcturus base database (Morningstar 3.5.5)')) {
            return false;
        }

        if ($command->option('skip-catalog')) {
            return true;
        }

        $catalogPath = $command->option('catalog-sql') ?: database_path('arcturus/catalog.sql.gz');

        return is_string($catalogPath) && $this->importDump($catalogPath, 'catalog');
    }

    /**
     * The import assumes it owns the database: leftovers from an interrupted
     * run collide with the dump's CREATE TABLE statements, and a half-imported
     * database is the state this whole class exists to avoid. Offer to clear
     * it, but never without being told to.
     *
     * A database an emulator manages never reaches here - `prepare()` refuses
     * it outright - so what is left is Atom's own tables, or leftovers nobody
     * claims. Both are the operator's call, not Atom's.
     */
    private function ensureEmptyDatabase(Command $command, HotelSchemaPreflight $schema): bool
    {
        if ($schema->tables === []) {
            return true;
        }

        warning(sprintf('The database already contains %d table(s).', count($schema->tables)));

        if ($schema->unrecognised !== []) {
            note(
                'These were not created by Atom: ' . implode(', ', array_slice($schema->unrecognised, 0, 5))
                . (count($schema->unrecognised) > 5 ? sprintf(' and %d more', count($schema->unrecognised) - 5) : ''),
            );
        }

        $clear = (bool) $command->option('fresh') || (
            ! $command->option('no-interaction') && confirm(
                label: 'Clear the database before installing?',
                default: false,
                hint: 'Atom will drop every table in it. Take a backup first - this cannot be undone.',
            )
        );

        if (! $clear) {
            error('Point Atom at an empty database, or re-run with --fresh to clear this one.');

            return false;
        }

        $this->dropAllTables();
        info('Database cleared.');

        return true;
    }

    private function importDump(string $path, string $label): bool
    {
        if (! file_exists($path)) {
            error("SQL dump not found at: {$path}");

            return false;
        }

        $statements = spin(fn () => $this->countStatements($path), "Preparing {$label} import...");
        $progress = progress("Importing {$label}", $statements);
        $progress->start();

        try {
            foreach ($this->readStatements($path) as $statement) {
                DB::connection()->getPdo()->exec($statement);
                $progress->advance();
            }
        } catch (Throwable $exception) {
            $progress->finish();
            error('Import failed: ' . $exception->getMessage());

            // Everything in the database got there in the last few seconds, so
            // clearing it leaves a clean slate to retry on rather than a
            // half-built schema the operator has to dismantle by hand.
            $this->dropAllTables();
            warning('The partial import has been rolled back. Re-run: php artisan atom:install');

            return false;
        }

        $progress->finish();
        info(ucfirst($label) . ' imported.');

        return true;
    }

    private function dropAllTables(): void
    {
        Schema::disableForeignKeyConstraints();

        try {
            Schema::dropAllTables();
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    private function countStatements(string $path): int
    {
        $count = 0;

        foreach ($this->readStatements($path) as $statement) {
            $count++;
        }

        return $count;
    }

    /**
     * Stream the dump (plain or gzipped) and yield one SQL statement at a
     * time. The dump is written one statement per line except CREATE TABLE
     * blocks, so a trailing semicolon at end-of-line terminates a statement.
     *
     * @return Generator<int, string>
     */
    private function readStatements(string $path): Generator
    {
        $handle = gzopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException("Unable to open SQL dump: {$path}");
        }

        try {
            $buffer = '';

            while (($line = gzgets($handle)) !== false) {
                $trimmed = trim($line);

                if ($buffer === '' && ($trimmed === '' || str_starts_with($trimmed, '--'))) {
                    continue;
                }

                $buffer .= $line;

                if (str_ends_with(rtrim($line), ';')) {
                    yield $this->portable($buffer);
                    $buffer = '';
                }
            }

            if (trim($buffer) !== '') {
                yield $this->portable($buffer);
            }
        } finally {
            gzclose($handle);
        }
    }

    private function portable(string $statement): string
    {
        return str_replace(
            array_keys(self::COLLATION_REPLACEMENTS),
            array_values(self::COLLATION_REPLACEMENTS),
            $statement,
        );
    }
}
