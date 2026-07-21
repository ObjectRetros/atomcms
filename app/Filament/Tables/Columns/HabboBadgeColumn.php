<?php

namespace App\Filament\Tables\Columns;

use App\Models\Compositions\HasBadge;
use Filament\Tables\Columns\Column;

class HabboBadgeColumn extends Column implements HasBadge
{
    protected string $view = 'filament.tables.columns.habbo-badge-column';

    public function getBadgePath(): string
    {
        $record = $this->getRecord();

        if (! $record instanceof HasBadge) {
            return '';
        }

        return $record->getBadgePath();
    }

    public function getBadgeName(): string
    {
        $record = $this->getRecord();

        if (! $record instanceof HasBadge) {
            return '';
        }

        return $record->getBadgeName();
    }
}
