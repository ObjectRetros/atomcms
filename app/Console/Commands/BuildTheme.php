<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BuildTheme extends Command
{
    protected $signature = 'build:theme';

    protected $description = 'Build a selected theme assets';

    public function handle()
    {
        $themes = $this->getAvailableThemes();

        if ($themes->isEmpty()) {
            $this->error('No themes found in resources/themes/');

            return Command::FAILURE;
        }

        $selectedTheme = $this->choice(
            'Which theme would you like to build?',
            $themes->toArray(),
            0,
        );

        $this->info("Building {$selectedTheme} theme...");

        $this->runBuildCommand($selectedTheme);

        return Command::SUCCESS;
    }

    private function getAvailableThemes(): \Illuminate\Support\Collection
    {
        $themesPath = resource_path('themes');

        if (! File::exists($themesPath)) {
            return collect();
        }

        return collect(File::directories($themesPath))
            ->map(fn ($path) => basename($path))
            ->sort();
    }

    private function runBuildCommand(string $theme): void
    {
        $command = escapeshellcmd("npm run build:{$theme}");
        $output = [];
        $returnCode = 0;

        exec($command, $output, $returnCode);

        foreach ($output as $line) {
            $this->line($line);
        }

        if ($returnCode === 0) {
            $this->info("Theme {$theme} built successfully!");
        } else {
            $this->error("Failed to build theme {$theme}");
        }
    }
}
