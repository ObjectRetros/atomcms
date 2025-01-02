<?php

namespace App\Filament\Resources\Hotel\EmulatorTextResource\Pages;

use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\Hotel\EmulatorTextResource;
use App\Services\RconService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ManageEmulatorTexts extends ManageRecords
{
    protected static string $resource = EmulatorTextResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('update-emulator-texts')
                ->label('Update texts (RCON)')
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
                        $rconService->updateConfig(Auth::user(), ':update_texts');

                        Notification::make()
                        ->body(__('RCON executed! If you have the ":update_texts" permission in-game, the emulator texts changes will now be live on the hotel.'))
                        ->icon('heroicon-o-check-circle')
                        ->iconColor('success')
                        ->send();
                    }
                }),

            CreateAction::make('create')
        ];
    }

    public function getPrimaryKey(): string
    {
        return 'key';
    }
}
