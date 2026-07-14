<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Schema;
use PDO;
use PDOException;
use Throwable;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\password;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

class AtomInstallCommand extends Command
{
    protected $signature = 'atom:install
        {--sql= : Path to a custom Arcturus base SQL dump (.sql or .sql.gz)}
        {--catalog-sql= : Path to a custom catalog SQL dump (.sql or .sql.gz)}
        {--skip-arcturus : Skip importing the bundled Arcturus base database and catalog}
        {--skip-catalog : Skip importing the bundled catalog on top of the base database}
        {--theme= : Theme to activate and build (atom or dusk)}
        {--skip-build : Skip building theme assets}';

    protected $description = 'One-command installer: configures the database, imports the Arcturus base SQL, links storage, runs migrations with seeders and builds your theme';

    private const THEMES = ['atom', 'dusk'];

    private const ARCTURUS_MARKER_TABLE = 'emulator_settings';

    public function handle(): int
    {
        intro('Atom CMS installer');

        $this->ensureEnvFile();
        $this->ensureAppKey();

        if (! $this->configureDatabase()) {
            return self::FAILURE;
        }

        if (! $this->option('skip-arcturus') && ! $this->importArcturusDatabase()) {
            return self::FAILURE;
        }

        $this->linkStorage();

        spin(function () {
            $this->callSilent('migrate', ['--force' => true]);
            $this->callSilent('db:seed', ['--force' => true]);
        }, 'Running migrations and seeders...');
        info('Migrations and seeders completed.');

        $this->setUpTheme();

        outro('Atom CMS is installed! Serve the app and visit /installation to finish configuring your hotel.');

        return self::SUCCESS;
    }

    private function ensureEnvFile(): void
    {
        if (file_exists(base_path('.env'))) {
            return;
        }

        copy(base_path('.env.example'), base_path('.env'));
        info('Created .env from .env.example.');
    }

    private function ensureAppKey(): void
    {
        if (config('app.key')) {
            return;
        }

        $this->callSilent('key:generate', ['--force' => true]);
        info('Generated application key.');
    }

    private function configureDatabase(): bool
    {
        $connection = config('database.default');
        $current = config("database.connections.{$connection}");

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            if ($this->input->isInteractive()) {
                $current['host'] = text('Database host', default: $current['host'] ?? '127.0.0.1', hint: "Use 'mariadb' for Docker, '127.0.0.1' for local");
                $current['port'] = text('Database port', default: (string) ($current['port'] ?? '3306'));
                $current['database'] = text('Database name', default: $current['database'] ?? 'atomcms', hint: 'Atom and Arcturus share this database');
                $current['username'] = text('Database username', default: $current['username'] ?? 'root');
                $current['password'] = password('Database password', hint: 'Leave empty to keep the value currently in .env') ?: $current['password'];
            }

            $this->applyDatabaseConfig($connection, $current);

            try {
                spin(fn () => DB::connection()->getPdo(), 'Testing database connection...');
                info("Connected to database '{$current['database']}' on {$current['host']}.");
                $this->writeDatabaseEnv($current);

                return true;
            } catch (Throwable $exception) {
                if ($this->isUnknownDatabaseError($exception) && $this->createDatabase($current)) {
                    $this->writeDatabaseEnv($current);

                    return true;
                }

                error('Could not connect: ' . $exception->getMessage());

                if (! $this->input->isInteractive()) {
                    return false;
                }
            }
        }

        error('Giving up after 3 failed connection attempts. Fix your database credentials and re-run: php artisan atom:install');

        return false;
    }

    private function isUnknownDatabaseError(Throwable $exception): bool
    {
        while ($exception !== null && ! $exception instanceof PDOException) {
            $exception = $exception->getPrevious();
        }

        return $exception !== null && str_contains($exception->getMessage(), 'Unknown database');
    }

    private function createDatabase(array $config): bool
    {
        $create = ! $this->input->isInteractive()
            || confirm("Database '{$config['database']}' does not exist. Create it?", default: true);

        if (! $create) {
            return false;
        }

        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;port=%s', $config['host'], $config['port']),
                $config['username'],
                $config['password'],
            );
            $pdo->exec(sprintf(
                'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
                str_replace('`', '', $config['database']),
            ));

            $connection = config('database.default');
            $this->applyDatabaseConfig($connection, $config);
            DB::connection()->getPdo();

            info("Created database '{$config['database']}'.");

            return true;
        } catch (Throwable $exception) {
            error('Could not create the database: ' . $exception->getMessage());

            return false;
        }
    }

    private function applyDatabaseConfig(string $connection, array $config): void
    {
        config([
            "database.connections.{$connection}.host" => $config['host'],
            "database.connections.{$connection}.port" => $config['port'],
            "database.connections.{$connection}.database" => $config['database'],
            "database.connections.{$connection}.username" => $config['username'],
            "database.connections.{$connection}.password" => $config['password'],
        ]);

        DB::purge($connection);
    }

    private function writeDatabaseEnv(array $config): void
    {
        $path = base_path('.env');
        $contents = file_get_contents($path);

        foreach ([
            'DB_HOST' => $config['host'],
            'DB_PORT' => $config['port'],
            'DB_DATABASE' => $config['database'],
            'DB_USERNAME' => $config['username'],
            'DB_PASSWORD' => $config['password'],
        ] as $key => $value) {
            $line = str_contains((string) $value, ' ') ? sprintf('%s="%s"', $key, $value) : sprintf('%s=%s', $key, $value);

            $contents = preg_match("/^{$key}=.*$/m", $contents)
                ? preg_replace("/^{$key}=.*$/m", $line, $contents)
                : $contents . PHP_EOL . $line;
        }

        file_put_contents($path, $contents);
    }

    private function importArcturusDatabase(): bool
    {
        if (Schema::hasTable(self::ARCTURUS_MARKER_TABLE)) {
            note('Arcturus tables already exist - skipping the base database and catalog import.');

            return true;
        }

        $basePath = $this->option('sql') ?: database_path('arcturus/BaseDB-MS-3.5.5.sql.gz');

        if (! $this->importDump($basePath, 'Arcturus base database (Morningstar 3.5.5)')) {
            return false;
        }

        if ($this->option('skip-catalog')) {
            return true;
        }

        $catalogPath = $this->option('catalog-sql') ?: database_path('arcturus/catalog.sql.gz');

        return $this->importDump($catalogPath, 'catalog');
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
                DB::unprepared($statement);
                $progress->advance();
            }
        } catch (Throwable $exception) {
            $progress->finish();
            error('Import failed: ' . $exception->getMessage());
            warning('The database may be partially imported. Drop and recreate it, then re-run: php artisan atom:install');

            return false;
        }

        $progress->finish();
        info(ucfirst($label) . ' imported.');

        return true;
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
     * @return \Generator<int, string>
     */
    private function readStatements(string $path): \Generator
    {
        $handle = gzopen($path, 'rb');

        if ($handle === false) {
            throw new \RuntimeException("Unable to open SQL dump: {$path}");
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
                    yield $buffer;
                    $buffer = '';
                }
            }

            if (trim($buffer) !== '') {
                yield $buffer;
            }
        } finally {
            gzclose($handle);
        }
    }

    private function setUpTheme(): void
    {
        $seeded = DB::table('website_settings')->where('key', 'theme')->value('value');

        $theme = $this->option('theme');

        if ($theme !== null && ! in_array($theme, self::THEMES, true)) {
            warning("Unknown theme '{$theme}' - expected one of: " . implode(', ', self::THEMES) . '. Keeping the current theme.');
            $theme = null;
        }

        if ($theme === null && $this->input->isInteractive()) {
            $theme = select(
                'Which theme do you want to use?',
                self::THEMES,
                default: in_array($seeded, self::THEMES, true) ? $seeded : 'atom',
                hint: 'You can switch themes later in the housekeeping.',
            );
        }

        $theme ??= in_array($seeded, self::THEMES, true) ? $seeded : 'atom';

        if ($theme !== $seeded) {
            DB::table('website_settings')->where('key', 'theme')->update(['value' => $theme]);
        }

        info("Active theme set to '{$theme}'.");

        if ($this->option('skip-build')) {
            note('Skipping the asset build - run this yourself: npm run build:' . $theme);

            return;
        }

        $result = spin(
            fn () => Process::timeout(600)->path(base_path())->run(['npm', 'run', "build:{$theme}"]),
            "Building {$theme} theme assets (npm run build:{$theme})...",
        );

        if ($result->failed()) {
            warning("The asset build failed - fix the error below and re-run: npm run build:{$theme}");
            $this->line(trim($result->errorOutput() ?: $result->output()));

            return;
        }

        info(ucfirst($theme) . ' theme assets built.');
    }

    private function linkStorage(): void
    {
        if (file_exists(public_path('storage'))) {
            note('Storage link already exists - skipping.');

            return;
        }

        $this->callSilent('storage:link');
        info('Linked public storage.');
    }
}
