<?php

namespace App\Models\Miscellaneous;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBetaCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBetaCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBetaCode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBetaCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBetaCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBetaCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBetaCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBetaCode whereUserId($value)
 *
 * @mixin \Eloquent
 */
class WebsiteBetaCode extends Model
{
    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
