<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::middleware('permission:create attendance')->group(function () {
        Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    });

    Route::middleware('permission:edit attendance')->group(function () {
        Route::get('/attendance/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
        Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
    });

    Route::middleware('permission:view attendance')->group(function () {
        Route::get('/attendance/sequential-absences', [AttendanceController::class, 'sequentialAbsences'])->name('attendance.sequential-absences');
        Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');
    });

    Route::middleware('permission:view own attendance')->group(function () {
        Route::get('/my-attendance', [AttendanceController::class, 'myAttendance'])->name('attendance.own');
    });
});
