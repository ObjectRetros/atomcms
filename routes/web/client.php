<?php

use App\Http\Controllers\Badge\BadgeController;
use App\Http\Controllers\Client\FlashController;
use App\Http\Controllers\Client\NitroController;
use Illuminate\Support\Facades\Route;

Route::get('/draw-badge', [BadgeController::class, 'show'])->name('draw-badge');
Route::post('/buy-badge', [BadgeController::class, 'buy'])->name('badge.buy');

Route::prefix('game')->middleware(['findretros.redirect', 'vpn.checker'])->group(function () {
    Route::get('/nitro', NitroController::class)->name('nitro-client');
    Route::get('/flash', FlashController::class)->name('flash-client');
});
