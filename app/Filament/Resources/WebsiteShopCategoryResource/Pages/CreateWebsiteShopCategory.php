<?php

namespace App\Filament\Resources\WebsiteShopCategoryResource\Pages;

use App\Filament\Resources\WebsiteShopCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWebsiteShopCategory extends CreateRecord
{
    protected static string $resource = WebsiteShopCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
