<?php

namespace App\Filament\Resources\Shop\WebsiteShopPackages\Pages;

use App\Filament\Resources\Shop\WebsiteShopPackages\WebsiteShopPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWebsiteShopPackages extends ListRecords
{
    protected static string $resource = WebsiteShopPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
