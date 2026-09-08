<?php

namespace App\Console\Commands;

use App\Emulator\EmulatorManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use PDO;
use PDOException;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Throwable;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

/** @phpstan-type DatabaseConfig array{host: string, port: string, database: string, username: string, password: string} */
class AtomInstallCommand extends Command
{
    protected $signature = 'atom:install
        {--emulator= : Registered emulator driver key to install against}
        {--sql= : Path to a custom Arcturus base SQL dump (.sql or .sql.gz)}
        {--catalog-sql= : Path to a custom catalog SQL dump (.sql or .sql.gz)}
        {--fresh : Drop every table in the target database before importing (destroys existing data)}
        {--skip-arcturus : Skip importing the bundled Arcturus base database and catalog}
        {--skip-catalog : Skip importing the bundled catalog on top of the base database}
        {--theme= : Theme to activate and build (atom or dusk)}
        {--skip-build : Skip building theme assets}';

    protected $description = 'One-command installer for a supported emulator';

    private const THEMES = ['atom', 'dusk'];

    public function handle(EmulatorManager $emulators): int
    {
        $this->attachConsoleInput();

        intro('Atom CMS installer');

        $this->ensureEnvFile();
        $this->ensureAppKey();

        if (! $this->configureEmulator($emulators)) {
            return self::FAILURE;
        }

        if (! $this->configureDatabase()) {
            return self::FAILURE;
        }

        $activeDriver = $emulators->active();

        if (! $activeDriver->installer()->prepare($this)) {
            return self::FAILURE;
        }

        $this->linkStorage();

        // The driver was selected after boot, so its migration paths are not
        // the ones EmulatorServiceProvider registered. Pass both explicitly;
        // migrate still orders the combined set by filename.
        spin(function () use ($activeDriver) {
            $this->callSilent('migrate', [
                '--force' => true,
                '--realpath' => true,
                '--path' => [database_path('migrations'), ...$activeDriver->migrationPaths()],
            ]);
            $this->callSilent('db:seed', ['--force' => true]);
        }, 'Running migrations and seeders...');
        info('Migrations and seeders completed.');

        $this->setUpTheme();

        outro('Atom CMS is installed! Serve the app and visit /installation to finish configuring your hotel.');

        return self::SUCCESS;
    }

    private function configureEmulator(EmulatorManager $emulators): bool
    {
        $driver = $this->option('emulator');

        if ($driver === null && $this->input->isInteractive()) {
            $driver = select(
                label: 'Which emulator does this hotel run?',
                options: $emulators->choices(),
                default: $emulators->active()->key(),
                hint: 'A hotel runs one emulator and does not switch later.',
            );
        }

        $driver = is_string($driver) ? strtolower($driver) : $emulators->active()->key();

        try {
            $activeDriver = $emulators->select($driver);
        } catch (\InvalidArgumentException $exception) {
            error($exception->getMessage());

            return false;
        }

        $this->writeEnvValue('EMULATOR_DRIVER', $driver);
        info('Using the ' . $activeDriver->label() . ' emulator driver.');

        return true;
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

        if (! is_string($connection)) {
            error('The default database connection is not configured.');

            return false;
        }

        $configured = config("database.connections.{$connection}");

        if (! is_array($configured)) {
            error("The '{$connection}' database connection is not configured.");

            return false;
        }

        $current = $this->databaseConfig($configured);

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            if ($this->input->isInteractive()) {
                $defaultHost = PHP_OS_FAMILY === 'Windows' && $current['host'] === 'mariadb'
                    ? '127.0.0.1'
                    : $current['host'];

                $this->line("Use 'mariadb' for Docker, or '127.0.0.1' for a database running on this computer.");
                $current['host'] = (string) $this->ask('Database host', $defaultHost);
                $current['port'] = (string) $this->ask('Database port', $current['port']);
                $emulator = app(EmulatorManager::class)->active()->label();
                $current['database'] = (string) $this->ask("Database name (shared by Atom and {$emulator})", $current['database']);
                $current['username'] = (string) $this->ask('Database username', $current['username']);

                $password = $this->secret('Database password (leave empty to keep the value from .env)');

                if (is_string($password) && $password !== '') {
                    $current['password'] = $password;
                }
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

    /**
     * @param  array<string, mixed>  $configured
     *
     * @return DatabaseConfig
     */
    private function databaseConfig(array $configured): array
    {
        return [
            'host' => $this->databaseConfigValue($configured, 'host', '127.0.0.1'),
            'port' => $this->databaseConfigValue($configured, 'port', '3306'),
            'database' => $this->databaseConfigValue($configured, 'database', 'atomcms'),
            'username' => $this->databaseConfigValue($configured, 'username', 'root'),
            'password' => $this->databaseConfigValue($configured, 'password', ''),
        ];
    }

    private function isUnknownDatabaseError(Throwable $exception): bool
    {
        while ($exception !== null && ! $exception instanceof PDOException) {
            $exception = $exception->getPrevious();
        }

        return $exception !== null && str_contains($exception->getMessage(), 'Unknown database');
    }

    /** @param DatabaseConfig $config */
    private function createDatabase(array $config): bool
    {
        $create = ! $this->input->isInteractive()
            || $this->confirm("Database '{$config['database']}' does not exist. Create it?", true);

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
                str_replace('`', '``', $config['database']),
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

    /** @param DatabaseConfig $config */
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

    /** @param DatabaseConfig $config */
    private function writeDatabaseEnv(array $config): void
    {
        $path = base_path('.env');
        $contents = file_get_contents($path);

        if (! is_string($contents)) {
            throw new \RuntimeException("Unable to read environment file: {$path}");
        }

        foreach ([
            'DB_HOST' => $config['host'],
            'DB_PORT' => $config['port'],
            'DB_DATABASE' => $config['database'],
            'DB_USERNAME' => $config['username'],
            'DB_PASSWORD' => $config['password'],
        ] as $key => $value) {
            $contents = $this->replaceEnvValue($contents, $key, $value, $path);
        }

        if (file_put_contents($path, $contents) === false) {
            throw new \RuntimeException("Unable to write environment file: {$path}");
        }
    }

    private function writeEnvValue(string $key, string $value): void
    {
        $path = base_path('.env');
        $contents = file_get_contents($path);

        if (! is_string($contents)) {
            throw new \RuntimeException("Unable to read environment file: {$path}");
        }

        $contents = $this->replaceEnvValue($contents, $key, $value, $path);

        if (file_put_contents($path, $contents) === false) {
            throw new \RuntimeException("Unable to write environment file: {$path}");
        }
    }

    private function replaceEnvValue(string $contents, string $key, string $value, string $path): string
    {
        $escaped = strtr($value, [
            '\\' => '\\\\',
            '"' => '\\"',
            '$' => '\\$',
            "\r" => '\\r',
            "\n" => '\\n',
        ]);
        $line = sprintf('%s="%s"', $key, $escaped);

        if (preg_match("/^{$key}=.*$/m", $contents) !== 1) {
            return $contents . PHP_EOL . $line;
        }

        $updated = preg_replace_callback("/^{$key}=.*$/m", fn (): string => $line, $contents);

        if (! is_string($updated)) {
            throw new \RuntimeException("Unable to update {$key} in environment file: {$path}");
        }

        return $updated;
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
            $selectedTheme = $this->choice(
                'Which theme do you want to use?',
                self::THEMES,
                in_array($seeded, self::THEMES, true) ? $seeded : 'atom',
            );

            if (is_string($selectedTheme)) {
                $theme = $selectedTheme;
            }
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

    /**
     * Composer cannot allocate a TTY for child processes on Windows. Attach
     * Symfony's questions directly to the active console so `composer setup`
     * remains interactive instead of silently accepting Docker defaults.
     */
    private function attachConsoleInput(): void
    {
        if (
            ! $this->input->isInteractive()
            || ! ($this->input instanceof StreamableInputInterface)
            || app()->environment('testing')
            || (function_exists('stream_isatty') && @stream_isatty(STDIN))
        ) {
            return;
        }

        $console = PHP_OS_FAMILY === 'Windows' ? 'CONIN$' : '/dev/tty';
        $stream = @fopen($console, 'r');

        if ($stream === false) {
            $this->input->setInteractive(false);
            warning('No interactive console is available. Using the database values from .env.');

            return;
        }

        $this->input->setStream($stream);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function databaseConfigValue(array $config, string $key, string $default): string
    {
        $value = $config[$key] ?? null;

        return is_string($value) || is_int($value) ? (string) $value : $default;
    }
}
