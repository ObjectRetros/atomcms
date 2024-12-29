<?php

namespace App\Models;

use App\Enums\NotificationType;
use App\Models\Compositions\HasNotificationUrl;
use App\Models\Article\{ArticleComment, ArticleReaction};
use Auth;
use Illuminate\Database\Eloquent\{Model, Builder, Casts\Attribute, Relations\HasMany, Factories\HasFactory,};
use Str;

class Article extends Model
{
    use HasFactory;
    use HasNotificationUrl;

    protected $guarded = [];

    protected $table = 'website_articles';

    protected $casts = [
        'visible' => 'boolean',
        'fixed' => 'boolean',
        'allow_comments' => 'boolean',
        'is_promotion' => 'boolean',
        'promotion_ends_at' => 'datetime'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (Article $article) {
            $article->user_id = Auth::id();
            $article->slug = Str::slug($article->title);
            $article->predominant_color = getPredominantImageColor($article->image);
        });

        static::updating(function (Article $article) {
            $article->slug = Str::slug($article->title);

            if ($article->isDirty('image')) {
                $article->predominant_color = getPredominantImageColor($article->image);
            }
        });
    }

    public function syncPaginatedComments(): void
    {
        $this->setRelation('comments',
            $this->comments()->defaultRelationships()->paginate(10)->fragment('comments')
        );
    }

    public static function fromIdAndSlug(string $id, string $slug, bool $withDefaultRelationships = true): Builder
    {
        return Article::valid()
            ->when($withDefaultRelationships, fn($query) => $query->defaultRelationships())
            ->whereId($id)
            ->whereSlug($slug);
    }

    public static function getLatestValidArticle(bool $withDefaultRelationships = true): ?Article
    {
        $article = Article::valid()
            ->when($withDefaultRelationships, fn($query) => $query->defaultRelationships())
            ->latest()
            ->first();

        if (!$article) return null;

        $article->syncPaginatedComments();

        return $article;
    }

    public static function forIndex(int $limit): Builder
    {
        return Article::valid()
            ->with(['user:id,username,look,avatar_background'])
            ->select(['id', 'user_id', 'title', 'slug', 'is_promotion', 'image', 'description', 'promotion_ends_at', 'created_at', 'fixed'])
            ->limit($limit)
            ->latest();
    }

    public function scopeValid(Builder $query): void
    {
        $query->whereVisible(true);
    }

    public function scopeDefaultRelationships(Builder $query): void
    {
        $query->with([
            'user:id,username,look,gender',
            'tags',
            'reactions' => fn($query) => $query->defaultRelationships(),
            'user.followers'
        ]);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ArticleComment::class)->defaultBehavior();
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(ArticleReaction::class)->defaultBehavior();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function titleColor(): Attribute
    {
        return new Attribute(
            get: fn() => isDarkColor($this->predominant_color) ? '#fff' : '#000'
        );
    }

    public function createFollowersNotification(): void
    {
        $this->user->followers()
            ->with('user:id,username')
            ->each(fn(AuthorNotification $follower) => $follower->user->notify($this->user, NotificationType::ArticlePosted, $this->getNotificationUrl())
            );
    }
}
