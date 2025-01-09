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
            Actions\CreateAction::make()
                ->label('Add Badge')
				->color('info')
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
            Actions\Action::make('backup')
                ->label('Create Backup')
                ->color('success')
                ->action('createBackup'),
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

        $badgeKeys = $badges->pluck('badge_key')->toArray();

        foreach ($jsonData as $key => $value) {
            if (str_starts_with($key, 'badge_desc_') && !in_array($key, $badgeKeys)) {
                unset($jsonData[$key]);
            }
        }

        foreach ($badges as $badge) {
            $jsonData[$badge->badge_key] = $badge->badge_description;
        }

        file_put_contents($jsonPath, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        Notification::make()
            ->title('Export Successful')
            ->body('Badge data exported successfully.')
            ->success()
            ->send();
    }

    public function createBackup(SettingsService $settingsService)
    {
        $jsonPath = $settingsService->getOrDefault('nitro_external_texts_file');

        if (empty($jsonPath)) {
            Notification::make()
                ->title('Backup Failed')
                ->body('The JSON file path is not configured in the website settings.')
                ->danger()
                ->send();
            return;
        }

        if (!file_exists($jsonPath)) {
            Notification::make()
                ->title('Backup Failed')
                ->body('The JSON file does not exist at the specified path.')
                ->danger()
                ->send();
            return;
        }

        $backupPath = dirname($jsonPath) . '/ExternalTexts_' . time(). '.json';

        if (copy($jsonPath, $backupPath)) {
            Notification::make()
                ->title('Backup Successful')
                ->body('A backup of the JSON file has been created: ' . basename($backupPath))
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Backup Failed')
                ->body('Failed to create a backup of the JSON file.')
                ->danger()
                ->send();
        }
    }
}