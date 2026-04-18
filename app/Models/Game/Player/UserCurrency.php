<?php

namespace App\Models\Game\Player;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $user_id
 * @property int $type
 * @property int $amount
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCurrency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCurrency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCurrency query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCurrency whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCurrency whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCurrency whereUserId($value)
 *
 * @mixin \Eloquent
 */
class UserCurrency extends Model
{
    protected $guarded = [];

    protected $table = 'users_currency';

    protected $primaryKey = 'user_id';

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
