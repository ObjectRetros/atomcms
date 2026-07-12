<?php

namespace App\Filament\Resources\Shop\WebsiteShopPackages\Pages;

use App\Filament\Resources\Shop\WebsiteShopPackages\WebsiteShopPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebsiteShopPackage extends EditRecord
{
    protected static string $resource = WebsiteShopPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
