<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CircleController;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::middleware('permission:view circles')->group(function () {
        Route::get('/circles', [CircleController::class, 'index'])->name('circles.index');
        Route::get('/circles/{circle}', [CircleController::class, 'show'])->name('circles.show');
    });

    Route::middleware('permission:create circles')->group(function () {
        Route::get('/circles/create', [CircleController::class, 'create'])->name('circles.create');
        Route::post('/circles', [CircleController::class, 'store'])->name('circles.store');
    });

    Route::middleware('permission:edit circles')->group(function () {
        Route::get('/circles/{circle}/edit', [CircleController::class, 'edit'])->name('circles.edit');
        Route::put('/circles/{circle}', [CircleController::class, 'update'])->name('circles.update');
    });

    Route::middleware('permission:delete circles')->group(function () {
        Route::delete('/circles/{circle}', [CircleController::class, 'destroy'])->name('circles.destroy');
    });

    Route::middleware('permission:manage circle teachers')->group(function () {
        Route::post('/circles/{circle}/teachers', [CircleController::class, 'assignTeacher'])->name('circles.teachers.assign');
        Route::delete('/circles/{circle}/teachers/{teacher}', [CircleController::class, 'removeTeacher'])->name('circles.teachers.remove');
    });
});