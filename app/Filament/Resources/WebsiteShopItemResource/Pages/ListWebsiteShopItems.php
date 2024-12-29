<?php

namespace App\Filament\Resources\WebsiteShopItemResource\Pages;

use App\Filament\Resources\WebsiteShopItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWebsiteShopItems extends ListRecords
{
    protected static string $resource = WebsiteShopItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
