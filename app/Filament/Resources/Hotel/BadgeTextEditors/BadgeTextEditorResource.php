<?php

namespace App\Filament\Resources\Hotel\BadgeTextEditors;

use App\Filament\Resources\Hotel\BadgeTextEditors\Pages\CreateBadgeTextEditor;
use App\Filament\Resources\Hotel\BadgeTextEditors\Pages\EditBadgeTextEditor;
use App\Filament\Resources\Hotel\BadgeTextEditors\Pages\ListBadgeTextEditors;
use App\Models\WebsiteBadge;
use App\Services\SettingsService;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BadgeTextEditorResource extends Resource
{
    protected static ?string $model = WebsiteBadge::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationLabel = 'Badge Editor';

    protected static ?string $modelLabel = 'Badge Text';

    protected static ?string $slug = 'hotel/badge-text-editor';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('badge_key')
                    ->required()
                    ->label('Badge Key - Expl. ATOM101')
                    ->placeholder('This is the badge code'),
                TextInput::make('badge_name')
                    ->required()
                    ->label('Badge Name')
                    ->placeholder('This is the name of the badge: Expl. The ATOM Badge'),
                Textarea::make('badge_description')
                    ->required()
                    ->label('Badge Description')
                    ->placeholder('Please add a description for the badge.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $settingsService = app(SettingsService::class);
        $badgesPath = $settingsService->getOrDefault('badges_path', '/gamedata/c_images/album1584/');

        return $table
            ->columns([
                ImageColumn::make('badge_key')
                    ->label('Badge Image')
                    ->getStateUsing(function ($record) use ($badgesPath) {
                        $badgeName = str_replace('badge_desc_', '', $record->badge_key);
                        $imageUrl = asset($badgesPath . $badgeName . '.gif');

                        return $imageUrl;
                    })
                    ->width(50)
                    ->height(50),
                TextColumn::make('badge_name')
                    ->label('Badge Code & Name')
                    ->formatStateUsing(function ($record) {
                        return $record->badge_key . ' : ' . $record->badge_name;
                    })
                    ->searchable(query: function ($query, $search) {
                        $query->where('badge_key', 'like', "%{$search}%")
                            ->orWhere('badge_name', 'like', "%{$search}%");
                    })
                    ->sortable(),
                TextColumn::make('badge_description')
                    ->label('Badge Description')
                    ->getStateUsing(fn ($record) => Str::limit($record->badge_description, 65))
                    ->searchable(),
            ])
            ->filters([])
            ->defaultSort('badge_key', 'asc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBadgeTextEditors::route('/'),
            'create' => CreateBadgeTextEditor::route('/create'),
            'edit' => EditBadgeTextEditor::route('/{record}/edit'),
        ];
    }
}
