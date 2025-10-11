<?php

namespace App\Models\Articles;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class WebsiteArticle extends Model
{
    use HasSlug, SoftDeletes;

    protected $guarded = ['id'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->usingSeparator('-')
            ->allowDuplicateSlugs(false);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(WebsiteArticleReaction::class, 'article_id')
            ->whereActive(true);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(WebsiteArticleComment::class, 'article_id');
    }

    public function userHasReachedArticleCommentLimit(): bool
    {
        return $this->comments()->where('user_id', '=', Auth::id())->count() >= (int) setting('max_comment_per_article');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (empty($model->image)) {
                $model->image = '';
            }
        });
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
