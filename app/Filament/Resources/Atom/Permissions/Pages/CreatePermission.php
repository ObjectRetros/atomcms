<?php

namespace App\Filament\Resources\Atom\Permissions\Pages;

use App\Filament\Resources\Atom\Permissions\PermissionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;
}
