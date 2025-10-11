<?php

namespace App\Filament\Resources\Atom\Teams;

use App\Filament\Resources\Atom\Teams\Pages\CreateTeam;
use App\Filament\Resources\Atom\Teams\Pages\EditTeam;
use App\Filament\Resources\Atom\Teams\Pages\ListTeams;
use App\Filament\Tables\Columns\HabboBadgeColumn;
use App\Filament\Traits\TranslatableResource;
use App\Models\Community\Staff\WebsiteTeam;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TeamResource extends Resource
{
    use TranslatableResource;

    protected static ?string $model = WebsiteTeam::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $slug = 'website/teams';

    public static string $translateIdentifier = 'teams';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('rank_name')
                            ->autofocus()
                            ->maxLength(255)
                            ->required()
                            ->label(__('filament::resources.inputs.name')),

                        TextInput::make('job_description')
                            ->maxLength(255)
                            ->label(__('filament::resources.inputs.description')),

                        TextInput::make('badge')
                            ->maxLength(255)
                            ->label(__('filament::resources.inputs.badge_code'))
                            ->required(),

                        Toggle::make('hidden_rank')
                            ->label(__('filament::resources.inputs.is_hidden')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament::resources.columns.id')),

                HabboBadgeColumn::make('badge')
                    ->label(__('filament::resources.columns.badge')),

                TextColumn::make('rank_name')
                    ->label(__('filament::resources.columns.name')),

                TextColumn::make('job_description')
                    ->label(__('filament::resources.inputs.description')),

                IconColumn::make('hidden_rank')
                    ->label(__('filament::resources.columns.is_hidden'))
                    ->icon(fn (Model $record) => $record->hidden_rank ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->colors([
                        'danger' => false,
                        'success' => true,
                    ]),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeams::route('/'),
            'create' => CreateTeam::route('/create'),
            'edit' => EditTeam::route('/{record}/edit'),
        ];
    }
}
