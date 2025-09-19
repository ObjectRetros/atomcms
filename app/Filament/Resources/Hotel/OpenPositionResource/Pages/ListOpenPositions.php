<?php

namespace App\Filament\Resources\Hotel\OpenPositionResource\Pages;

use App\Filament\Resources\Hotel\OpenPositionResource;
use Filament\Resources\Pages\ListRecords;

class ListOpenPositions extends ListRecords
{
    protected static string $resource = OpenPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}