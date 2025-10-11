<?php

namespace App\Filament\Resources\Atom\Articles\Pages;

use App\Filament\Resources\Atom\Articles\ArticleResource;
use App\Models\Article;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    protected function afterCreate(): void
    {
        /** @var null|Article $articleCreated */
        $articleCreated = $this->getRecord();

        if (! $articleCreated || ! $articleCreated->visible) {
            return;
        }

        $articleCreated->createFollowersNotification();
    }
}
