<?php

namespace App\Database;

use App\Database\Schema\CollisionAwareMySqlBuilder;
use Illuminate\Database\MySqlConnection;

class CollisionAwareMySqlConnection extends MySqlConnection
{
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new CollisionAwareMySqlBuilder($this);
    }
}
