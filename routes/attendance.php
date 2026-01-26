<?php

namespace App\Http\Routes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index')->middleware(['auth', 'verified']);