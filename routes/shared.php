<?php

use App\Actions\Fortify\Controllers\TwoFactorAuthenticatedSessionController;
use App\Http\Controllers\Shop\PaypalController;
use App\Http\Controllers\User\ForgotPasswordController;
use App\Http\Controllers\User\TwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

Route::middleware(['guest', 'maintenance', 'check.ban'])->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:6,1')->name('register.store');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'submitForgetPassword'])->middleware('throttle:6,1')->name('forgot.password.post');
    Route::post('/reset-password/{token}', [ForgotPasswordController::class, 'submitResetPassword'])->middleware('throttle:6,1')->name('reset.password.post');
});

Route::middleware(['auth', 'maintenance', 'check.ban', 'throttle:two-factor-settings'])
    ->prefix('user/settings')->group(function () {
        Route::post('/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])->name('user.two-factor.enable');
        Route::post('/two-factor-authentication/confirm', [TwoFactorAuthenticationController::class, 'verify'])->name('two-factor.verify');
        Route::delete('/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])->name('user.two-factor.disable');
    });

Route::middleware('auth')->controller(PaypalController::class)->prefix('paypal')->group(function () {
    Route::post('/process-transaction', 'process')->middleware(['maintenance', 'check.ban', 'force.staff.2fa', 'throttle:10,1'])->name('paypal.process-transaction');
    Route::get('/successful-transaction', 'successful')->name('paypal.successful-transaction');
    Route::get('/cancelled-transaction', 'cancelled')->name('paypal.cancelled-transaction');
});

if (Features::enabled(Features::twoFactorAuthentication())) {
    $limiter = config('fortify.limiters.two-factor');
    Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
        ->middleware(array_filter(['guest:' . config('fortify.guard'), $limiter ? 'throttle:' . $limiter : null]))
        ->name('two-factor.login.store');
}
