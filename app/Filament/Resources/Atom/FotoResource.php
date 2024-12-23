<?php

namespace App\Filament\Resources\Atom;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Resources\Atom\FotoResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use App\Models\Camera;

class FotoResource extends Resource
{
    protected static ?string $model = Camera::class;
	
    protected static ?string $navigationIcon = 'heroicon-o-photo';
	
	protected static ?string $navigationGroup = 'Website';

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
            'index' => Pages\ListFotos::route('/'),
            'edit' => Pages\EditFoto::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
