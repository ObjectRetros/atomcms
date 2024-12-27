<?php

namespace App\Filament\Resources\Audit\UserAuditResource\Pages;

use App\Filament\Resources\Audit\UserAuditResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserAudits extends ListRecords
{
    protected static string $resource = UserAuditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
