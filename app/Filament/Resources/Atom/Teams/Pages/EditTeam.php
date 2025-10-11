<?php

namespace App\Filament\Resources\Atom\Teams\Pages;

use App\Filament\Resources\Atom\Teams\TeamResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTeam extends EditRecord
{
    protected static string $resource = TeamResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
