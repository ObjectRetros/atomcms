<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $referrals_total
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReferral newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReferral newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReferral query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReferral whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReferral whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReferral whereReferralsTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReferral whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReferral whereUserId($value)
 *
 * @mixin \Eloquent
 */
class UserReferral extends Model
{
    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
