<?php

namespace App\Filament\Resources\Audit;

use App\Filament\Resources\Audit\UserBanResource\Pages;
use App\Models\Audit\UserAudit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Rmsramos\Activitylog\Resources\ActivitylogResource;

class UserBanResource extends ActivitylogResource
{
	public static function table(Table $table): Table
    {
        return $table
            ->columns([
                static::getLogNameColumnCompoment()
					->toggleable(true),
                static::getEventColumnCompoment(),
                static::getSubjectTypeColumnCompoment(),
                TextColumn::make('causer.name')
                    ->label(__('activitylog::tables.columns.causer.label'))
                    ->getStateUsing(function (Model $record) {
                        if ($record->causer_id == null) {
                            return new HtmlString('&mdash;');
                        }

                        return $record->causer->username;
                    })
                    ->searchable(),
                static::getPropertiesColumnCompoment(),
                static::getCreatedAtColumnCompoment(),
            ])
            ->defaultSort(config('filament-activitylog.resources.default_sort_column', 'created_at'), config('filament-activitylog.resources.default_sort_direction', 'asc'))
            ->filters([
                static::getDateFilterComponent(),
                static::getEventFilterCompoment(),
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
            'index' => Pages\ListUserBan::route('/'),
			'view' => Pages\ViewBan::route('/{record}'),
        ];
    }
}
