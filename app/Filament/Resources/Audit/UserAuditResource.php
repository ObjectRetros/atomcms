<?php

namespace App\Filament\Resources\Audit;

use App\Filament\Resources\Audit\UserAuditResource\Pages;
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

class UserAuditResource extends ActivitylogResource
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                static::getLogNameColumnComponent()
                    ->toggleable(true),
                static::getEventColumnComponent(),
                static::getSubjectTypeColumnComponent(),
                TextColumn::make('causer.name')
                    ->label(__('activitylog::tables.columns.causer.label'))
                    ->getStateUsing(function (Model $record) {
                        if ($record->causer_id == null) {
                            return new HtmlString('—');
                        }

                        return $record->causer->username;
                    })
                    ->searchable(),
                static::getPropertiesColumnComponent(),
                static::getCreatedAtColumnComponent(),
            ])
            ->defaultSort(config('filament-activitylog.resources.default_sort_column', 'created_at'), config('filament-activitylog.resources.default_sort_direction', 'asc'))
            ->filters([
                static::getDateFilterComponent(),
                static::getEventFilterComponent(),
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
            'index' => Pages\ListUserAudits::route('/'),
            'view' => Pages\ViewAudit::route('/{record}'),
        ];
    }
}