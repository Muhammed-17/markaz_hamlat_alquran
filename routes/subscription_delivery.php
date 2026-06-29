<?php

use App\Http\Controllers\SubscriptionDeliveryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('subscription-deliveries')->group(function () {
    // ═══════════════════════════════════════
    // Teacher: تسجيل تسليم جديد
    // ═══════════════════════════════════════
    Route::get('/create', [SubscriptionDeliveryController::class, 'create'])
        ->name('subscription-deliveries.create');
    Route::post('/', [SubscriptionDeliveryController::class, 'store'])
        ->name('subscription-deliveries.store');

    // ═══════════════════════════════════════
    // Supervisor / Admin: عرض ومراجعة التسليمات
    // ═══════════════════════════════════════
    Route::get('/', [SubscriptionDeliveryController::class, 'index'])
        ->name('subscription-deliveries.index');

    // ✅ مراجعة المدير (تأكيد + تعديل المبلغ المحصل)
    Route::get('/{delivery}/admin-review', [SubscriptionDeliveryController::class, 'adminReview'])
        ->name('subscription-deliveries.admin-review');
    Route::patch('/{delivery}/admin-review', [SubscriptionDeliveryController::class, 'adminReviewUpdate'])
        ->name('subscription-deliveries.admin-review-update');

    // ═══════════════════════════════════════
    // Teacher / Admin: تعديل وحذف التسليم
    // ═══════════════════════════════════════
    Route::get('/{delivery}/edit', [SubscriptionDeliveryController::class, 'edit'])
        ->name('subscription-deliveries.edit');
    Route::put('/{delivery}', [SubscriptionDeliveryController::class, 'update'])
        ->name('subscription-deliveries.update');

    // ✅ جديد: حذف التسليم
    Route::delete('/{delivery}', [SubscriptionDeliveryController::class, 'destroy'])
        ->name('subscription-deliveries.destroy');

    // ═══════════════════════════════════════
    // ❌ محذوفة (تم استبدالها بـ admin-review)
    // ═══════════════════════════════════════
    // Route::patch('/{delivery}/confirm', ...);
    // Route::patch('/{delivery}/reject', ...);
    // Route::patch('/{delivery}/admin-confirm', ...);
});
