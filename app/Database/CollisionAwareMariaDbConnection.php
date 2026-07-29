<?php

namespace App\Database;

use App\Database\Schema\CollisionAwareMariaDbBuilder;
use Illuminate\Database\MariaDbConnection;

class CollisionAwareMariaDbConnection extends MariaDbConnection
{
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new CollisionAwareMariaDbBuilder($this);
    }
}
