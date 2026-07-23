<?php

namespace App\Models\Home;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $rated_user_id
 * @property int $rating
 * @property-read User|null $ratedUser
 * @property-read User|null $user
 *
 * Aggregate columns produced by loadRatingsForHome()'s selectRaw:
 * @property float|int|string|null $rating_avg
 * @property int $total
 * @property int $most_positive
 */
class UserHomeRating extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<User, $this> */
    public function ratedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_user_id');
    }
}
