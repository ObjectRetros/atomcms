<?php

namespace App\Models\Home;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $recipient_user_id
 * @property string $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $rendered_content
 * @property-read User|null $recipientUser
 * @property-read User|null $user
 */
class UserHomeMessage extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory;

    protected $guarded = [];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<User, $this> */
    public function recipientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    /** @param Builder<static> $query */
    #[Scope]
    protected function defaultUserData(Builder $query): void
    {
        $query->with('user:id,username,look,online');
    }

    /** @return Attribute<string, never> */
    public function renderedContent(): Attribute
    {
        return new Attribute(
            get: fn (): string => e($this->content),
        );
    }
}
