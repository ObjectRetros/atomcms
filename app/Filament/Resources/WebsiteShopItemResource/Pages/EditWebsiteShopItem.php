<?php

namespace App\Filament\Resources\WebsiteShopItemResource\Pages;

use App\Filament\Resources\WebsiteShopItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWebsiteShopItem extends EditRecord
{
    protected static string $resource = WebsiteShopItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
