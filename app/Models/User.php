<?php

namespace App\Models;

use App\Models\Articles\WebsiteArticle;
use App\Models\Articles\WebsiteArticleComment;
use App\Models\Community\Staff\WebsiteStaffApplications;
use App\Models\Community\Staff\WebsiteTeam;
use App\Models\Game\Furniture\Item;
use App\Models\Game\Permission;
use App\Models\Game\Player\UserSetting;
use App\Models\Game\Player\UserSubscription;
use App\Models\Game\Room;
use App\Models\Miscellaneous\CameraWeb;
use App\Models\Miscellaneous\WebsiteBetaCode;
use App\Models\Shop\WebsitePaypalTransaction;
use App\Models\Shop\WebsiteUsedShopVoucher;
use App\Models\Traits\HasBadges;
use App\Models\Traits\HasCurrency;
use App\Models\Traits\HasFriends;
use App\Models\Traits\HasReferrals;
use App\Models\Traits\HasTickets;
use App\Models\User\Ban;
use App\Models\User\WebsiteUserGuestbook;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens, HasBadges, HasCurrency, HasFactory, HasFriends, HasReferrals, HasTickets, LogsActivity, Notifiable, TwoFactorAuthenticatable;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $hidden = ['id', 'password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'hidden_staff' => 'boolean',
            'online' => 'boolean',
        ];
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function permission(): HasOne
    {
        return $this->hasOne(Permission::class, 'id', 'rank');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(WebsiteArticle::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'owner_id');
    }

    public function ban(): HasOne
    {
        return $this->hasOne(Ban::class, 'user_id')
            ->where('ban_expire', '>', time())
            ->whereIn('type', ['account', 'super']);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    public function betaCode(): HasOne
    {
        return $this->hasOne(WebsiteBetaCode::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(WebsiteTeam::class, 'team_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(WebsiteStaffApplications::class, 'user_id');
    }

    public function hcSubscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class);
    }

    public function articleComments(): HasMany
    {
        return $this->hasMany(WebsiteArticleComment::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WebsitePaypalTransaction::class);
    }

    public function usedShopVouchers(): HasMany
    {
        return $this->hasMany(WebsiteUsedShopVoucher::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'user_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(CameraWeb::class);
    }

    public function profileGuestbook(): HasMany
    {
        return $this->hasMany(WebsiteUserGuestbook::class, 'profile_id');
    }

    public function guestbook(): HasMany
    {
        return $this->hasMany(WebsiteUserGuestbook::class, 'user_id');
    }

    public function chatLogs(): HasMany
    {
        return $this->hasMany(ChatlogRoom::class, 'user_from_id');
    }

    public function chatLogsPrivate(): HasMany
    {
        return $this->hasMany(ChatlogPrivate::class, 'user_from_id');
    }

    public function confirmTwoFactorAuthentication($code): bool
    {
        $codeIsValid = app(TwoFactorAuthenticationProvider::class)
            ->verify(decrypt($this->two_factor_secret), $code);

        if (! $codeIsValid) {
            return false;
        }

        $this->update(['two_factor_confirmed' => true]);

        return true;
    }

    public function hasAppliedForPosition(int $rankId): bool
    {
        return $this->applications()->where('rank_id', '=', $rankId)->exists();
    }

    public function hasAppliedForTeam(int $teamId): bool
    {
        if (! $teamId) {
            return false;
        }

        return $this->applications()
            ->where('team_id', $teamId)
            ->exists();
    }

    public function changePassword(string $newPassword): void
    {
        $this->password = Hash::make($newPassword);
        $this->save();
    }

    public function getFilamentName(): string
    {
        return $this->username ?? 'Guest';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return hasHousekeepingPermission('can_access_housekeeping');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id', 'username', 'motto', 'rank', 'credits'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function save(array $options = [])
    {
        if (! $this->isDirty()) {
            return false;
        }

        return parent::save($options);
    }
}
