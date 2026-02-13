<?php

namespace App\Filament\Resources\WebsiteShopPackageResource\Pages;

use App\Filament\Resources\WebsiteShopPackageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWebsiteShopPackage extends EditRecord
{
    protected static string $resource = WebsiteShopPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
