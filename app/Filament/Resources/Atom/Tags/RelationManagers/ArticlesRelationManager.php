<?php

namespace App\Filament\Resources\Atom\Tags\RelationManagers;

use App\Filament\Resources\Atom\Articles\ArticleResource;
use App\Filament\Traits\TranslatableResource;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ArticlesRelationManager extends RelationManager
{
    use TranslatableResource;

    // Use camelCase to match the method in the Tag model
    protected static string $relationship = 'websiteArticles';

    protected static ?string $recordTitleAttribute = 'title';

    public static string $translateIdentifier = 'article';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(ArticleResource::getForm());
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns(ArticleResource::getTable())
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                ViewAction::make(),
                DetachAction::make(),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
            ]);
    }
}
