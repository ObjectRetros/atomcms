<?php

use App\Http\Controllers\Client\FlashController;
use App\Http\Controllers\Client\NitroController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'findretros.redirect', 'vpn.checker'])->prefix('game')->group(function () {
    Route::get('/nitro', NitroController::class)->name('nitro-client');
    Route::get('/flash', FlashController::class)->name('flash-client');
});
