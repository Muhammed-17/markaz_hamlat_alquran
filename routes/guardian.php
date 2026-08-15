<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// ================================================================
// إشعارات ولي الأمر
// ================================================================
Route::middleware(['auth', 'verified', 'role:guardian'])
    ->prefix('guardian/notifications')
    ->name('guardian.notifications.')
    ->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('readAll');
    });

// ================================================================
//  أبناء ولي الأمر
// ================================================================
Route::middleware(['auth', 'verified', 'role:guardian'])
    ->prefix('guardian')
    ->name('guardian.')
    ->group(function () {
        Route::middleware('permission:view own attendance')->group(function () {
            Route::get('/my-attendance', [AttendanceController::class, 'myAttendance'])
                ->name('attendance.own');
        });

        Route::middleware('permission:view own subscriptions')->group(function () {
            Route::get('/my-subscription', [SubscriptionController::class, 'mySubscription'])
                ->name('subscription.own');
        });
        Route::get('/my-dashboard', [DashboardController::class, 'myDashboard'])
            ->name('dashboard.own');
    });
