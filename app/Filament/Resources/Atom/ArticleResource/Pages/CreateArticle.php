<?php

namespace App\Filament\Resources\Atom\ArticleResource\Pages;

use App\Models\User;
use App\Models\Article;
use App\Enums\NotificationType;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Atom\ArticleResource;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    protected function afterCreate(): void
    {
        /** @var null|Article $articleCreated */
        $articleCreated = $this->getRecord();

        if (!$articleCreated || !$articleCreated->visible) return;

        $articleCreated->createFollowersNotification();
    }
}
