<?php

namespace App\Filament\Resources\User\PlusBans\Pages;

use App\Filament\Resources\User\PlusBans\PlusBanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePlusBans extends ManageRecords
{
    protected static string $resource = PlusBanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $data['added_by'] = auth()->user()->username;
                    $data['added_date'] = time();

                    return $data;
                }),
        ];
    }
}
