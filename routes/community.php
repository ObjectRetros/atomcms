<?php

use App\Http\Controllers\Articles\ArticleController;
use App\Http\Controllers\Articles\WebsiteArticleCommentsController;
use App\Http\Controllers\Badge\BadgeController;
use App\Http\Controllers\Community\LeaderboardController;
use App\Http\Controllers\Community\PhotosController;
use App\Http\Controllers\Community\Staff\StaffApplicationsController;
use App\Http\Controllers\Community\Staff\StaffController;
use App\Http\Controllers\Community\Staff\WebsiteTeamApplicationsController;
use App\Http\Controllers\Community\Staff\WebsiteTeamsController;
use App\Http\Controllers\Community\WebsiteRareValuesController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('community')->group(function () {
        Route::get('/photos', PhotosController::class)->name('photos.index');

        Route::get('/staff', StaffController::class)->name('staff.index');
        Route::get('/teams', WebsiteTeamsController::class)->name('teams.index');

        Route::get('/staff-applications', [StaffApplicationsController::class, 'index'])->name('staff-applications.index');
        Route::get('/staff-applications/{position}', [StaffApplicationsController::class, 'show'])->name('staff-applications.show');
        Route::post('/staff-applications/{position}', [StaffApplicationsController::class, 'store'])->name('staff-applications.store');

        Route::get('/team-applications', [WebsiteTeamApplicationsController::class, 'index'])->name('team-applications.index');
        Route::get('/team-applications/{position}', [WebsiteTeamApplicationsController::class, 'show'])->name('team-applications.show');
        Route::post('/team-applications/{position}', [WebsiteTeamApplicationsController::class, 'store'])->name('team-applications.store');

        Route::post('/article/{article:slug}/comment', [WebsiteArticleCommentsController::class, 'store'])->name('article.comment.store');
        Route::delete('/article/{comment}/comment', [WebsiteArticleCommentsController::class, 'destroy'])->name('article.comment.destroy');
        Route::post('/article/{article:slug}/toggle-reaction', [ArticleController::class, 'toggleReaction'])
            ->name('article.toggle-reaction')
            ->middleware('throttle:30,1');
    });

    Route::get('/leaderboard', LeaderboardController::class)->name('leaderboard.index');

    Route::get('/draw-badge', [BadgeController::class, 'show'])->name('draw-badge');
    Route::post('/buy-badge', [BadgeController::class, 'buy'])->name('badge.buy');

    Route::withoutMiddleware('auth')->group(function () {
        Route::get('/community/articles', [ArticleController::class, 'index'])->name('article.index');
        Route::get('/community/article/{article:slug}', [ArticleController::class, 'show'])->name('article.show');
    });

    Route::get('/values', [WebsiteRareValuesController::class, 'index'])->name('values.index');
    Route::post('/values/search', [WebsiteRareValuesController::class, 'search'])->name('values.search');
    Route::get('/values/category/{category}', [WebsiteRareValuesController::class, 'category'])->name('values.category');
    Route::get('/values/{value}', [WebsiteRareValuesController::class, 'value'])->name('values.value');
});
