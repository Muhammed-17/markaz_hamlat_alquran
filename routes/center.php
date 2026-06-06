<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CenterController;

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/centers', [CenterController::class, 'index'])->name('centers.index');
    Route::post('/centers', [CenterController::class, 'store'])->name('centers.store');
    Route::delete('/centers/{center}', [CenterController::class, 'destroy'])->name('centers.destroy');
});
