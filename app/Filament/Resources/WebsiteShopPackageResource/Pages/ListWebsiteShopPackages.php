<?php

namespace App\Filament\Resources\WebsiteShopPackageResource\Pages;

use App\Filament\Resources\WebsiteShopPackageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWebsiteShopPackages extends ListRecords
{
    protected static string $resource = WebsiteShopPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
