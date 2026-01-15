<?php

namespace App\Filament\Resources\Atom\CmsSettings\Pages;

use App\Filament\Resources\Atom\CmsSettings\CmsSettingResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Cache;

class ManageCmsSettings extends ManageRecords
{
    protected static string $resource = CmsSettingResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('reload_cache')
                ->label('Reload Cache')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reload Settings Cache')
                ->modalDescription('This will clear and reload the website settings cache. The cache will be automatically rebuilt on the next request.')
                ->modalSubmitActionLabel('Reload Cache')
                ->action(function () {
                    Cache::forget('website_settings');

                    Notification::make()
                        ->success()
                        ->title('Cache Cleared')
                        ->body('Settings cache has been cleared successfully.')
                        ->send();
                }),

            CreateAction::make(),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [25, 50, 100];
    }
}
