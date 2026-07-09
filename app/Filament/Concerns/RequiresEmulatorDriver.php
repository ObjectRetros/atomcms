<?php

namespace App\Filament\Concerns;

use App\Emulator\Emulator;

/**
 * Hides a Filament resource - navigation and access - unless the configured
 * emulator driver matches. For resources bound to one emulator's schema; when
 * several drivers offer the same capability, each ships its own resource
 * gated to its driver.
 */
trait RequiresEmulatorDriver
{
    abstract protected static function requiredEmulatorDriver(): string;

    public static function shouldRegisterNavigation(): bool
    {
        return Emulator::driver() === static::requiredEmulatorDriver()
            && parent::shouldRegisterNavigation();
    }

    public static function canViewAny(): bool
    {
        return Emulator::driver() === static::requiredEmulatorDriver()
            && parent::canViewAny();
    }
}
