<?php

namespace App\Services\Emulator\Drivers;

use App\Services\Emulator\EmulatorInterface;
use Illuminate\Support\Facades\Schema;

class SadieDriver implements EmulatorInterface
{
    public function getCurrencyBalance(\App\Models\User $user, string $type): int
    {
        // Sadie Emulator might store currencies in the 'users' table columns
        // or a different table entirely.
        // Implement the specific data retrieval logic here.

        // Example: if Sadie uses columns on the users table:
        // return match ($type) {
        //     'duckets' => $user->duckets ?? 0,
        //     'diamonds' => $user->diamonds ?? 0,
        //     default => 0,
        // };

        return 0;
    }

    public function getPermissionColumns(): array
    {
        if (! Schema::hasTable('permissions')) {
            return [];
        }

        $columns = Schema::getColumns('permissions');

        return collect($columns)->filter(function (array $column) {
            $columnName = $column['name'] ?? null;

            if (! $columnName) {
                return false;
            }

            // Adjust this filter logic to match Sadie's permission column naming convention
            return str_starts_with($columnName, 'cmd')
                || str_starts_with($columnName, 'acc')
                || str_ends_with($columnName, 'cmd');
        })->values()->toArray();
    }
}
