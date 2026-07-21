<?php

namespace App\Filament\Resources\Atom\Articles\Pages;

use App\Filament\Resources\Atom\Articles\ArticleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $isVisible = (bool) Arr::pull($data, 'is_visible', true);

        return DB::transaction(function () use ($data, $isVisible): Model {
            $record = parent::handleRecordCreation($data);

            if (! $isVisible) {
                $record->delete();
            }

            return $record;
        });
    }
}
