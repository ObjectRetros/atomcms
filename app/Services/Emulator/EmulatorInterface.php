<?php

namespace App\Services\Emulator;

use App\Models\User;

interface EmulatorInterface
{
    public function getCurrencyBalance(User $user, string $type): int;

    /**
     * Get the list of columns that represent permissions in the database.
     */
    public function getPermissionColumns(): array;
}
