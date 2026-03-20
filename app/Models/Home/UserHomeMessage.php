<?php

namespace App\Models\Home;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserHomeMessage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function scopeDefaultUserData(Builder $query): void
    {
        $query->with('user:id,username,look,online');
    }

    public function renderedContent(): Attribute
    {
        return new Attribute(
            get: fn (): string => e($this->content),
        );
    }
}
