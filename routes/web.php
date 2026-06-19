<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\GuardianSearchController;

// ================================================================
// Public Routes
// ================================================================
Route::get('/', [WelcomeController::class, 'index'])->name('home');

// ================================================================
// Dashboard Routes
// ================================================================

// ✅ dashboard للأدوار الإدارية — مع permission check
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware([
        'auth',
        'verified',
        'not.guardian',
        'permission:view dashboard',  // ✅ فقط من لديه الصلاحية
    ])
    ->name('dashboard');

// ✅ guardian dashboard — مع role + permission
Route::get('/guardian-dashboard', [DashboardController::class, 'guardianDashboard'])
    ->middleware([
        'auth',
        'verified',
        'role:guardian',
        'permission:view own children', // ✅ تحقق إضافي
    ])
    ->name('guardian.dashboard');

// ================================================================
// Profile Routes
// ================================================================
Route::middleware(['auth', 'permission:edit profile'])->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ================================================================
// Guardian Search Routes
// ✅ منفصلة عن /api — داخل web.php مع throttle
// ================================================================
Route::middleware(['auth', 'throttle:30,1'])->group(function () {

    // البحث عن ولي أمر
    Route::get('/guardians/search', [GuardianSearchController::class, 'search'])
        ->name('guardians.search');

    // ✅ التحقق من وجود حساب بالإيميل أو الموبايل
    Route::get('/guardians/check', [GuardianSearchController::class, 'check'])
        ->name('guardians.check');
});

// ================================================================
// Sub-route files
// ================================================================
require __DIR__ . '/auth.php';
require __DIR__ . '/student.php';
require __DIR__ . '/circle.php';
require __DIR__ . '/notification.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/attendance.php';
require __DIR__ . '/teacher.php';
require __DIR__ . '/subscription.php';
require __DIR__ . '/center.php';
require __DIR__ . '/guardian_accounts.php';
require __DIR__ . '/guardian_notification.php';
