<?php

namespace App\Filament\Resources\Hotel\CatalogPageResource\Pages;

use App\Filament\Resources\Hotel\CatalogPageResource;
use App\Services\RconService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListCatalogPages extends ListRecords
{
    protected static string $resource = CatalogPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('update-catalog')
                ->label('Update catalog (RCON)')
                ->color('danger')
                ->action(function () {
                    $rconService = app(RconService::class);

                    if (!$rconService->isConnected()) {
                        Notification::make()
                            ->body(__('The RCON service is not connected.'))
                            ->icon('heroicon-o-exclamation-circle')
                            ->iconColor('danger')
                            ->send();

                        return;
                    } else {
                        $rconService->updateCatalog();

                        Notification::make()
                        ->body(__('The catalog has been updated!'))
                        ->icon('heroicon-o-check-circle')
                        ->iconColor('success')
                        ->send();
                    }
                }),

            CreateAction::make(),
        ];
    }
}
