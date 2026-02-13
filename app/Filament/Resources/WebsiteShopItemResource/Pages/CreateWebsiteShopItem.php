<?php

namespace App\Filament\Resources\WebsiteShopItemResource\Pages;

use App\Filament\Resources\WebsiteShopItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWebsiteShopItem extends CreateRecord
{
    protected static string $resource = WebsiteShopItemResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
