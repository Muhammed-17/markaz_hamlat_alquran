<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');
    Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');

    // Subscription Prices
    Route::get('/subscription-prices', [\App\Http\Controllers\SubscriptionPriceController::class, 'index'])->name('subscription-prices.index');
    Route::post('/subscription-prices', [\App\Http\Controllers\SubscriptionPriceController::class, 'store'])->name('subscription-prices.store');
    Route::delete('/subscription-prices/{subscriptionPrice}', [\App\Http\Controllers\SubscriptionPriceController::class, 'destroy'])->name('subscription-prices.destroy');
});
