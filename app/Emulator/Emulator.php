<?php

namespace App\Emulator;

use App\Emulator\Data\Feature;

/**
 * Answers questions about the configured emulator driver.
 */
class Emulator
{
    public static function driver(): string
    {
        return (string) config('emulator.driver');
    }

    public static function supports(Feature $feature): bool
    {
        $features = config('emulator.drivers.' . self::driver() . '.features', []);

        return in_array($feature, $features, true);
    }
}
