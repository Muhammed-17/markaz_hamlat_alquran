<?php

use App\Http\Controllers\Guardian\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:guardian'])
    ->prefix('guardian/notifications')
    ->name('guardian.notifications.')
    ->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('readAll');
    });
