<?php

namespace App\Filament\Resources\Atom;

use App\Models\Miscellaneous\CameraWeb;
use Filament\Tables;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Resources\Atom\CameraWebResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use App\Models\Camera;

class CameraWebResource extends Resource
{
    protected static ?string $model = CameraWeb::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

	protected static ?string $navigationGroup = 'Website';

    protected static ?string $slug = 'camera-web';

    protected static ?string $pluralModelLabel = 'photos';
	protected static ?string $navigationLabel = 'Web Camera';

	public static function form(Form $form): Form
{
    return $form
        ->schema([
            Toggle::make('visible')
                ->label(__('Visible'))
                ->default(true),
        ]);
	}

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament::resources.columns.id'))
                    ->sortable(),
                TextColumn::make('user_id')
                    ->label(__('filament::resources.columns.user_id')),
                TextColumn::make('room_id')
                    ->label(__('filament::resources.columns.room_id')),
                TextColumn::make('timestamp')
                    ->label(__('filament::resources.columns.created_at'))
                    ->dateTime(),
                ImageColumn::make('url')
                    ->label(__('filament::resources.columns.image'))
                    ->extraAttributes(['style' => 'image-rendering: pixelated'])
                    ->size(125),
				ToggleColumn::make('visible')
					->label(__('Visible'))
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCameraWeb::route('/'),
            'edit' => Pages\EditCameraWeb::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
