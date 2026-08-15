<?php

use App\Http\Controllers\LevelController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::resource('levels', LevelController::class)
        ->parameters(['levels' => 'level']);
});
