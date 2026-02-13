<?php

namespace App\Filament\Resources\WebsiteShopPackageResource\Pages;

use App\Filament\Resources\WebsiteShopPackageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWebsiteShopPackage extends CreateRecord
{
    protected static string $resource = WebsiteShopPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
