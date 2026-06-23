<?php

use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::middleware('permission:create teachers')->group(function () {
        Route::get('teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::post('teachers', [TeacherController::class, 'store'])->name('teachers.store');
    });

    Route::middleware('permission:view teachers')->group(function () {
        Route::get('teachers', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('teachers/{teacher}', [TeacherController::class, 'show'])->name('teachers.show');
    });

    Route::middleware('permission:edit teachers')->group(function () {
        Route::get('teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::patch('teachers/{teacher}', [TeacherController::class, 'update']);
    });

    Route::middleware('permission:delete teachers')->group(function () {
        Route::delete('teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
    });

    Route::middleware('permission:toggle teacher status')->group(function () {
        Route::patch('teachers/{teacher}/toggle', [TeacherController::class, 'toggle'])->name('teachers.toggle');
    });
});
