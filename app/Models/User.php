<?php

namespace App\Models;

use App\Models\Articles\WebsiteArticle;
use App\Models\Articles\WebsiteArticleComment;
use App\Models\Community\Staff\WebsiteStaffApplications;
use App\Models\Community\Staff\WebsiteTeam;
use App\Models\Game\Furniture\Item;
use App\Models\Game\Permission;
use App\Models\Game\Player\MessengerFriendship;
use App\Models\Game\Player\UserBadge;
use App\Models\Game\Player\UserCurrency;
use App\Models\Game\Player\UserSetting;
use App\Models\Game\Player\UserSubscription;
use App\Models\Game\Room;
use App\Models\Help\WebsiteHelpCenterTicket;
use App\Models\Miscellaneous\CameraWeb;
use App\Models\Miscellaneous\WebsiteBetaCode;
use App\Models\Shop\WebsitePaypalTransaction;
use App\Models\Shop\WebsiteUsedShopVoucher;
use App\Models\User\Ban;
use App\Models\User\ClaimedReferralLog;
use App\Models\User\Referral;
use App\Models\User\UserReferral;
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
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;
	use LogsActivity;


    public $timestamps = false;

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'hidden_staff' => 'boolean',
            'online' => 'boolean',
        ];
    }

    public function currencies(): HasMany
    {
        return $this->hasMany(UserCurrency::class, 'user_id');
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function currency(string $currency)
    {
        if (!$this->relationLoaded('currencies')) {
            $this->load('currencies');
        }

        $type = match ($currency) {
            'duckets' => 0,
            'diamonds' => 5,
            'points' => 101,
        };

        return $this->currencies->where('type', $type)->first()->amount ?? 0;
    }

    public function permission(): HasOne
    {
        return $this->hasOne(Permission::class, 'id', 'rank');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(WebsiteArticle::class);
    }

    public function referrals(): HasOne
    {
        return $this->hasOne(UserReferral::class);
    }

    public function userReferrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    public function claimedReferralLog(): HasMany
    {
        return $this->hasMany(ClaimedReferralLog::class);
    }

    public function badges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'owner_id');
    }

    public function friends(): HasMany
    {
        return $this->hasMany(MessengerFriendship::class, 'user_one_id');
    }

    public function referralsNeeded()
    {
        $referrals = 0;

        if (!is_null($this->referrals)) {
            $referrals = $this->referrals->referrals_total;
        }

        return setting('referrals_needed') - $referrals;
    }

    public function ban(): HasOne
    {
        return $this->hasOne(Ban::class, 'user_id')->where('ban_expire', '>', time())->whereIn('type', ['account', 'super']);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    public function ssoTicket(): string
    {
        $sso = sprintf('%s-%s', Str::replace(' ', '', setting('hotel_name')), Str::uuid());

        if (User::where('auth_ticket', $sso)->exists()) {
            return $this->ssoTicket();
        }

        $this->update([
            'auth_ticket' => $sso,
        ]);

        return $sso;
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

    public function tickets(): HasMany
    {
        return $this->hasMany(WebsiteHelpCenterTicket::class);
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

    public function chatLogs()
    {
        return $this->hasMany(ChatlogRoom::class, 'user_from_id');
    }

    public function chatLogsPrivate()
    {
        return $this->hasMany(ChatlogPrivate::class, 'user_from_id');
    }

    public function getOnlineFriends(int $total = 10)
    {
        return $this->friends()
            ->select(['user_two_id', 'users.id', 'users.username', 'users.look', 'users.motto', 'users.last_online'])
            ->join('users', 'users.id', '=', 'user_two_id')
            ->where('users.online', '1')
            ->inRandomOrder()
            ->limit($total)
            ->get();
    }

    public function confirmTwoFactorAuthentication($code)
    {
        $codeIsValid = app(TwoFactorAuthenticationProvider::class)
            ->verify(decrypt($this->two_factor_secret), $code);

        if (!$codeIsValid) {
            return false;
        }

        $this->update([
            'two_factor_confirmed' => true,
        ]);

        return true;
    }

    public function hasAppliedForPosition(int $rankId)
    {
        return $this->applications()->where('rank_id', '=', $rankId)->exists();
    }

    public function changePassword(string $newPassword)
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
        return hasPermission('housekeeping_access');
    }
	
	public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['id', 'username', 'motto', 'rank']);
    }
}
