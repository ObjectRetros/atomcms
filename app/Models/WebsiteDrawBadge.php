<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteDrawBadge extends Model
{
    protected $table = 'website_drawbadges';

    protected $fillable = [
        'user_id',
        'badge_path',
        'badge_url',
        'badge_name',
        'badge_desc',
        'published',
    ];
	
	public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}