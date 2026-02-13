<?php

namespace App\Filament\Resources\WebsiteShopCategoryResource\Pages;

use App\Filament\Resources\WebsiteShopCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWebsiteShopCategory extends EditRecord
{
    protected static string $resource = WebsiteShopCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
