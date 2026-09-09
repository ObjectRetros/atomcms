<?php

namespace App\Console\Commands;

use App\Services\InstallationService;
use App\Services\OperatingMode;
use App\Support\EnvironmentFile;
use Dotenv\Dotenv;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Throwable;

class AtomModeCommand extends Command
{
    protected $signature = 'atom:mode {mode? : full or headless}
        {--frontend-url= : Origin of the independent frontend}
        {--session-domain= : Explicit cookie domain for sibling hosts}
        {--status : Show effective mode and asset readiness}
        {--environment-managed : Print configuration for the deployment environment without editing .env}';

    protected $description = 'Configure the public presentation without changing hotel data';

    public function handle(OperatingMode $modes, EnvironmentFile $environment, InstallationService $installation): int
    {
        if ($this->option('status') || $this->argument('mode') === null) {
            $this->table(['Setting', 'Value'], [
                ['Mode', config('atom.mode')],
                ['Backend', config('app.url')],
                ['Frontend', config('atom.frontend_url') ?: '(built-in theme)'],
                ['Installation', $installation->isComplete() ? 'complete' : 'incomplete'],
                ['Housekeeping assets', is_file(public_path('build-housekeeping/manifest.json')) ? 'ready' : 'missing: npm run build:housekeeping'],
            ]);

            return self::SUCCESS;
        }

        try {
            $values = $modes->environment((string) $this->argument('mode'), $this->option('frontend-url') ?: config('atom.frontend_url'), $this->option('session-domain') ?? config('session.domain'));
            if (! $installation->isComplete()) {
                $this->error('Complete installation with atom:setup --complete before switching modes.');

                return self::FAILURE;
            }
            if ($this->option('environment-managed')) {
                foreach ($values as $key => $value) {
                    $this->line($environment->replace('', $key, $value));
                }
                $this->info('Apply these values in your deployment, rebuild config/route caches, and restart workers.');

                return self::SUCCESS;
            }
            $fileValues = Dotenv::parse((string) file_get_contents(base_path('.env')));
            foreach ($values as $key => $value) {
                $external = getenv($key);
                if ($external !== false && $external !== $value && $external !== ($fileValues[$key] ?? null)) {
                    $this->error("{$key} is externally managed. Use --environment-managed and update the deployment environment.");

                    return self::FAILURE;
                }
            }
            $path = base_path('.env');
            $previous = file_get_contents($path);
            if (! is_string($previous)) {
                throw new \RuntimeException('Create the environment file before switching modes.');
            }
            $environment->write($path, $values);
            try {
                foreach (['config:cache', 'route:cache'] as $command) {
                    $result = Process::path(base_path())->env($values)->run([PHP_BINARY, 'artisan', $command]);
                    $this->output->write($result->output());
                    if ($result->failed()) {
                        throw new \RuntimeException("{$command} failed.");
                    }
                }
            } catch (Throwable $exception) {
                file_put_contents($path, $previous, LOCK_EX);
                $this->callSilent('config:clear');
                $this->callSilent('route:clear');
                throw $exception;
            }
            $this->info('Configuration saved. Restart queue workers and persistent application servers through your deployment process.');
            if ($values['ATOM_MODE'] === 'full' && ! is_file(public_path('build/manifest.json'))) {
                $this->warn('Public assets are missing. Run npm run build:' . (setting('theme') ?: 'atom'));
            }
            if (! is_file(public_path('build-housekeeping/manifest.json'))) {
                $this->warn('Housekeeping assets are missing. Run npm run build:housekeeping.');
            }

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
