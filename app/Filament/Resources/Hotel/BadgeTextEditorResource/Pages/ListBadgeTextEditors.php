<?php

namespace App\Filament\Resources\Hotel\BadgeTextEditorResource\Pages;

use App\Filament\Resources\Hotel\BadgeTextEditorResource;
use App\Models\WebsiteBadgedata;
use App\Services\SettingsService;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\File;

class ListBadgeTextEditors extends ListRecords
{
    protected static string $resource = BadgeTextEditorResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make() // Add this line for the "Add Badge" button
                ->label('Add Badge')
                ->modalHeading('Add a New Badge')
                ->modalButton('Create Badge')
                ->after(function () {
                    Notification::make()
                        ->title('Badge Created')
                        ->body('The badge was successfully created.')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('export')
                ->label('Export to JSON')
                ->action('exportToJson'),
        ];
    }

    public function exportToJson(SettingsService $settingsService)
    {
        $jsonPath = $settingsService->getOrDefault('nitro_external_texts_file');

        if (empty($jsonPath)) {
            Notification::make()
                ->title('Export Failed')
                ->body('The JSON file path is not configured in the website settings.')
                ->danger()
                ->send();
            return;
        }

        if (!file_exists($jsonPath)) {
            Notification::make()
                ->title('Export Failed')
                ->body('The JSON file does not exist at the specified path.')
                ->danger()
                ->send();
            return;
        }

        $jsonData = json_decode(file_get_contents($jsonPath), true);

        $badges = WebsiteBadgedata::all();

        foreach ($badges as $badge) {
            $jsonData[$badge->badge_key] = $badge->badge_description;
        }

        file_put_contents($jsonPath, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        Notification::make()
            ->title('Export Successful')
            ->body('Badge data exported successfully.')
            ->success()
            ->send();
    }
}