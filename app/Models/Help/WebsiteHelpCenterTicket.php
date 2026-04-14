<?php

namespace App\Models\Help;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Stevebauman\Purify\Facades\Purify;

/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $category_id
 * @property string $title
 * @property string $content
 * @property int $open
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read WebsiteHelpCenterCategory|null $category
 * @property-read Collection<int, WebsiteHelpCenterTicketReply> $replies
 * @property-read int|null $replies_count
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicket whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicket whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicket whereOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicket whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicket whereUserId($value)
 *
 * @mixin \Eloquent
 */
class WebsiteHelpCenterTicket extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(WebsiteHelpCenterCategory::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(WebsiteHelpCenterTicketReply::class, 'ticket_id');
    }

    public function canDeleteTicket()
    {
        return $this->user_id === Auth::id() || hasPermission('delete_website_tickets');
    }

    public function canManageTicket()
    {
        return $this->user_id === Auth::id() || hasPermission('manage_website_tickets');
    }

    public function canCloseTicket()
    {
        return $this->user_id === Auth::id() || hasPermission('manage_website_tickets');
    }

    public function isOpen()
    {
        return $this->open || hasPermission('manage_website_tickets');
    }

    public function getContentAttribute($value)
    {
        return Purify::clean($value);
    }
}
