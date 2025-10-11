<?php

namespace App\Filament\Resources\Atom\NavigationResource\Pages;

use App\Filament\Resources\Atom\NavigationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNavigation extends EditRecord
{
    protected static string $resource = NavigationResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
