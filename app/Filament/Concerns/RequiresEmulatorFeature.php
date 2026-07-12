<?php

namespace App\Filament\Concerns;

use App\Emulator\Data\Feature;
use App\Emulator\Emulator;

/**
 * Hides a Filament resource - navigation and access - when the configured
 * emulator driver does not support the feature it manages.
 */
trait RequiresEmulatorFeature
{
    abstract protected static function requiredEmulatorFeature(): Feature;

    public static function shouldRegisterNavigation(): bool
    {
        return Emulator::supports(static::requiredEmulatorFeature())
            && parent::shouldRegisterNavigation();
    }

    public static function canViewAny(): bool
    {
        return Emulator::supports(static::requiredEmulatorFeature())
            && parent::canViewAny();
    }
}
