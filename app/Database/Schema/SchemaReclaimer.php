<?php

namespace App\Database\Schema;

use Illuminate\Database\Connection;
use Illuminate\Support\Collection;

/**
 * Moves colliding tables and columns aside as old_<timestamp>_<name> so the
 * CMS schema can claim the canonical names.
 *
 * MySQL and MariaDB carry data, indexes and foreign keys along when a table
 * or column is renamed, but the index and constraint names themselves stay
 * behind. Foreign key names are schema-unique, so they are renamed explicitly
 * (drop + re-add) to free the conventional names Laravel would generate for
 * the replacement schema.
 */
class SchemaReclaimer
{
    /**
     * One prefix per process, so a single migrate run groups its renames
     * under the same timestamp.
     */
    private static ?string $prefix = null;

    public function __construct(private readonly Connection $connection) {}

    public static function enabled(): bool
    {
        return (bool) config('habbo.migrations.rename_tables');
    }

    public static function prefix(): string
    {
        return self::$prefix ??= sprintf('old_%d_', now()->getTimestamp());
    }

    public function reclaimTable(string $table): void
    {
        // Index names are table-scoped, so only the schema-unique foreign
        // key names need to move aside with the table.
        $this->renameForeignKeys($table);

        $this->connection->statement(sprintf(
            'RENAME TABLE %s TO %s',
            $this->wrap($this->prefixed($table)),
            $this->wrap($this->renamed($this->prefixed($table))),
        ));
    }

    /**
     * @param  list<string>  $columns
     */
    public function reclaimColumns(string $table, array $columns): void
    {
        foreach ($columns as $column) {
            // The re-added constraints and renamed indexes stay attached to
            // the column and follow it through the rename below.
            $this->renameForeignKeys($table, $column);
            $this->renameIndexes($table, $column);

            $this->connection->statement(sprintf(
                'ALTER TABLE %s RENAME COLUMN %s TO %s',
                $this->wrap($this->prefixed($table)),
                $this->wrap($column),
                $this->wrap($this->renamed($column)),
            ));
        }
    }

    private function renameForeignKeys(string $table, ?string $column = null): void
    {
        foreach ($this->foreignKeys($table) as $name => $parts) {
            $first = $parts->first();

            if ($first === null) {
                continue;
            }

            $columns = $parts->pluck('column_name');

            if ($column !== null && ! $columns->contains($column)) {
                continue;
            }

            $this->connection->statement(sprintf(
                'ALTER TABLE %s DROP FOREIGN KEY %s',
                $this->wrap($this->prefixed($table)),
                $this->wrap($name),
            ));

            $this->connection->statement(sprintf(
                'ALTER TABLE %s ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s) ON DELETE %s ON UPDATE %s',
                $this->wrap($this->prefixed($table)),
                $this->wrap($this->renamed($name)),
                $columns->map(fn (string $c) => $this->wrap($c))->implode(', '),
                $this->wrap($first->referenced_table),
                $parts->pluck('referenced_column')->map(fn (string $c) => $this->wrap($c))->implode(', '),
                $first->delete_rule,
                $first->update_rule,
            ));
        }
    }

    private function renameIndexes(string $table, string $column): void
    {
        $indexes = $this->connection->select(
            'SELECT DISTINCT INDEX_NAME AS index_name
                FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND COLUMN_NAME = ?
                AND INDEX_NAME <> ?',
            [$this->prefixed($table), $column, 'PRIMARY'],
        );

        foreach ($indexes as $index) {
            $this->connection->statement(sprintf(
                'ALTER TABLE %s RENAME INDEX %s TO %s',
                $this->wrap($this->prefixed($table)),
                $this->wrap($index->index_name),
                $this->wrap($this->renamed($index->index_name)),
            ));
        }
    }

    /**
     * The table's outgoing foreign keys, keyed by constraint name and ordered
     * by column position so composite keys re-assemble correctly.
     *
     * @return Collection<array-key, Collection<int, object{constraint_name: string, column_name: string, referenced_table: string, referenced_column: string, delete_rule: string, update_rule: string}>>
     */
    private function foreignKeys(string $table): Collection
    {
        /** @var list<object{constraint_name: string, column_name: string, referenced_table: string, referenced_column: string, delete_rule: string, update_rule: string}> $rows */
        $rows = $this->connection->select(
            'SELECT kcu.CONSTRAINT_NAME AS constraint_name,
                    kcu.COLUMN_NAME AS column_name,
                    kcu.REFERENCED_TABLE_NAME AS referenced_table,
                    kcu.REFERENCED_COLUMN_NAME AS referenced_column,
                    rc.DELETE_RULE AS delete_rule,
                    rc.UPDATE_RULE AS update_rule
                FROM information_schema.KEY_COLUMN_USAGE kcu
                JOIN information_schema.REFERENTIAL_CONSTRAINTS rc
                    ON rc.CONSTRAINT_SCHEMA = kcu.CONSTRAINT_SCHEMA
                    AND rc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
                    AND rc.TABLE_NAME = kcu.TABLE_NAME
                WHERE kcu.CONSTRAINT_SCHEMA = DATABASE()
                AND kcu.TABLE_NAME = ?
                AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
                ORDER BY kcu.CONSTRAINT_NAME, kcu.ORDINAL_POSITION',
            [$this->prefixed($table)],
        );

        return collect($rows)->groupBy('constraint_name');
    }

    /**
     * The reclaimed name, kept within MySQL's 64 character identifier limit
     * with a stable hash suffix when truncation is unavoidable.
     */
    private function renamed(string $name): string
    {
        $renamed = self::prefix() . $name;

        if (strlen($renamed) > 64) {
            $renamed = substr($renamed, 0, 55) . '_' . substr(md5($name), 0, 8);
        }

        return $renamed;
    }

    private function prefixed(string $table): string
    {
        return $this->connection->getTablePrefix() . $table;
    }

    private function wrap(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}
