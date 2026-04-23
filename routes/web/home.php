<?php

use App\Http\Controllers\Home\HomeController as UserHomeController;
use App\Http\Controllers\Home\ItemController as HomeItemController;
use App\Http\Controllers\Home\MessageController as HomeMessageController;
use App\Http\Controllers\Home\RatingController as HomeRatingController;
use App\Http\Controllers\Home\ShopController as HomeShopController;
use Illuminate\Support\Facades\Route;

Route::prefix('home')->as('home.')->group(function () {
    Route::get('/{username}', [UserHomeController::class, 'show'])->name('show')->withoutMiddleware('auth');
    Route::get('/{username}/placed-items', [UserHomeController::class, 'getPlacedItems'])->name('placed-items')->withoutMiddleware('auth');
    Route::get('/{username}/widget-content/{itemId}', [HomeItemController::class, 'getWidgetContent'])->name('widget-content')->withoutMiddleware('auth');

    Route::post('/{username}/save', [UserHomeController::class, 'save'])->name('save')->middleware('throttle:10,1');
    Route::post('/{username}/buy-item', [HomeItemController::class, 'store'])->name('buy-item')->middleware('throttle:30,1');
    Route::post('/{username}/rating', [HomeRatingController::class, 'store'])->name('rating')->middleware('throttle:10,1');
    Route::post('/{username}/message', [HomeMessageController::class, 'store'])->name('message')->middleware('throttle:10,1');

    Route::prefix('shop')->as('shop.')->group(function () {
        Route::get('/categories', [HomeShopController::class, 'categories'])->name('categories');
        Route::get('/category/{category}/items', [HomeShopController::class, 'itemsByCategory'])->name('category-items');
        Route::get('/type/{type}/items', [HomeShopController::class, 'itemsByType'])->name('type-items');
        Route::get('/balance', [HomeShopController::class, 'balance'])->name('balance');
    });

    Route::get('/{username}/inventory', [HomeShopController::class, 'inventory'])->name('inventory');
});
