<?php

namespace App\Models\Articles;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property int $article_id
 * @property int $user_id
 * @property string $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read WebsiteArticle|null $article
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticleComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticleComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticleComment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticleComment whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticleComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticleComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticleComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticleComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteArticleComment whereUserId($value)
 *
 * @mixin \Eloquent
 */
class WebsiteArticleComment extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function article(): BelongsTo
    {
        return $this->belongsTo(WebsiteArticle::class, 'article_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function canBeDeleted(): bool
    {
        return $this->user_id === Auth::id() || hasPermission('delete_article_comments');
    }
}
