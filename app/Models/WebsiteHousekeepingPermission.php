<?php

namespace App\Models;

use App\Models\Game\Permission;
use Illuminate\Database\Eloquent\Model;

class WebsiteHousekeepingPermission extends Model
{
    protected $guarded = ['id'];

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'min_rank');
    }
}

