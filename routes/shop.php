<?php

use App\Http\Controllers\Shop\PaypalController;
use App\Http\Controllers\Shop\ShopController;
use App\Http\Controllers\Shop\ShopVoucherController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('shop')->group(function () {
        Route::get('/{category:slug?}', ShopController::class)->name('shop.index');
        Route::post('/purchase/{package}', [ShopController::class, 'purchase'])->name('shop.buy');
        Route::post('/voucher', ShopVoucherController::class)->name('shop.use-voucher');
    });

    Route::controller(PaypalController::class)->prefix('paypal')->group(function () {
        Route::get('/process-transaction', 'process')->name('paypal.process-transaction');
        Route::get('/successful-transaction', 'successful')->name('paypal.successful-transaction');
        Route::get('/cancelled-transaction', 'cancelled')->name('paypal.cancelled-transaction');
    });
});
