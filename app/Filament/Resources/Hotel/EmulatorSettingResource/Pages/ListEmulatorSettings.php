<?php

namespace App\Filament\Resources\Hotel\EmulatorSettingResource\Pages;

use App\Filament\Resources\Hotel\EmulatorSettingResource;
use App\Services\RconService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListEmulatorSettings extends ListRecords
{
    protected static string $resource = EmulatorSettingResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('update-emulator-config')
                ->label('Update settings (RCON)')
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
                        $rcon->updateConfig(Auth::user(), ':update_config');
                        Notification::make()
                        ->body(__('RCON executed! If you have the ":update_config" permission in-game, the emulator settings changes will now be live on the hotel.'))
                        ->icon('heroicon-o-check-circle')
                        ->iconColor('success')
                        ->send();
                    }
                }),

            CreateAction::make(),
        ];
    }
}
