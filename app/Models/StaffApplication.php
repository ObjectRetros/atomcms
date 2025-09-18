<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffApplication extends Model
{
    protected $table = 'website_staff_applications';
    
    protected $fillable = [
        'user_id',
        'rank_id',
        'content',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Game\Permission::class, 'rank_id');
    }
}