<?php

namespace App\Filament\Resources\Shop\WebsiteShopItems\Pages;

use App\Filament\Resources\Shop\WebsiteShopItems\WebsiteShopItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWebsiteShopItems extends ListRecords
{
    protected static string $resource = WebsiteShopItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
