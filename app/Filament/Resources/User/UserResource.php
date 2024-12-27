<?php

namespace App\Filament\Resources\User;

use App\Models\Community\Staff\WebsiteTeam;
use App\Models\Game\Permission;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Traits\TranslatableResource;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Tables\Columns\UserAvatarColumn;
use App\Filament\Resources\User\UserResource\Pages;
use App\Filament\Resources\User\UserResource\RelationManagers;

class UserResource extends Resource
{
    use TranslatableResource;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $slug = 'user-management/users';

    public static string $translateIdentifier = 'users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Main')
                    ->tabs([
                        Tab::make(__('filament::resources.tabs.General Information'))
                            ->schema([
                                TextInput::make('username')
                                    ->label(__('filament::resources.inputs.username'))
                                    ->required()
                                    ->disabled()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(25),

                                TextInput::make('motto')
                                    ->label(__('filament::resources.inputs.motto'))
                                    ->required()
                                    ->maxLength(127),

                                Select::make('gender')
                                    ->native(false)
                                    ->label(__('filament::resources.inputs.gender'))
                                    ->options([
                                        'M' => __('filament::resources.common.Male'),
                                        'F' => __('filament::resources.common.Female')
                                    ])
                                    ->required(),

                                DateTimePicker::make('account_created')
                                    ->native(false)
                                    ->displayFormat('Y-m-d H:i:s')
                                    ->dehydrateStateUsing(fn(Model $record) => $record->account_created)
                                    ->disabled()
                                    ->label(__('filament::resources.inputs.created_at')),

                                DateTimePicker::make('last_login')
                                    ->native(false)
                                    ->displayFormat('Y-m-d H:i:s')
                                    ->dehydrateStateUsing(fn(Model $record) => $record->last_login)
                                    ->disabled()
                                    ->label(__('filament::resources.inputs.last_login')),

                                DateTimePicker::make('last_online')
                                    ->native(false)
                                    ->displayFormat('Y-m-d H:i:s')
                                    ->dehydrateStateUsing(fn(Model $record) => $record->last_online)
                                    ->disabled()
                                    ->label(__('filament::resources.inputs.last_online')),

                                TextInput::make('ip_register')
                                    ->label(__('filament::resources.inputs.ip_register'))
                                    ->disabled(),

                                TextInput::make('ip_current')
                                    ->label(__('filament::resources.inputs.ip_current'))
                                    ->disabled(),

                                TextInput::make('referral_code')
                                    ->label(__('filament::resources.inputs.referral_code'))
                                    ->disabled(),

                                TextInput::make('referrer_code')
                                    ->label(__('filament::resources.inputs.referrer_code'))
                                    ->nullable()
                                    ->maxLength(15),

                                Select::make('team_id')
                                    ->native(false)
                                    ->label(__('filament::resources.inputs.team_id'))
                                    ->options(WebsiteTeam::all()->pluck('rank_name', 'id'))
                                    ->columnSpanFull()
                            ])->columns(['sm' => 2]),

                        Tab::make(__('filament::resources.tabs.Currencies'))
                            ->schema([
                                TextInput::make('credits')
                                    ->label(__('filament::resources.common.Credits'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->columnSpanFull(),

                                TextInput::make('currency_0')
                                    ->label(__('filament::resources.common.Duckets'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->columnSpanFull(),

                                TextInput::make('currency_5')
                                    ->label(__('filament::resources.common.Diamonds'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->columnSpanFull(),

                                TextInput::make('currency_101')
                                    ->label(__('filament::resources.common.Points'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->columnSpanFull(),
                            ])
                            ->columns(['sm' => 2]),

                        Tab::make(__('filament::resources.tabs.Security'))
                            ->schema([
                                Section::make(__('filament::resources.tabs.Change Username'))
                                    ->description(__('filament::resources.helpers.change_username_description'))
                                    ->schema([
                                        Toggle::make('allow_change_username')
                                            ->label(__('filament::resources.inputs.allow_change_username'))
                                    ])->collapsible()->collapsed(),

                                Section::make(__('filament::resources.tabs.Change Email'))
                                    ->schema([
                                        TextInput::make('mail')
                                            ->label(__('filament::resources.inputs.email'))
                                            ->email()
                                            ->required()
                                    ])->collapsible()->collapsed(),

                                Section::make(__('filament::resources.tabs.Change Password'))
                                    ->description(__('filament::resources.helpers.change_password_description'))
                                    ->schema([
                                        TextInput::make('password')
                                            ->label(__('filament::resources.inputs.new_password'))
                                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                                            ->dehydrated(fn($state) => filled($state))
                                            ->password()
                                            ->confirmed(),

                                        TextInput::make('password_confirmation')
                                            ->label(__('filament::resources.inputs.new_password_confirmation'))
                                            ->dehydrated(false)
                                            ->password(),
                                    ])->collapsible()
                                    ->columns(['sm' => 2])
                                    ->collapsed(),

                                Section::make(__('filament::resources.tabs.Change Rank'))
                                    ->schema([
                                        Select::make('rank')
                                            ->native(false)
                                            ->label(__('filament::resources.inputs.rank'))
                                            ->options(Permission::where('id', '<', auth()->user()->rank)->get()->pluck('rank_name', 'id')),

                                        Toggle::make('is_hidden')
                                            ->label(__('filament::resources.inputs.is_hidden'))
                                            ->default(false)
                                    ])->collapsible()
                                    ->collapsed(),
                            ])
                    ])->columnSpanFull()
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament::resources.columns.id'))
                    ->searchable(),

                UserAvatarColumn::make('avatar')
                    ->toggleable()
                    ->label(__('filament::resources.columns.avatar'))
                    ->options('&size=m&head_direction=3&gesture=sml&headonly=1'),

                TextColumn::make('username')
                    ->label(__('filament::resources.columns.username'))
                    ->searchable(),

                TextColumn::make('mail')
                    ->label(__('filament::resources.columns.email'))
                    ->toggleable()
                    ->searchable()
                    ->limit(50),

                TextColumn::make('motto')
                    ->label(__('filament::resources.columns.motto'))
                    ->toggleable()
                    ->limit(30)
                    ->searchable(),

                IconColumn::make('online')
                    ->label(__('filament::resources.columns.online'))
                    ->icon(fn(Model $record) => $record->online ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->colors([
                        'danger' => false,
                        'success' => true,
                    ]),

                TextColumn::make('account_created')
                    ->toggleable()
                    ->date('Y-m-d H:i')
                    ->label(__('filament::resources.columns.created_at'))
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SettingsRelationManager::class,
            RelationManagers\BadgesRelationManager::class,
            RelationManagers\ChatLogRelationManager::class,
            RelationManagers\ChatLogPrivateRelationManager::class,
        ];
    }

    public static function fillWithOutsideData(Model $record, array $formData): array
    {
        $formData['currency_0'] = $record->currency('duckets');
        $formData['currency_5'] = $record->currency('diamonds');
        $formData['currency_101'] = $record->currency('points');

        if ($record->settings) {
            $formData['allow_change_username'] = $record->settings->can_change_name;
        }

        return $formData;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
