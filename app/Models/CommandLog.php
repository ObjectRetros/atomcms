<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $user_id
 * @property Carbon $timestamp
 * @property string $command
 * @property string $params
 * @property string $succes
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommandLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommandLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommandLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommandLog whereCommand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommandLog whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommandLog whereSucces($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommandLog whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommandLog whereUserId($value)
 *
 * @mixin \Eloquent
 */
class CommandLog extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory;

    protected $table = 'commandlogs';

    // commandlogs has no primary key, so keyed operations are unsupported.
    protected $primaryKey = null;

    public $incrementing = false;

    protected $guarded = [];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'timestamp' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
