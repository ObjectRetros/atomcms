<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $ip_address
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClaimedReferralLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClaimedReferralLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClaimedReferralLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClaimedReferralLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClaimedReferralLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClaimedReferralLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClaimedReferralLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClaimedReferralLog whereUserId($value)
 *
 * @mixin \Eloquent
 */
class ClaimedReferralLog extends Model
{
    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
