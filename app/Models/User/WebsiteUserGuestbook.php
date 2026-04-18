<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $profile_id
 * @property int $user_id
 * @property string $message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $profile
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteUserGuestbook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteUserGuestbook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteUserGuestbook query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteUserGuestbook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteUserGuestbook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteUserGuestbook whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteUserGuestbook whereProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteUserGuestbook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteUserGuestbook whereUserId($value)
 *
 * @mixin \Eloquent
 */
class WebsiteUserGuestbook extends Model
{
    protected $guarded = ['id'];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(User::class, 'profile_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
