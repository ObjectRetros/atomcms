<?php

use App\Http\Controllers\Help\HelpCenterController;
use App\Http\Controllers\Help\TicketController;
use App\Http\Controllers\Help\TicketReplyController;
use App\Http\Controllers\Help\WebsiteRulesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->withoutMiddleware('check.ban')->group(function () {
    Route::prefix('help-center')->as('help-center.')->group(function () {
        Route::get('/', HelpCenterController::class)->name('index');

        Route::prefix('tickets')->as('ticket.')->group(function () {
            Route::get('/create', [TicketController::class, 'create'])->name('create');
            Route::post('/store', [TicketController::class, 'store'])->name('store');

            Route::get('/show/{ticket}', [TicketController::class, 'show'])->name('show');
            Route::get('/edit/{ticket}', [TicketController::class, 'edit'])->name('edit');
            Route::put('/edit/{ticket}', [TicketController::class, 'update'])->name('update');
            Route::delete('/delete/{ticket}', [TicketController::class, 'destroy'])->name('destroy');

            Route::put('/toggle-status/{ticket}', [TicketController::class, 'toggleTicketStatus'])->name('toggle-status');

            Route::post('/reply/{ticket}/store', [TicketReplyController::class, 'store'])->name('reply.store');
            Route::delete('/reply/{reply}/delete', [TicketController::class, 'destroy'])->name('reply.destroy');

            Route::get('/all', [TicketController::class, 'index'])->name('index');
        });

        Route::get('/rules', WebsiteRulesController::class)->name('rules.index')->withoutMiddleware('auth');
    });
});
