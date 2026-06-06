<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\RolePermissionController;

Route::get('/', [WelcomeController::class, 'index']);

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/guardian-dashboard', [DashboardController::class, 'guardianDashboard'])->middleware(['auth', 'verified', 'role:guardian'])->name('guardian.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__ . '/auth.php';
require __DIR__ . '/student.php';
require __DIR__ . '/circle.php';
require __DIR__ . '/notification.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/attendance.php';
require __DIR__ . '/teacher.php';
require __DIR__ . '/subscription.php';
require __DIR__ . '/center.php';
require __DIR__ . '/guardian.php';
