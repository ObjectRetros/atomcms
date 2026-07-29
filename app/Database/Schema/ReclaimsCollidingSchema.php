<?php

namespace App\Database\Schema;

use Closure;
use Illuminate\Database\Schema\ColumnDefinition;
use RuntimeException;

/**
 * Intercepts schema changes that would collide with tables or columns left
 * behind by the emulator database or a previous CMS. With the rename flag on
 * the collision is moved aside as old_<timestamp>_<name>; with it off the
 * raw SQL error is replaced by an actionable message.
 */
trait ReclaimsCollidingSchema
{
    public function create($table, Closure $callback)
    {
        if ($this->hasTable($table)) {
            $this->guardCollision(sprintf(
                "Table '%s' already exists, usually left behind by your emulator database or a previous CMS. "
                . "Set RENAME_COLLIDING_TABLES=true in your .env to let Atom move it aside as '%s%s', "
                . 'or remove it manually, then rerun the migration.',
                $table,
                SchemaReclaimer::prefix(),
                $table,
            ));

            (new SchemaReclaimer($this->connection))->reclaimTable($table);
        }

        parent::create($table, $callback);
    }

    public function table($table, Closure $callback)
    {
        $blueprint = $this->createBlueprint($table, $callback);

        $added = array_map(
            fn (ColumnDefinition $column) => (string) $column->get('name'),
            array_values($blueprint->getAddedColumns()),
        );

        if ($added !== [] && $this->hasTable($table)) {
            $colliding = array_values(array_intersect($added, $this->getColumnListing($table)));

            if ($colliding !== []) {
                $this->guardCollision(sprintf(
                    "Column '%s.%s' already exists, usually left behind by your emulator database or a previous CMS. "
                    . "Set RENAME_COLLIDING_TABLES=true in your .env to let Atom move it aside as '%s%s', "
                    . 'or remove it manually, then rerun the migration.',
                    $table,
                    $colliding[0],
                    SchemaReclaimer::prefix(),
                    $colliding[0],
                ));

                (new SchemaReclaimer($this->connection))->reclaimColumns($table, $colliding);
            }
        }

        $this->build($blueprint);
    }

    private function guardCollision(string $message): void
    {
        if (! SchemaReclaimer::enabled()) {
            throw new RuntimeException($message);
        }
    }
}
