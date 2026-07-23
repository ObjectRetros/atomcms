<?php

namespace App\Emulator;

use App\Emulator\Data\Feature;

/**
 * Thin static entry point over the EmulatorManager singleton, kept because
 * feature checks are sprinkled across Filament resources, middleware and
 * observers. Swap the EmulatorManager binding to fake it in tests.
 */
class Emulator
{
    public static function driver(): string
    {
        return app(EmulatorManager::class)->driver();
    }

    public static function supports(Feature $feature): bool
    {
        return app(EmulatorManager::class)->supports($feature);
    }
}
