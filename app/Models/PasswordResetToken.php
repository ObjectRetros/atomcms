<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $email
 * @property int $token
 * @property Carbon $created_at
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PasswordResetToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PasswordResetToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PasswordResetToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PasswordResetToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PasswordResetToken whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PasswordResetToken whereToken($value)
 *
 * @mixin \Eloquent
 */
class PasswordResetToken extends Model
{
    protected $primaryKey = 'token';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['email', 'token', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // timestamps = true, but we don't have "UPDATED_AT". To prevent an error, we set the default value to `null`.
    public const UPDATED_AT = null;

    /**
     * Tokens are stored hashed so a database read cannot reveal a usable reset link.
     */
    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    public function hasExpired(): bool
    {
        return $this->created_at
            ->addMinutes((int) config('habbo.password_reset_token_time'))
            ->isPast();
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'mail');
    }
}
