<?php

use App\Http\Controllers\Api\V1\AccountController;
use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\BootstrapController;
use App\Http\Controllers\Api\V1\CommunityController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\ShopController;
use App\Http\Controllers\Api\V1\SupportController;
use App\Http\Controllers\Api\V1\ToolController;
use App\Http\Controllers\Api\V1\TwoFactorController;
use Illuminate\Support\Facades\Route;

Route::get('status', [BootstrapController::class, 'status'])->name('status');
Route::get('bootstrap', BootstrapController::class)->name('bootstrap');
Route::get('ban', [AccountController::class, 'ban'])->name('ban');
Route::middleware(['maintenance', 'check.ban', 'force.staff.2fa'])->group(function (): void {
    Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('articles/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');
    Route::get('articles/{article:slug}/comments', [ArticleController::class, 'comments'])->name('articles.comments.index');
    Route::get('users/online', [CommunityController::class, 'online'])->name('users.online');
    Route::get('users', [CommunityController::class, 'users'])->name('users.index');
    Route::get('users/{user:username}', [CommunityController::class, 'user'])->name('users.show');
    Route::get('homes/{user:username}', [HomeController::class, 'show'])->name('homes.show');
    Route::get('homes/{user:username}/widgets/{homeItem}', [HomeController::class, 'widget'])->scopeBindings()->name('homes.widgets.show');
    Route::get('rules', [SupportController::class, 'rules'])->withoutMiddleware('check.ban')->name('rules');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('me', [AccountController::class, 'show'])->name('me');
        Route::put('me/account', [AccountController::class, 'update'])->name('me.account');
        Route::put('me/password', [AccountController::class, 'password'])->name('me.password');
        Route::get('me/sessions', [AccountController::class, 'sessions'])->name('me.sessions');
        Route::post('me/referral-claim', [AccountController::class, 'claimReferral'])->middleware('throttle:5,1')->name('me.referral-claim');
        Route::get('me/two-factor', [TwoFactorController::class, 'show'])->withoutMiddleware('force.staff.2fa')->name('me.two-factor');
        Route::post('articles/{article:slug}/comments', [ArticleController::class, 'storeComment'])->name('articles.comments.store');
        Route::delete('comments/{comment}', [ArticleController::class, 'destroyComment'])->name('comments.destroy');
        Route::post('articles/{article:slug}/reactions', [ArticleController::class, 'reaction'])->name('articles.reactions');
        Route::put('articles/{article:slug}/reactions/{reaction}', [ArticleController::class, 'setReaction'])->name('articles.reactions.set');
        Route::delete('articles/{article:slug}/reactions/{reaction}', [ArticleController::class, 'setReaction'])->name('articles.reactions.remove');
        Route::get('staff', [CommunityController::class, 'staff'])->name('staff');
        Route::get('teams', [CommunityController::class, 'teams'])->name('teams');
        Route::get('leaderboards', [CommunityController::class, 'leaderboards'])->name('leaderboards');
        Route::get('photos', [CommunityController::class, 'photos'])->middleware('emulator.feature:camera-photos')->name('photos');
        Route::get('applications', [CommunityController::class, 'applications'])->name('applications.index');
        Route::get('applications/{position}', [CommunityController::class, 'position'])->name('applications.show');
        Route::post('applications/{position}', [CommunityController::class, 'apply'])->middleware('throttle:10,1')->name('applications.store');
        Route::get('shop', [ShopController::class, 'index'])->name('shop');
        Route::post('shop/packages/{package}/purchases', [ShopController::class, 'purchase'])->middleware('throttle:10,1')->name('shop.purchases.store');
        Route::get('shop/purchases', [ShopController::class, 'purchases'])->name('shop.purchases.index');
        Route::post('shop/vouchers', [ShopController::class, 'voucher'])->middleware('throttle:5,1')->name('shop.vouchers');
        Route::post('shop/paypal/orders', [ShopController::class, 'createOrder'])->middleware('throttle:10,1')->name('shop.paypal.store');
        Route::get('shop/paypal/orders/{order}', [ShopController::class, 'order'])->name('shop.paypal.show');
        Route::get('home-shop', [HomeController::class, 'shop'])->name('homes.shop');
        Route::get('homes/{user:username}/inventory', [HomeController::class, 'inventory'])->name('homes.inventory');
        Route::put('homes/{user:username}', [HomeController::class, 'save'])->middleware('throttle:10,1')->name('homes.save');
        Route::post('homes/{user:username}/purchases', [HomeController::class, 'purchase'])->middleware('throttle:30,1')->name('homes.purchases');
        Route::post('homes/{user:username}/messages', [HomeController::class, 'message'])->middleware('throttle:10,1')->name('homes.messages');
        Route::post('homes/{user:username}/ratings', [HomeController::class, 'rating'])->middleware('throttle:10,1')->name('homes.ratings');
        Route::get('badges', [ToolController::class, 'badges'])->name('badges');
        Route::post('badges', [ToolController::class, 'buyBadge'])->middleware('throttle:10,1')->name('badges.store');
        Route::post('logo', [ToolController::class, 'logo'])->middleware('throttle:10,1')->name('logo');
        Route::get('rare-values', [ToolController::class, 'rareValues'])->middleware('emulator.feature:rare-values')->name('rare-values');
        Route::get('rare-values/{value}', [ToolController::class, 'rareValue'])->middleware('emulator.feature:rare-values')->name('rare-values.show');
        Route::post('client/launch', [ToolController::class, 'launch'])->middleware(['findretros.redirect', 'vpn.checker', 'throttle:10,1'])->name('client.launch');
        Route::withoutMiddleware('check.ban')->prefix('support')->name('support.')->group(function (): void {
            Route::get('/', [SupportController::class, 'index'])->name('index');
            Route::get('tickets', [SupportController::class, 'tickets'])->name('tickets.index');
            Route::post('tickets', [SupportController::class, 'store'])->middleware('throttle:10,1')->name('tickets.store');
            Route::get('tickets/{ticket}', [SupportController::class, 'show'])->name('tickets.show');
            Route::put('tickets/{ticket}', [SupportController::class, 'update'])->name('tickets.update');
            Route::delete('tickets/{ticket}', [SupportController::class, 'destroy'])->name('tickets.destroy');
            Route::post('tickets/{ticket}/toggle-status', [SupportController::class, 'toggle'])->name('tickets.toggle');
            Route::post('tickets/{ticket}/replies', [SupportController::class, 'reply'])->middleware('throttle:10,1')->name('replies.store');
            Route::delete('replies/{reply}', [SupportController::class, 'destroyReply'])->name('replies.destroy');
        });
    });
});
