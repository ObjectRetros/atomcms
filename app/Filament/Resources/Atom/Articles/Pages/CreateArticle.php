<?php

namespace App\Filament\Resources\Atom\Articles\Pages;

use App\Filament\Resources\Atom\Articles\ArticleResource;
use App\Support\AuthenticatedUser;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Never trust a client-supplied author; the article always belongs
        // to the staff member who is creating it.
        $data['user_id'] = AuthenticatedUser::current()->id;

        return $data;
    }

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
