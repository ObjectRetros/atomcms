<?php

namespace App\Filament\Concerns;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;

/**
 * Identifies table rows for models whose tables have no primary key (the
 * emulator log tables). Rows are keyed by a hash of their raw attributes and
 * resolved back from the currently loaded page of records.
 */
trait HasKeylessTableRecords
{
    /**
     * @param  Model | array<string, mixed>  $record
     */
    public function getTableRecordKey(Model|array $record): string
    {
        if (is_array($record)) {
            return parent::getTableRecordKey($record);
        }

        return md5((string) json_encode($record->getRawOriginal()));
    }

    /**
     * @return Model | array<string, mixed> | null
     */
    protected function resolveTableRecord(?string $key): Model|array|null
    {
        if ($key === null) {
            return null;
        }

        $records = $this->getTableRecords();

        if ($records instanceof Paginator || $records instanceof CursorPaginator) {
            $records = collect($records->items());
        }

        foreach ($records as $record) {
            if ($this->getTableRecordKey($record) === $key) {
                return $record;
            }
        }

        return null;
    }
}
