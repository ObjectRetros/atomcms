<?php

namespace App\Filament\Resources\Home\HomeCategoryResource\Pages;

use App\Filament\Resources\Home\HomeCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditHomeCategory extends EditRecord
{
    protected static string $resource = HomeCategoryResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make()
                ->action(function (Model $record): void {
                    if ($record->homeItems()->exists()) {
                        Notification::make()
                            ->danger()
                            ->title(__('Unable to delete category'))
                            ->body(__('This category is currently in use by one or more items.'))
                            ->persistent()
                            ->send();

                        $this->halt();

                        return;
                    }

                    $record->delete();

                    $this->redirect(HomeCategoryResource::getUrl('index'));

                    Notification::make()
                        ->success()
                        ->title(__('Category deleted successfully'))
                        ->send();
                }),
        ];
    }
}
