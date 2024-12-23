<?php

namespace App\Filament\Resources\Atom\FotoResource\Pages;

use App\Filament\Resources\Atom\FotoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFoto extends EditRecord
{
    protected static string $resource = FotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
