<?php

namespace App\Filament\Resources\Atom\WriteableBoxResource\Pages;

use App\Filament\Resources\Atom\WriteableBoxResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageWriteableBoxes extends ManageRecords
{
    protected static string $resource = WriteableBoxResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
