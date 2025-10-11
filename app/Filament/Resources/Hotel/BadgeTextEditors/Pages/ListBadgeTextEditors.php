<?php

namespace App\Filament\Resources\Hotel\BadgeTextEditors\Pages;

use App\Filament\Resources\Hotel\BadgeTextEditors\BadgeTextEditorResource;
use App\Models\WebsiteBadge;
use App\Services\SettingsService;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Log;

class ListBadgeTextEditors extends ListRecords
{
    protected static string $resource = BadgeTextEditorResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make()
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
            Action::make('export')
                ->label('Export to ExternalTexts')
                ->action('exportToJson'),
            Action::make('backup')
                ->label('Create Backup of ExternalTexts')
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

        if (! file_exists($jsonPath)) {
            Notification::make()
                ->title('Export Failed')
                ->body('The JSON file does not exist at the specified path.')
                ->danger()
                ->send();

            return;
        }

        $jsonData = json_decode(file_get_contents($jsonPath), true);

        $badges = WebsiteBadge::all();
        $badgeKeys = $badges->pluck('badge_key')->toArray();

        foreach ($jsonData as $key => $value) {
            if (
                (str_starts_with($key, 'badge_desc_') || str_starts_with($key, 'badge_name_')) &&
                ! in_array(str_replace(['badge_desc_', 'badge_name_'], '', $key), $badgeKeys)
            ) {
                unset($jsonData[$key]);
            }
        }

        foreach ($badges as $badge) {
            $jsonData['badge_desc_' . $badge->badge_key] = $badge->badge_description;
            $jsonData['badge_name_' . $badge->badge_key] = $badge->badge_name;
        }

        try {
            $result = file_put_contents(
                $jsonPath,
                json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            );

            if ($result === false) {
                throw new Exception('Failed to write to the JSON file.');
            }

            Notification::make()
                ->title('Export Successful')
                ->body('Badge data exported successfully.')
                ->success()
                ->send();
        } catch (Exception $e) {
            Log::error('Failed to export badge data: ' . $e->getMessage());

            Notification::make()
                ->title('Export Failed')
                ->body('Failed to export badge data. Please check file permissions or contact your administrator.')
                ->danger()
                ->send();
        }
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

        if (! file_exists($jsonPath)) {
            Notification::make()
                ->title('Backup Failed')
                ->body('The JSON file does not exist at the specified path.')
                ->danger()
                ->send();

            return;
        }

        $backupPath = dirname($jsonPath) . '/ExternalTexts_' . time() . '.json';

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
