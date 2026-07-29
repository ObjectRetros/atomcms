<?php

namespace App\Filament\Resources\Home\HomeItemResource\Pages;

use App\Filament\Resources\Home\HomeItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomeItems extends ListRecords
{
    protected static string $resource = HomeItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getTableReorderColumn(): ?string
    {
        return 'order';
    }
}
