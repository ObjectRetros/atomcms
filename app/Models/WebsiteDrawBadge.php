<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteDrawBadge extends Model
{
    protected $table = 'website_drawbadges';

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
