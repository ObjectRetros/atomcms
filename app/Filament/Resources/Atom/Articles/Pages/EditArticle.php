<?php

namespace App\Filament\Resources\Atom\Articles\Pages;

use App\Filament\Resources\Atom\Articles\ArticleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $isVisible = (bool) Arr::pull($data, 'is_visible', true);

        return DB::transaction(function () use ($record, $data, $isVisible): Model {
            $record = parent::handleRecordUpdate($record, $data);

            if ($isVisible && $record->trashed()) {
                $record->restore();
            } elseif (! $isVisible && ! $record->trashed()) {
                $record->delete();
            }

            return $record;
        });
    }
}
