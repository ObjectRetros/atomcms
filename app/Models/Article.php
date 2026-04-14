<?php

namespace App\Models;

use App\Models\Articles\Tag;
use App\Models\Articles\WebsiteArticleComment;
use App\Models\Articles\WebsiteArticleReaction;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Str;

/**
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string $short_story
 * @property string $full_story
 * @property int|null $user_id
 * @property string $image
 * @property int $can_comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read User|null $user
 *
 * @method static Builder<static>|Article defaultRelationships()
 * @method static Builder<static>|Article newModelQuery()
 * @method static Builder<static>|Article newQuery()
 * @method static Builder<static>|Article query()
 * @method static Builder<static>|Article valid()
 * @method static Builder<static>|Article whereCanComment($value)
 * @method static Builder<static>|Article whereCreatedAt($value)
 * @method static Builder<static>|Article whereDeletedAt($value)
 * @method static Builder<static>|Article whereFullStory($value)
 * @method static Builder<static>|Article whereId($value)
 * @method static Builder<static>|Article whereImage($value)
 * @method static Builder<static>|Article whereShortStory($value)
 * @method static Builder<static>|Article whereSlug($value)
 * @method static Builder<static>|Article whereTitle($value)
 * @method static Builder<static>|Article whereUpdatedAt($value)
 * @method static Builder<static>|Article whereUserId($value)
 *
 * @property-read Collection<int, Tag> $tags
 * @property-read int|null $tags_count
 *
 * @mixin \Eloquent
 */
class Article extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'website_articles';

    protected $casts = [
        'visible' => 'boolean',
        'fixed' => 'boolean',
        'allow_comments' => 'boolean',
        'is_promotion' => 'boolean',
        'promotion_ends_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (Article $article) {
            $article->user_id = Auth::id();
            $article->slug = Str::slug($article->title);
        });

        static::updating(function (Article $article) {
            $article->slug = Str::slug($article->title);
        });
    }

    public function syncPaginatedComments(): void
    {
        $this->setRelation('comments',
            $this->comments()->paginate(10)->fragment('comments'),
        );
    }

    public static function fromIdAndSlug(string $id, string $slug, bool $withDefaultRelationships = true): Builder
    {
        return Article::valid()
            ->when($withDefaultRelationships, fn ($query) => $query->defaultRelationships())
            ->whereId($id)
            ->whereSlug($slug);
    }

    public static function getLatestValidArticle(bool $withDefaultRelationships = true): ?Article
    {
        $article = Article::valid()
            ->when($withDefaultRelationships, fn ($query) => $query->defaultRelationships())
            ->latest()
            ->first();

        if (! $article) {
            return null;
        }

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
            'reactions',
        ]);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(WebsiteArticleComment::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(WebsiteArticleReaction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
