<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmulatorSetting extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $primaryKey = 'key';

    public $incrementing = false;
}
