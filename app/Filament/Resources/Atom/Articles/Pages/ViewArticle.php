<?php

namespace App\Filament\Resources\Atom\Articles\Pages;

use App\Filament\Resources\Atom\Articles\ArticleResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ViewArticle extends ViewRecord
{
    protected static string $resource = ArticleResource::class;

    public function getHeaderActions(): array
    {
        return [
            Action::make('Send Notification')
                ->label(__('Send notifications'))
                ->color('gray')
                ->visible(fn (Model $record) => $record->user_id === Auth::id())
                ->requiresConfirmation()
                ->action(function (Model $record) {
                    $record->createFollowersNotification();
                }),

            EditAction::make(),
        ];
    }
}
