<?php

namespace App\Filament\Resources\Home\HomeItemResource\Pages;

use App\Filament\Resources\Home\HomeItemResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditHomeItem extends EditRecord
{
    protected static string $resource = HomeItemResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make()
                ->action(function (Model $record): void {
                    if ($record->userHomeItems()->exists()) {
                        Notification::make()
                            ->danger()
                            ->title(__('Unable to delete item'))
                            ->body(__('This item is currently in use by one or more users.'))
                            ->persistent()
                            ->send();

                        $this->halt();

                        return;
                    }

                    $record->delete();

                    $this->redirect(HomeItemResource::getUrl('index'));

                    Notification::make()
                        ->success()
                        ->title(__('Item deleted successfully'))
                        ->send();
                }),
        ];
    }
}
