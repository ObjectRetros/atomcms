<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetToken extends Model
{
    protected $primaryKey = 'token';

    protected $fillable = ['email', 'token', 'created_at'];

    protected $casts = [
        'created_at' => 'date',
    ];

    // timestamps = true, but we don't have "UPDATED_AT". To prevent an error, we set the default value to `null`.
    const UPDATED_AT = null;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'mail');
    }
}
