<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class BuildTheme extends Command
{
    protected $signature = 'build:theme';

    protected $description = 'Build a selected theme assets';

    public function handle(): int
    {
        $themes = $this->getAvailableThemes();

        if ($themes->isEmpty()) {
            $this->error('No themes found in resources/themes/');

            return Command::FAILURE;
        }

        $selection = $this->choice(
            'Which theme would you like to build?',
            $themes->toArray(),
            0,
        );

        if (! is_string($selection)) {
            $this->error('The selected theme is invalid.');

            return Command::FAILURE;
        }

        $this->info("Building {$selection} theme...");

        return $this->runBuildCommand($selection);
    }

    /** @return Collection<int, string> */
    private function getAvailableThemes(): Collection
    {
        $themesPath = resource_path('themes');

        if (! File::exists($themesPath)) {
            return collect();
        }

        return collect(File::directories($themesPath))
            ->map(fn ($path) => basename($path))
            ->sort();
    }

    private function runBuildCommand(string $theme): int
    {
        $process = new Process(['npm', 'run', "build:{$theme}"], base_path());
        $process->setTimeout(300);
        $process->run(fn (string $type, string $buffer) => $this->output->write($buffer));

        if ($process->isSuccessful()) {
            $this->info("Theme {$theme} built successfully!");

            return Command::SUCCESS;
        }

        $this->error("Failed to build theme {$theme}");

        return Command::FAILURE;
    }
}
