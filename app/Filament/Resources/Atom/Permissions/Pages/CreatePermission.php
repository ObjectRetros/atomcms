<?php

namespace App\Filament\Resources\Atom\Permissions\Pages;

use App\Filament\Resources\Atom\Permissions\PermissionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Schema;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['log_commands'] ??= '1';
        $data['prefix'] ??= '';
        $data['prefix_color'] ??= '';

        foreach (['auto_credits_amount', 'auto_pixels_amount', 'auto_gotw_amount', 'auto_points_amount'] as $currencyColumn) {
            $data[$currencyColumn] ??= 0;
        }

        foreach (Schema::getColumns('permissions') as $column) {
            $columnName = $column['name'] ?? null;

            if (! $columnName) {
                continue;
            }

            if (
                str_starts_with($columnName, 'cmd')
                || str_starts_with($columnName, 'acc')
                || str_ends_with($columnName, 'cmd')
            ) {
                $data[$columnName] ??= '0';
            }
        }

        return $data;
    }
}
