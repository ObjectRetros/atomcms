<?php

namespace App\Database\Schema;

use Illuminate\Database\Schema\MySqlBuilder;

class CollisionAwareMySqlBuilder extends MySqlBuilder
{
    use ReclaimsCollidingSchema;
}
