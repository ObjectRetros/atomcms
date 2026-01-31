<?php

namespace App\Services\Emulator\Drivers;

use App\Services\Emulator\EmulatorInterface;
use Illuminate\Support\Facades\Schema;

class ArcturusDriver implements EmulatorInterface
{
    public function getCurrencyBalance(\App\Models\User $user, string $type): int
    {
        if (! $user->relationLoaded('currencies')) {
            $user->load('currencies');
        }

        $currencyType = match ($type) {
            'duckets' => 0,
            'diamonds' => 5,
            'points' => 101,
            default => 0,
        };

        return $user->currencies->where('type', $currencyType)->first()->amount ?? 0;
    }

    public function getPermissionColumns(): array
    {
        // Ensure the permissions table exists to avoid errors during migrations or setup
        if (! Schema::hasTable('permissions')) {
            return [];
        }

        $columns = Schema::getColumns('permissions');

        return collect($columns)->filter(function (array $column) {
            $columnName = $column['name'] ?? null;

            if (! $columnName) {
                return false;
            }

            return str_starts_with($columnName, 'cmd')
                || str_starts_with($columnName, 'acc')
                || str_ends_with($columnName, 'cmd');
        })->values()->toArray();
    }
}
