<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::middleware('permission:view students')->group(function () {
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    });

    // ✅ create قبل {student}
    Route::middleware('permission:create students')->group(function () {
        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    });

    Route::middleware('permission:view students')->group(function () {
        Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    });

    Route::middleware('permission:edit students')->group(function () {
        Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    });

    Route::middleware('permission:delete students')->group(function () {
        Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    });

    Route::middleware('permission:assign student to circle')->group(function () {
        Route::post('/students/{student}/assign-circle', [StudentController::class, 'assignCircle'])->name('students.assign-circle');
    });
});