<?php

namespace App\Models\Articles;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $article_id
 * @property string $reaction
 * @property int $active
 * @property-read WebsiteArticle|null $article
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticleReaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticleReaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticleReaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticleReaction whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticleReaction whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticleReaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticleReaction whereReaction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticleReaction whereUserId($value)
 *
 * @mixin \Eloquent
 */
class WebsiteArticleReaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected $hidden = [
        'user_id',
        'article_id',
    ];

    public static function getReaction(int $articleId, int $userId, string $reaction): ?self
    {
        return self::where('user_id', $userId)
            ->where('article_id', $articleId)
            ->where('reaction', $reaction)
            ->first();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->user_id = auth()->id();
        });
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(WebsiteArticle::class, 'article_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
