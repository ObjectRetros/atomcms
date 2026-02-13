<?php

namespace App\Models\Help;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Gate;
use Stevebauman\Purify\Facades\Purify;

class WebsiteHelpCenterTicket extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'open' => 'boolean',
        ];
    }

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

    public function canManageTicket(): bool
    {
        return Gate::allows('update', $this);
    }

    public function canDeleteTicket(): bool
    {
        return Gate::allows('delete', $this);
    }

    public function isOpen(): bool
    {
        return (bool) $this->open;
    }

    public function getContentAttribute($value)
    {
        return Purify::clean($value);
    }
}
