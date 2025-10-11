<?php

namespace App\Filament\Resources\Atom\CameraWebs\Pages;

use App\Filament\Resources\Atom\CameraWebs\CameraWebResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCameraWeb extends EditRecord
{
    protected static string $resource = CameraWebResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
