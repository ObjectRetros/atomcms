<?php

use App\Actions\Fortify\Controllers\TwoFactorAuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

require __DIR__ . '/web/localization.php';
require __DIR__ . '/web/installation.php';

Route::middleware(['maintenance', 'check.ban', 'force.staff.2fa'])->group(function () {
    require __DIR__ . '/web/system.php';
    require __DIR__ . '/web/guest.php';

    Route::middleware('auth')->group(function () {
        require __DIR__ . '/web/user.php';
        require __DIR__ . '/web/home.php';
        require __DIR__ . '/web/community.php';
        require __DIR__ . '/web/shop.php';
        require __DIR__ . '/web/help-center.php';
        require __DIR__ . '/web/client.php';
        require __DIR__ . '/web/tools.php';
    });
});

if (Features::enabled(Features::twoFactorAuthentication())) {
    $twoFactorLimiter = config('fortify.limiters.two-factor');

    Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
        ->middleware(
            array_filter([
                'guest:' . config('fortify.guard'),
                $twoFactorLimiter ? 'throttle:' . $twoFactorLimiter : null,
            ]),
        );
}
