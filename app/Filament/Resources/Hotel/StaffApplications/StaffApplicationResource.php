<?php

namespace App\Filament\Resources\Hotel\StaffApplications;

use App\Filament\Resources\Hotel\StaffApplications\Pages\ListStaffApplications;
use App\Models\Community\Staff\WebsiteStaffApplications;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
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
                    ->label('Rank')
                    ->relationship('rank', 'rank_name')
                    ->searchable()
                    ->nullable(),

                Select::make('team_id')
                    ->label('Team')
                    ->relationship('team', 'rank_name')
                    ->searchable()
                    ->nullable(),

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

                TextColumn::make('applied_for')
                    ->label('Applied For')
                    ->state(fn (WebsiteStaffApplications $record) => $record->team_id
                        ? ($record->team->rank_name ?? '-')
                        : ($record->rank->rank_name ?? '-'),
                    )
                    ->searchable(query: function ($query, string $search) {
                        $query
                            ->orWhereHas('rank', fn ($q) => $q->where('rank_name', 'like', "%{$search}%"))
                            ->orWhereHas('team', fn ($q) => $q->where('rank_name', 'like', "%{$search}%"));
                    })
                    ->sortable(),

                TextColumn::make('rank.rank_name')
                    ->label('Rank')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('team.rank_name')
                    ->label('Team')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('content')
                    ->limit(50)
                    ->wrap()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
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
        ];
    }
}
