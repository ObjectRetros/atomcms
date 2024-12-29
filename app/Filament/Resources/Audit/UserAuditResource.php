<?php

namespace App\Filament\Resources\Audit;

use App\Filament\Resources\Audit\UserAuditResource\Pages;
use App\Models\Audit\UserAudit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Rmsramos\Activitylog\Resources\ActivitylogResource;

class UserAuditResource extends ActivitylogResource
{
	
	public static function table(Table $table): Table
    {
        return $table
            ->columns([
                static::getLogNameColumnCompoment()
					->toggleable(true),
                static::getEventColumnCompoment(),
                static::getSubjectTypeColumnCompoment(),
                static::getCauserNameColumnCompoment(),
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
            'index' => Pages\ListUserAudits::route('/'),
			'view' => Pages\ViewAudit::route('/{record}'),
        ];
    }
}
