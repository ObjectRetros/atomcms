<?php

use App\Actions\Fortify\Controllers\TwoFactorAuthenticatedSessionController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BannedController;
use App\Http\Controllers\FlashController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\PhotosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NitroController;
use App\Http\Controllers\PasswordSettingsController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StaffApplicationsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TwoFactorAuthenticationController;
use App\Http\Controllers\WebsiteArticleCommentsController;
use App\Http\Controllers\WebsiteTeamsController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// Language route
Route::get('/language/{locale}', LocaleController::class)->name('language.select');

Route::middleware(['maintenance', 'check-ban', 'force.staff.2fa'])->group(function () {
    // Maintenance route
    Route::get('/maintenance', MaintenanceController::class)->name('maintenance.show');

    // Banned route
    Route::get('/banned', BannedController::class)->name('banned.show');

    Route::middleware('guest')->withoutMiddleware('force.staff.2fa')->group(function () {
        Route::get('/', HomeController::class)->name('welcome');

        Route::get('/register/{username}/{referral_code}', function (string $username, string $referral_code) {
            User::where('referral_code', '=', $referral_code)->firstOrFail();

            return view('auth.register', [
                'referral_code' =>  $referral_code,
            ]);
        })->name('register.referral');
    });

    Route::middleware('auth')->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('/me', [MeController::class, 'show'])->name('me.show');

            // User settings routes
            Route::prefix('settings')->group(function () {
                Route::get('/account', [AccountSettingsController::class, 'edit'])->name('settings.account.show');
                Route::put('/account', [AccountSettingsController::class, 'update'])->name('settings.account.update');
                Route::get('/password', [PasswordSettingsController::class, 'edit'])->name('settings.password.show');
                Route::put('/password', [PasswordSettingsController::class, 'update'])->name('settings.password.update');
                Route::get('/session-logs', [AccountSettingsController::class, 'sessionLogs'])->name('settings.session-logs');
                Route::get('/two-factor', [TwoFactorAuthenticationController::class, 'index'])->name('settings.two-factor');
                Route::post('/2fa-verify', [TwoFactorAuthenticationController::class, 'verify'])->name('two-factor.verify');
            });
        });

        // Profiles
        Route::get('/profile/{user:username}', ProfileController::class)->name('profile.show');

        // Community routes
        Route::prefix('community')->group(function () {
            Route::get('/', [MeController::class, 'index'])->name('community.index');
            Route::get('/claim/referral-reward', ReferralController::class)->name('claim.referral-reward');
            Route::get('/photos', PhotosController::class)->name('photos.index')->withoutMiddleware('auth');

            Route::withoutMiddleware('auth')->group(function () {
                Route::get('/articles', [ArticleController::class, 'index'])->name('article.index');
                Route::get('/article/{article:slug}', [ArticleController::class, 'show'])->name('article.show');
                Route::post('/article/{article:slug}/comment', [WebsiteArticleCommentsController::class, 'store'])->name('article.comment.store');
                Route::delete('/article/{comment}/comment', [WebsiteArticleCommentsController::class, 'destroy'])->name('article.comment.destroy');

                Route::get('/staff', StaffController::class)->name('staff.index');
                Route::get('/teams', WebsiteTeamsController::class)->name('teams.index');
            });

            Route::get('/staff-applications', [StaffApplicationsController::class, 'index'])->name('staff-applications.index');
            Route::get('/staff-applications/{position}', [StaffApplicationsController::class, 'show'])->name('staff-applications.show');
            Route::post('/staff-applications/{position}', [StaffApplicationsController::class, 'store'])->name('staff-applications.store');

            Route::post('/article/{article:slug}/toggle-reaction', [ArticleController::class, 'toggleReaction'])
                ->name('article.toggle-reaction')
                ->middleware('throttle:30,1');
        });

        // Leaderboard routes
        Route::get('/leaderboard', LeaderboardController::class)->name('leaderboard.index');

        // Rules routes
        Route::view('/rules', 'rules')->name('rules.index')->withoutMiddleware('auth');

        // Shop routes
        Route::get('/shop', ShopController::class)->name('shop.index');
        Route::post('/shop/redeem-voucher', [ShopController::class, 'redeemVoucher'])->name('shop.redeem-voucher');
        Route::post('/shop/{product}/purchase', [ShopController::class, 'purchase'])->name('shop.purchase');

        // Client route
        Route::prefix('game')->middleware(['findretros.redirect', 'vpn.checker'])->group(function () {
            Route::get('/nitro', NitroController::class)->name('nitro-client');
            Route::get('/flash', FlashController::class)->name('flash-client');
        });
    });
});

if (Features::enabled(Features::twoFactorAuthentication())) {
    $twoFactorLimiter = config('fortify.limiters.two-factor');

    Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
        ->middleware(
            array_filter([
                'guest:' . config('fortify.guard'),
                $twoFactorLimiter ? 'throttle:' . $twoFactorLimiter : null,
            ])
        );
}