<?php

use App\Http\Controllers\Miscellaneous\MaintenanceController;
use App\Http\Controllers\User\BannedController;
use Illuminate\Support\Facades\Route;

Route::get('/maintenance', MaintenanceController::class)->name('maintenance.show');
Route::get('/banned', BannedController::class)->name('banned.show');
