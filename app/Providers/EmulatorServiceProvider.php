<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Binds the emulator contracts to the implementations of the configured driver,
 * so the rest of the CMS depends only on the contracts.
 */
class EmulatorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $driver = config('emulator.driver');

        /** @var array<class-string, class-string> $bindings */
        $bindings = config("emulator.drivers.{$driver}.bindings", []);

        foreach ($bindings as $contract => $implementation) {
            $this->app->bind($contract, $implementation);
        }
    }
}
