<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

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
