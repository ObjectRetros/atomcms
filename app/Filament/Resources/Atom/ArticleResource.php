<?php

namespace App\Filament\Resources\Atom;

use App\Models\Articles\WebsiteArticle;
use Filament\Forms\Components\RichEditor;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use App\Filament\Traits\TranslatableResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Atom\ArticleResource\Pages;
use App\Filament\Resources\Atom\ArticleResource\RelationManagers;

class ArticleResource extends Resource
{
    use TranslatableResource;

    protected static ?string $model = WebsiteArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Website';

    protected static ?string $slug = 'website/articles';

    public static string $translateIdentifier = 'articles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::getForm());
    }

    public static function getForm(): array
    {
        return [
            Tabs::make('Main')
                ->tabs([
                    Tabs\Tab::make(__('filament::resources.tabs.Home'))
                        ->icon('heroicon-o-home')
                        ->schema([
                            TextInput::make('title')
                                ->label(__('filament::resources.inputs.title'))
                                ->required()
                                ->autocomplete()
                                ->maxLength(255)
                                ->columnSpan('full'),

                            TextInput::make('short_story')
                                ->label(__('filament::resources.inputs.description'))
                                ->required()
                                ->maxLength(255)
                                ->autocomplete()
                                ->columnSpan('full'),

                            FileUpload::make('image')
                                ->label(__('filament::resources.inputs.image'))
                                ->directory('website_news_images')
                                ->visibility('public'),

                            RichEditor::make('full_story')
                                ->label(__('filament::resources.inputs.content'))
                                ->required()
                                ->columnSpan('full'),

                            Hidden::make('user_id')
                                ->default(fn () => auth()->check() ? auth()->user()->id : null),
                        ]),

                    Tabs\Tab::make(__('filament::resources.tabs.Configurations'))
                        ->icon('heroicon-o-cog')
                        ->schema([
                            Toggle::make('is_visible')
                                ->label(__('filament::resources.inputs.visible'))
                                ->onIcon('heroicon-s-check')
                                ->offIcon('heroicon-s-x-mark')
                                ->default(true)
                                ->live()
                                ->afterStateUpdated(function (string $operation, $state, $record) {
                                    if ($operation !== 'edit' || is_null($record)) {
										return;
									}

                                    try {
                                        if ($state) {
                                            $record->restore();
                                        } else {
                                            $record->delete();
                                        }
                                    } catch (\Exception $e) {
                                        report($e);
                                    }
                                })
                                ->formatStateUsing(function ($record) {
									if (is_null($record)) {
										return true;
									}								
									return is_null($record->deleted_at);
								}),

                            Toggle::make('can_comment')
                                ->onIcon('heroicon-s-check')
                                ->label(__('filament::resources.inputs.allow_comments'))
                                ->default(true)
                                ->offIcon('heroicon-s-x-mark'),
                        ]),
                ])->columnSpanFull()
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->poll('60s')
            ->columns(static::getTable())
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
            ]);
    }

    public static function getTable(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('filament::resources.columns.id')),

            ImageColumn::make('image')
                ->circular()
                ->extraAttributes(['style' => 'image-rendering: pixelated'])
                ->size(50)
                ->label(__('filament::resources.columns.image')),

            TextColumn::make('title')
                ->label(__('filament::resources.columns.title'))
                ->searchable()
                ->limit(50),

            TextColumn::make('user.username')
                ->searchable()
                ->label(__('filament::resources.columns.by')),

            ToggleColumn::make('is_visible')
                ->label(__('filament::resources.columns.visible'))
                ->onIcon('heroicon-s-check')
                ->toggleable()
                ->state(fn ($record) => is_null($record->deleted_at))
                ->disabled(),

            ToggleColumn::make('allow_comments')
                ->label(__('filament::resources.columns.allow_comments'))
                ->onIcon('heroicon-s-check')
                ->toggleable()
                ->disabled(),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
	
	public static function getRelations(): array
    {
        return [
            RelationManagers\TagsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'view' => Pages\ViewArticle::route('/{record}'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->withTrashed();
    }
}
