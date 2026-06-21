<?php

use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:view teachers|view all teachers'])->group(function () {
    Route::patch('teachers/{teacher}/toggle', [TeacherController::class, 'toggle'])->name('teachers.toggle');
    Route::resource('teachers', TeacherController::class);
});