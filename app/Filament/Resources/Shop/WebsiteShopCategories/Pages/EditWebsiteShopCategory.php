<?php

namespace App\Filament\Resources\Shop\WebsiteShopCategories\Pages;

use App\Filament\Resources\Shop\WebsiteShopCategories\WebsiteShopCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebsiteShopCategory extends EditRecord
{
    protected static string $resource = WebsiteShopCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
