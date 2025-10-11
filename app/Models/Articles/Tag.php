<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function websiteArticles()
    {
        return $this->morphedByMany(WebsiteArticle::class, 'taggable');
    }
}
