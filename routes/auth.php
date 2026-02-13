<?php

use App\Http\Controllers\Miscellaneous\HomeController;
use App\Http\Controllers\Miscellaneous\MaintenanceController;
use App\Http\Controllers\User\BannedController;
use App\Http\Controllers\User\ForgotPasswordController;
use App\Http\Controllers\User\UserReferralController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

Route::middleware(['guest', 'throttle:15,1'])->withoutMiddleware('force.staff.2fa')->group(function () {
    Route::get('/login', static fn () => to_route('welcome'))->name('login');
    Route::get('/', HomeController::class)->name('welcome');

    Route::get('/register', [RegisteredUserController::class, 'create']);
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
    Route::get('/register/{referral_code}', UserReferralController::class)->name('register.referral');

    Route::get('forgot-password', ForgotPasswordController::class)->name('forgot.password.get');
    Route::post('forgot-password', [ForgotPasswordController::class, 'submitForgetPassword'])->name('forgot.password.post');
    Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPassword'])->name('reset.password.get');
    Route::post('reset-password/{token}', [ForgotPasswordController::class, 'submitResetPassword'])->name('reset.password.post');
});

Route::middleware(['maintenance', 'check.ban', 'force.staff.2fa'])->group(function () {
    Route::get('/maintenance', MaintenanceController::class)->name('maintenance.show');
    Route::get('/banned', BannedController::class)->name('banned.show');
});
