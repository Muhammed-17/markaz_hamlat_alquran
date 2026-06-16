<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPriceController;

Route::middleware(['auth', 'verified'])->group(function () {

    // ─── عرض الاشتراكات ───────────────────────────────────────────
    Route::middleware('permission:view subscriptions|view own subscriptions')->group(function () {
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])
            ->name('subscriptions.index');
        Route::get('/subscriptions/late-and-unpaid', [SubscriptionController::class, 'lateAndUnpaid'])
            ->name('subscriptions.late_and_unpaid');
    });

    // ─── إضافة اشتراك ─────────────────────────────────────────────
    Route::middleware('permission:create subscriptions')->group(function () {
        Route::get('/subscriptions/create', [SubscriptionController::class, 'create'])
            ->name('subscriptions.create');
        Route::post('/subscriptions', [SubscriptionController::class, 'store'])
            ->name('subscriptions.store');
    });

    // ─── تعديل اشتراك ─────────────────────────────────────────────
    Route::middleware('permission:edit subscriptions')->group(function () {
        Route::get('/subscriptions/{subscription}/edit', [SubscriptionController::class, 'edit'])
            ->name('subscriptions.edit');
        Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update'])
            ->name('subscriptions.update');
    });

    // ─── حذف اشتراك ───────────────────────────────────────────────
    Route::middleware('permission:delete subscriptions')->group(function () {
        Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])
            ->name('subscriptions.destroy');
    });

    // ─── عرض أسعار الاشتراكات ────────────────────────────────────
    Route::middleware('permission:view subscription prices')->group(function () {
        Route::get('/subscription-prices', [SubscriptionPriceController::class, 'index'])
            ->name('subscription-prices.index');
    });

    // ─── إدارة أسعار الاشتراكات ──────────────────────────────────
    Route::middleware('permission:manage subscription prices')->group(function () {
        Route::post('/subscription-prices', [SubscriptionPriceController::class, 'store'])
            ->name('subscription-prices.store');
        Route::delete('/subscription-prices/{subscriptionPrice}', [SubscriptionPriceController::class, 'destroy'])
            ->name('subscription-prices.destroy');
    });
});
