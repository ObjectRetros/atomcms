<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $key
 * @property string $value
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmulatorText newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmulatorText newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmulatorText query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmulatorText whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmulatorText whereValue($value)
 *
 * @mixin \Eloquent
 */
class EmulatorText extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected $primaryKey = 'key';

    public $incrementing = false;
}
