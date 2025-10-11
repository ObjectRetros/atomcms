<?php

namespace App\Filament\Resources\Atom\Permissions;

use App\Filament\Resources\Atom\Permissions\Pages\CreatePermission;
use App\Filament\Resources\Atom\Permissions\Pages\EditPermission;
use App\Filament\Resources\Atom\Permissions\Pages\ListPermissions;
use App\Filament\Resources\Atom\Permissions\Pages\ViewPermission;
use App\Filament\Tables\Columns\HabboBadgeColumn;
use App\Filament\Traits\TranslatableResource;
use App\Models\Game\Permission;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;
use Str;

class PermissionResource extends Resource
{
    use TranslatableResource;

    protected static ?string $model = Permission::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $slug = 'website/permissions';

    public static string $translateIdentifier = 'permissions';

    protected static ?string $recordTitleAttribute = 'rank_name';

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        /**
         * @param  string  $name
         * @param  bool  $needsSecondOption  = false
         */
        $groupedToggleButton = fn (string $name, bool $needsSecondOption = false): ToggleButtons => ToggleButtons::make($name)
            ->label(function () use ($name) {
                $translationKey = "filament::resources.permissions.{$name}";
                $translation = __($translationKey);

                if ($translationKey == $translation) {
                    return $name;
                }

                return $translation;
            })
            ->options(function () use ($needsSecondOption) {
                $options = [
                    '0' => __('filament::resources.options.no'),
                    '1' => __('filament::resources.options.yes'),
                ];

                if ($needsSecondOption) {
                    $options['2'] = __('filament::resources.options.rights');
                }

                return $options;
            })
            ->icons(['0' => 'heroicon-o-check', '1' => 'heroicon-o-x-mark', '2' => 'heroicon-o-sparkles'])
            ->colors(['0' => 'danger', '1' => 'success'])
            ->grouped();

        return $schema
            ->components([
                Tabs::make('Main')
                    ->tabs([
                        Tab::make(__('filament::resources.tabs.General Information'))
                            ->schema([
                                TextInput::make('rank_name')
                                    ->label(__('filament::resources.inputs.name'))
                                    ->maxLength(25)
                                    ->required(),

                                TextInput::make('badge')
                                    ->label(__('filament::resources.inputs.badge_code'))
                                    ->maxLength(12)
                                    ->required(),

                                TextInput::make('level')
                                    ->label(__('filament::resources.inputs.level'))
                                    ->required(),

                                TextInput::make('room_effect')
                                    ->label(__('filament::resources.inputs.room_effect'))
                                    ->required(),
                            ]),

                        Tab::make(__('filament::resources.tabs.In-game Permissions'))
                            ->schema([
                                Section::make(__('filament::resources.sections.permissions.title'))
                                    ->description(new HtmlString(__('filament::resources.sections.permissions.description')))
                                    ->schema([
                                        Grid::make()
                                            ->columns([
                                                'sm' => 2,
                                                'md' => 3,
                                                'lg' => 3,
                                            ])
                                            ->schema(function () use ($groupedToggleButton) {
                                                $columns = Schema::getColumns('permissions');

                                                $arcturusPermissions = collect($columns)->filter(function (array $column) {
                                                    $columnName = $column['name'] ?? null;

                                                    if (! $columnName) {
                                                        return false;
                                                    }

                                                    return str_starts_with($columnName, 'cmd')
                                                        || str_starts_with($columnName, 'acc')
                                                        || str_ends_with($columnName, 'cmd');
                                                })->values();

                                                return $arcturusPermissions->map(function (array $column) use ($groupedToggleButton) {
                                                    $columnName = $column['name'];
                                                    $needsSecondOption = $column['type_name'] == 'enum' && str_ends_with($column['type'], "'2')");

                                                    return $groupedToggleButton($columnName, $needsSecondOption);
                                                })->toArray();
                                            }),
                                    ]),

                            ]),

                        Tab::make(__('filament::resources.tabs.Configurations'))
                            ->schema([
                                Grid::make(['default' => 2])
                                    ->schema([
                                        Select::make('log_commands')
                                            ->label(__('filament::resources.inputs.log_commands'))
                                            ->columnSpanFull()
                                            ->options([
                                                '0' => __('filament::resources.options.no'),
                                                '1' => __('filament::resources.options.yes'),
                                            ]),

                                        TextInput::make('prefix')
                                            ->label(__('filament::resources.inputs.prefix'))
                                            ->maxLength(5)
                                            ->required(),

                                        ColorPicker::make('prefix_color')
                                            ->label(__('filament::resources.inputs.prefix_color'))
                                            ->required(),

                                        Toggle::make('hidden_rank')
                                            ->label(__('filament::resources.inputs.is_hidden'))
                                            ->columnSpanFull(),

                                        Section::make()
                                            ->schema([
                                                Grid::make()
                                                    ->columns([
                                                        'md' => 2,
                                                    ])
                                                    ->schema([
                                                        TextInput::make('auto_credits_amount')
                                                            ->columnSpan(1)
                                                            ->label(__('filament::resources.inputs.auto_credits_amount'))
                                                            ->required(),

                                                        TextInput::make('auto_pixels_amount')
                                                            ->label(__('filament::resources.inputs.auto_pixels_amount'))
                                                            ->required(),

                                                        TextInput::make('auto_gotw_amount')
                                                            ->label(__('filament::resources.inputs.auto_gotw_amount'))
                                                            ->required(),

                                                        TextInput::make('auto_points_amount')
                                                            ->label(__('filament::resources.inputs.auto_points_amount'))
                                                            ->required(),
                                                    ]),
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
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
                    ->alignCenter()
                    ->label(__('filament::resources.columns.image')),

                TextColumn::make('rank_name')
                    ->label(__('filament::resources.columns.name'))
                    ->description(fn (Model $record) => Str::limit($record->description, 40))
                    ->tooltip(function (Model $record): ?string {
                        $description = $record->description;

                        if (strlen($description) <= 40) {
                            return null;
                        }

                        return $description;
                    })
                    ->searchable(),

                TextColumn::make('prefix')
                    ->label(__('filament::resources.columns.prefix'))
                    ->description(fn (Model $record) => $record->prefix_color)
                    ->searchable(),

                ToggleColumn::make('hidden_rank')
                    ->label(__('filament::resources.columns.is_hidden')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
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
            'index' => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'view' => ViewPermission::route('/{record}'),
            'edit' => EditPermission::route('/{record}/edit'),
        ];
    }
}
