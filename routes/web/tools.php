<?php

use App\Http\Controllers\Miscellaneous\LogoGeneratorController;
use Illuminate\Support\Facades\Route;

Route::get('/logo-generator', [LogoGeneratorController::class, 'index'])->name('logo-generator.index');
Route::post('/logo-generator', [LogoGeneratorController::class, 'store'])->name('store.generated-logo');
