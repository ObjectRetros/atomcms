<?php

namespace App\Filament\Resources\Audit\UserBanResource\Pages;

use App\Filament\Resources\Audit\UserBanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserBan extends ListRecords
{
    protected static string $resource = UserBanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
