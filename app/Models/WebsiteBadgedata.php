<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteBadgedata extends Model
{
    use HasFactory;

    protected $table = 'website_badgedata';

    protected $fillable = [
        'badge_key',
        'badge_name',
        'badge_description',
    ];
}