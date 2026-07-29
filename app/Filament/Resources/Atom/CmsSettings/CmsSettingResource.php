<?php

namespace App\Filament\Resources\Atom\CmsSettings;

use App\Filament\Concerns\TranslatableResource;
use App\Filament\Resources\Atom\CmsSettings\Pages\ManageCmsSettings;
use App\Filament\Support\TruncatedTooltip;
use App\Models\Miscellaneous\WebsiteSetting;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CmsSettingResource extends Resource
{
    use TranslatableResource;

    protected static ?string $model = WebsiteSetting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $slug = 'website/cms-settings';

    public static string $translateIdentifier = 'cms-settings';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('key')
                            ->label(__('filament::resources.inputs.key'))
                            ->maxLength(50)
                            ->autocomplete()
                            ->unique(ignoreRecord: true)
                            ->required(),

                        TextInput::make('value')
                            ->label(__('filament::resources.inputs.value'))
                            ->required()
                            ->maxLength(255)
                            ->autocomplete(),

                        TextInput::make('comment')
                            ->label(__('filament::resources.inputs.comment'))
                            ->nullable()
                            ->maxLength(255)
                            ->autocomplete()
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'sm' => 2,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('key')
                    ->label(__('filament::resources.columns.key'))
                    ->searchable(),

                TextColumn::make('value')
                    ->label(__('filament::resources.columns.value'))
                    ->searchable()
                    ->limit(30),

                TextColumn::make('comment')
                    ->label(__('filament::resources.columns.comment'))
                    ->toggleable()
                    ->searchable()
                    ->tooltip(fn (TextColumn $column): ?string => TruncatedTooltip::of($column))
                    ->limit(60),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                // ...
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCmsSettings::route('/'),
        ];
    }
}
