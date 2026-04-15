<?php

use App\Http\Controllers\HotelApiController;
use App\Http\Controllers\NpcChatController;
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
Route::get('/online-count', [HotelApiController::class, 'onlineUserCount'])->name('api.online-count')->middleware('throttle:50,1');

/*
|--------------------------------------------------------------------------
| NPC Chat API Routes
|--------------------------------------------------------------------------
|
| These routes handle AI-powered NPC chat interactions.
| The Arcturus emulator plugin sends player chat messages here,
| and receives AI-generated NPC responses.
|
*/
Route::prefix('npc')->middleware(['npc.token', 'throttle:30,1'])->group(function () {
    Route::post('/chat', [NpcChatController::class, 'chat'])->name('api.npc.chat');
    Route::get('/info/{botId}', [NpcChatController::class, 'info'])->name('api.npc.info');
    Route::post('/reset', [NpcChatController::class, 'resetConversation'])->name('api.npc.reset');
});