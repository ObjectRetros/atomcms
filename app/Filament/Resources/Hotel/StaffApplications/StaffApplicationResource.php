<?php

namespace App\Filament\Resources\Hotel\StaffApplications;

use App\Filament\Resources\Hotel\StaffApplications\Pages\EditStaffApplication;
use App\Filament\Resources\Hotel\StaffApplications\Pages\ListStaffApplications;
use App\Models\Community\Staff\WebsiteStaffApplications;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StaffApplicationResource extends Resource
{
    protected static ?string $model = WebsiteStaffApplications::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'username')
                    ->required()
                    ->searchable(),
                Select::make('rank_id')
                    ->relationship('rank', 'rank_name')
                    ->required()
                    ->searchable(),
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.username')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('rank.rank_name')
                    ->label('Rank')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('content')
                    ->limit(50)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStaffApplications::route('/'),
            'edit' => EditStaffApplication::route('/{record}/edit'),
        ];
    }
}
