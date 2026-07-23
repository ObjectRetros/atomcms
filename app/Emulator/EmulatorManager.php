<?php

namespace App\Emulator;

use App\Emulator\Data\Feature;

/**
 * Answers questions about the configured emulator driver. Bound as a
 * singleton; reads config at call time so tests can switch drivers.
 */
class EmulatorManager
{
    public function driver(): string
    {
        return (string) config('emulator.driver');
    }

    public function supports(Feature $feature): bool
    {
        $features = config('emulator.drivers.' . $this->driver() . '.features', []);

        return is_array($features) && in_array($feature, $features, true);
    }
}
