<?php

namespace App\Filament\Resources\Hotel\WebsiteAds\Pages;

use App\Filament\Resources\Hotel\WebsiteAds\WebsiteAdResource;
use App\Models\WebsiteAd;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListWebsiteAds extends ListRecords
{
    protected static string $resource = WebsiteAdResource::class;

    protected function getActions(): array
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
                    session()->flash('success', 'ADS data imported successfully!');
                })
                ->requiresConfirmation()
                ->modalHeading('Import ADS Data')
                ->modalDescription('Are you sure you want to import ADS data? This action cannot be undone.')
                ->modalButton('Yes, import data'),
            Action::make('emptyTable')
                ->label('Empty Database Table')
                ->color('danger')
                ->action(function () {
                    WebsiteAd::truncate();
                    session()->flash('success', 'The table has been emptied successfully!');
                })
                ->requiresConfirmation()
                ->modalHeading('Empty Table')
                ->modalDescription('Are you sure you want to empty the table? This action cannot be undone and will delete all records.')
                ->modalButton('Yes, empty table'),
        ];
    }
}
