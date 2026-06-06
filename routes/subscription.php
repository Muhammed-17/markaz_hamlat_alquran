<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPriceController;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::middleware('permission:view subscriptions')->group(function () {
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/subscriptions/late-and-unpaid', [SubscriptionController::class, 'lateAndUnpaid'])->name('subscriptions.late_and_unpaid');
    });

    Route::middleware('permission:create subscriptions')->group(function () {
        Route::get('/subscriptions/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');
        Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    });

    // Subscription Prices
    Route::middleware('permission:manage subscription prices')->group(function () {
        Route::get('/subscription-prices', [SubscriptionPriceController::class, 'index'])->name('subscription-prices.index');
        Route::post('/subscription-prices', [SubscriptionPriceController::class, 'store'])->name('subscription-prices.store');
        Route::delete('/subscription-prices/{subscriptionPrice}', [SubscriptionPriceController::class, 'destroy'])->name('subscription-prices.destroy');
    });

});