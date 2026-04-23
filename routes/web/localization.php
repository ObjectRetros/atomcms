<?php

use App\Http\Controllers\Miscellaneous\LocaleController;
use Illuminate\Support\Facades\Route;

Route::get('/language/{locale}', LocaleController::class)->name('language.select');
