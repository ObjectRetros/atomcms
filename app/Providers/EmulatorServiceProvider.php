<?php

namespace App\Providers;

use App\Emulator\EmulatorManager;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

/**
 * Binds the emulator contracts to the implementations of the configured driver,
 * so the rest of the CMS depends only on the contracts.
 */
class EmulatorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EmulatorManager::class);

        $driver = (string) config('emulator.driver');
        $drivers = config('emulator.drivers', []);

        if (! is_array($drivers) || ! array_key_exists($driver, $drivers)) {
            throw new InvalidArgumentException("Unknown emulator driver [{$driver}]");
        }

        /** @var array<class-string, class-string> $bindings */
        $bindings = config("emulator.drivers.{$driver}.bindings", []);

        foreach ($bindings as $contract => $implementation) {
            // The repositories are stateless, so one instance can serve the
            // whole process.
            $this->app->singleton($contract, $implementation);
        }
    }
}
