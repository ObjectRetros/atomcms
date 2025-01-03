<?php

namespace App\Filament\Resources\Atom\CameraWebResource\Pages;

use App\Filament\Resources\Atom\CameraWebResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCameraWeb extends ListRecords
{
    protected static string $resource = CameraWebResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
