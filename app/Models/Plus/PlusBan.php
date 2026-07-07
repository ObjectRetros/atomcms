<?php

namespace App\Models\Plus;

use Illuminate\Database\Eloquent\Model;

/**
 * A ban row on the Plus EMU schema: bantype ('user', 'ip' or 'machine') with
 * the target - username, address or machine id - in the value column.
 *
 * @property int $id
 * @property string $bantype
 * @property string $value
 * @property string $reason
 * @property int $expire Unix timestamp; 0 means never
 * @property string $added_by
 * @property int $added_date Unix timestamp
 */
class PlusBan extends Model
{
    protected $table = 'bans';

    protected $fillable = [
        'bantype',
        'value',
        'reason',
        'expire',
        'added_by',
        'added_date',
    ];

    public $timestamps = false;
}
