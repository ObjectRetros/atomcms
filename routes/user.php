<?php

use App\Http\Controllers\User\AccountSettingsController;
use App\Http\Controllers\User\GuestbookController;
use App\Http\Controllers\User\MeController;
use App\Http\Controllers\User\PasswordSettingsController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ReferralController;
use App\Http\Controllers\User\TwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/me', MeController::class)->name('me.show');
        Route::get('/claim/referral-reward', ReferralController::class)->name('claim.referral-reward');

        Route::prefix('settings')->group(function () {
            Route::get('/account', [AccountSettingsController::class, 'edit'])->name('settings.account.show');
            Route::put('/account', [AccountSettingsController::class, 'update'])->name('settings.account.update');

            Route::get('/password', [PasswordSettingsController::class, 'edit'])->name('settings.password.show');
            Route::put('/password', [PasswordSettingsController::class, 'update'])->name('settings.password.update');

            Route::get('/session-logs', [AccountSettingsController::class, 'sessionLogs'])->name('settings.session-logs');

            Route::get('/two-factor', [TwoFactorAuthenticationController::class, 'index'])->name('settings.two-factor');
            Route::post('/user/settings/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])->name('user.two-factor.enable');
            Route::post('/2fa-verify', [TwoFactorAuthenticationController::class, 'verify'])->name('two-factor.verify');
            Route::delete('/user/settings/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])->name('user.two-factor.disable');
        });
    });

    Route::get('/profile/{user:username}', ProfileController::class)->name('profile.show');
    Route::post('/profile/{user}/guestbook', [GuestbookController::class, 'store'])->name('guestbook.store');
    Route::delete('/profile/{user}/{guestbook}/delete', [GuestbookController::class, 'destroy'])->name('guestbook.destroy');
});
