<?php

namespace App\Filament\Resources\Hotel\WordFilterResource\Pages;

use App\Filament\Resources\Hotel\WordFilterResource;
use App\Services\RconService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageWordFilters extends ManageRecords
{
    protected static string $resource = WordFilterResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('update-wordfilter')
                ->label('Update wordfilter (RCON)')
                ->color('danger')
                ->action(function () {
                    $rcon = app(RconService::class);
                    if (!$rcon->isConnected()) {
                        Notification::make()
                            ->body(__('The RCON service is not connected.'))
                            ->icon('heroicon-o-exclamation-circle')
                            ->iconColor('danger')
                            ->send();
                        return;
                    } else {
                        $rcon->updateWordFilter();
                        Notification::make()
                        ->body(__('The wordfilter has been successfully updated!'))
                        ->icon('heroicon-o-check-circle')
                        ->iconColor('success')
                        ->send();
                    }
                }),

            CreateAction::make(),
        ];
    }
}
