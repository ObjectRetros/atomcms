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

    protected $fillable = [
        'article_id',
        'user_id',
        'reaction',
        'active',
    ];

    public $timestamps = false;

    protected $hidden = [
        'user_id',
        'article_id',
    ];

    public static function toggleFor(WebsiteArticle $article, User $user, string $reaction): self
    {
        $record = self::query()->firstOrCreate([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'reaction' => $reaction,
        ], [
            'active' => true,
        ]);

        if (! $record->wasRecentlyCreated) {
            $record->update(['active' => ! $record->active]);
        }

        return $record;
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
