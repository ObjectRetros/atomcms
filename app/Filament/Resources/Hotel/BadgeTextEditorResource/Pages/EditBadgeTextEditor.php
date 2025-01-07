<?php

namespace App\Filament\Resources\Hotel\BadgeTextEditorResource\Pages;

use App\Filament\Resources\Hotel\BadgeTextEditorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBadgeTextEditor extends EditRecord
{
    protected static string $resource = BadgeTextEditorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
