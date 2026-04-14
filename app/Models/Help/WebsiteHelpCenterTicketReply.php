<?php

namespace App\Models\Help;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Stevebauman\Purify\Facades\Purify;

/**
 * @property int $id
 * @property int $ticket_id
 * @property int $user_id
 * @property string $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read WebsiteHelpCenterTicket $ticket
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicketReply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicketReply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicketReply query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicketReply whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicketReply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicketReply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicketReply whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicketReply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterTicketReply whereUserId($value)
 *
 * @mixin \Eloquent
 */
class WebsiteHelpCenterTicketReply extends Model
{
    protected $guarded = ['id'];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(WebsiteHelpCenterTicket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function canDeleteReply()
    {
        return $this->user_id === Auth::id() || hasPermission('delete_website_ticket_replies');
    }

    public function getContentAttribute($value)
    {
        return Purify::clean($value);
    }
}
