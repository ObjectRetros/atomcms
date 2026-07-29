<?php

namespace App\Database\Schema;

use Illuminate\Database\Schema\MariaDbBuilder;

class CollisionAwareMariaDbBuilder extends MariaDbBuilder
{
    use ReclaimsCollidingSchema;
}
