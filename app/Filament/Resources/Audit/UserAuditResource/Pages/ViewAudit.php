<?php

namespace App\Filament\Resources\Audit\UserAuditResource\Pages;

use App\Filament\Resources\Audit\UserAuditResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAudit extends ViewRecord
{
    protected static string $resource = UserAuditResource::class;
}
