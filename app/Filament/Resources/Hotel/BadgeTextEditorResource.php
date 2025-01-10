<?php

namespace App\Filament\Resources\Hotel;

use App\Filament\Resources\Hotel\BadgeTextEditorResource\Pages;
use App\Models\WebsiteBadge;
use App\Services\SettingsService;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class BadgeTextEditorResource extends Resource
{
    protected static ?string $model = WebsiteBadgedata::class;

    protected static ?string $navigationGroup = 'Hotel';
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationLabel = 'Badge Editor';
    protected static ?string $modelLabel = 'Badge Text';
    protected static ?string $slug = 'hotel/badge-text-editor';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('badge_key')
                    ->required()
                    ->label('Badge Key - Expl. ATOM101')
                    ->placeholder('This is the badge code'),
				Forms\Components\TextInput::make('badge_name')
                    ->required()
                    ->label('Badge Name')
                    ->placeholder('This is the name of the badge: Expl. The ATOM Badge'),
                Forms\Components\Textarea::make('badge_description')
                    ->required()
                    ->label('Badge Description')
                    ->placeholder('Please add a description for the badge.'),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        $settingsService = app(SettingsService::class);
        $badgesPath = $settingsService->getOrDefault('badges_path', '/gamedata/c_images/album1584/');

        return $table
            ->columns([                
                Tables\Columns\ImageColumn::make('badge_key')
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBadgeTextEditors::route('/'),
            'create' => Pages\CreateBadgeTextEditor::route('/create'),
            'edit' => Pages\EditBadgeTextEditor::route('/{record}/edit'),
        ];
    }
}