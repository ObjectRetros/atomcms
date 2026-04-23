<?php

namespace App\Models\Articles;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

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
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, WebsiteArticleComment> $comments
 * @property-read int|null $comments_count
 * @property-read Collection<int, WebsiteArticleReaction> $reactions
 * @property-read int|null $reactions_count
 * @property-read Collection<int, Tag> $tags
 * @property-read int|null $tags_count
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticle onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticle whereCanComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticle whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticle whereFullStory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticle whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticle whereShortStory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticle whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticle whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticle whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticle withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticle withoutTrashed()
 *
 * @mixin \Eloquent
 */
class WebsiteArticle extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    protected $guarded = ['id'];

    protected $table = 'website_articles';

    protected $casts = [
        'can_comment' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public static function fromIdAndSlug(string $id, string $slug, bool $withDefaultRelationships = true): Builder
    {
        return self::valid()
            ->when($withDefaultRelationships, fn ($query) => $query->defaultRelationships())
            ->whereKey($id)
            ->where('slug', $slug);
    }

    public static function getLatestValidArticle(bool $withDefaultRelationships = true): ?self
    {
        $article = self::valid()
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
        return self::valid()
            ->with(['user:id,username,look,avatar_background'])
            ->select(['id', 'user_id', 'title', 'slug', 'image', 'short_story', 'created_at', 'can_comment'])
            ->limit($limit)
            ->latest();
    }

    public function syncPaginatedComments(): void
    {
        $this->setRelation('comments',
            $this->comments()->paginate(10)->fragment('comments'),
        );
    }

    public function scopeValid(Builder $query): void
    {
        $query->whereNull($this->getQualifiedDeletedAtColumn());
    }

    public function scopeDefaultRelationships(Builder $query): void
    {
        $query->with([
            'user:id,username,look,gender',
            'tags',
            'reactions',
        ]);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->usingSeparator('-')
            ->allowDuplicateSlugs();
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

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
