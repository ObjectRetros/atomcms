<?php

use App\Http\Controllers\HotelApiController;
use App\Http\Controllers\PaypalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user/{username}', [HotelApiController::class, 'fetchUser'])->name('api.fetch-user')->middleware('throttle:50,1');;
Route::get('/online-users', [HotelApiController::class, 'onlineUsers'])->name('api.online-users')->middleware('throttle:50,1');;

Route::get('/online-count', [HotelApiController::class, 'onlineUserCount'])->name('api.online-count')->middleware('throttle:30,1');

// Paypal routes
Route::prefix('paypal')->group(function() {
    Route::post('/order/create', [PaypalController::class, 'create'])->name('paypal.create');
    Route::post('/order/capture/', [PaypalController::class, 'execute'])->name('paypal.capture');
});

