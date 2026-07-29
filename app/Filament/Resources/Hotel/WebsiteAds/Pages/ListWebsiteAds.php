<?php

namespace App\Filament\Resources\Hotel\WebsiteAds\Pages;

use App\Filament\Resources\Hotel\WebsiteAds\WebsiteAdResource;
use App\Models\WebsiteAd;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListWebsiteAds extends ListRecords
{
    protected static string $resource = WebsiteAdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create new ADS')
                ->color('success'),
            Action::make('importAdsData')
                ->label('Import ADS Images from folder')
                ->color('info')
                ->action(function () {
                    Artisan::call('import:ads-data');

                    Notification::make()
                        ->title('ADS data imported successfully!')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Import ADS Data')
                ->modalDescription('Are you sure you want to import ADS data? This action cannot be undone.')
                ->modalButton('Yes, import data'),
            Action::make('emptyTable')
                ->label('Empty Database Table')
                ->color('danger')
                ->visible(fn (): bool => auth()->user()?->can('deleteAny', WebsiteAd::class) ?? false)
                ->action(function () {
                    // Delete one by one so the model's deleting hook removes
                    // the uploaded files from the ads disk; truncate would
                    // orphan them.
                    WebsiteAd::query()->each(fn (WebsiteAd $ad) => $ad->delete());

                    Notification::make()
                        ->title('The table has been emptied successfully!')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Empty Table')
                ->modalDescription('Are you sure you want to empty the table? This action cannot be undone and will delete all records.')
                ->modalButton('Yes, empty table'),
        ];
    }
}
