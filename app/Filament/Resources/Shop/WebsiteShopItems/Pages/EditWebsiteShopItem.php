<?php

namespace App\Filament\Resources\Shop\WebsiteShopItems\Pages;

use App\Filament\Resources\Shop\WebsiteShopItems\WebsiteShopItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebsiteShopItem extends EditRecord
{
    protected static string $resource = WebsiteShopItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
