<?php

namespace App\Filament\Resources\Hotel\BadgeTextEditorResource\Pages;

use App\Filament\Resources\Hotel\BadgeTextEditorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use PDOException;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class EditBadgeTextEditor extends EditRecord
{
    protected static string $resource = BadgeTextEditorResource::class;

    protected function getActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    protected function afterSave(): void {}

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return parent::handleRecordUpdate($record, $data);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                  Log::error('Duplicate badge key error: ' . $e->getMessage());

                Notification::make()
                    ->title('Duplicate Badge Key')
                    ->body('The badge key already exists. Please use a unique badge key.')
                    ->danger()
                    ->persistent()
                    ->send();

                return $record;
            }
            throw $e;
        }
    }
}