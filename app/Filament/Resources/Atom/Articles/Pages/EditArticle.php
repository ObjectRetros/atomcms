<?php

namespace App\Filament\Resources\Atom\Articles\Pages;

use App\Filament\Resources\Atom\Articles\ArticleResource;
use App\Models\Articles\WebsiteArticle;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use LogicException;

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
        if (! $record instanceof WebsiteArticle) {
            throw new LogicException('The article editor received an unsupported model.');
        }

        $isVisible = (bool) Arr::pull($data, 'is_visible', true);

        return DB::transaction(function () use ($record, $data, $isVisible): Model {
            $updatedRecord = parent::handleRecordUpdate($record, $data);

            if (! $updatedRecord instanceof WebsiteArticle) {
                throw new LogicException('The article editor updated an unsupported model.');
            }

            if ($isVisible && $updatedRecord->trashed()) {
                $updatedRecord->restore();
            } elseif (! $isVisible && ! $updatedRecord->trashed()) {
                $updatedRecord->delete();
            }

            return $updatedRecord;
        });
    }
}
