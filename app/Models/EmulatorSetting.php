<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $key
 * @property string $value
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmulatorSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmulatorSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmulatorSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmulatorSetting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmulatorSetting whereValue($value)
 *
 * @mixin \Eloquent
 */
class EmulatorSetting extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $primaryKey = 'key';

    public $incrementing = false;
}
