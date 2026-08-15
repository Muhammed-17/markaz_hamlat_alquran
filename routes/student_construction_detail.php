<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentConstructionDetailController;

Route::middleware(['auth', 'verified'])->group(function () {

    // ❌ حذفنا index لأنك لا تريد صفحة قائمة مستقلة

    Route::middleware('permission:create student construction details')->group(function () {
        Route::get('/student-construction-details/create', [StudentConstructionDetailController::class, 'create'])->name('student-construction-details.create');
        Route::post('/student-construction-details', [StudentConstructionDetailController::class, 'store'])->name('student-construction-details.store');
    });

    Route::middleware('permission:view student construction details')->group(function () {
        Route::get('/student-construction-details/{studentConstructionDetail}', [StudentConstructionDetailController::class, 'show'])->name('student-construction-details.show');
    });

    Route::middleware('permission:edit student construction details')->group(function () {
        Route::get('/student-construction-details/{studentConstructionDetail}/edit', [StudentConstructionDetailController::class, 'edit'])->name('student-construction-details.edit');
        Route::put('/student-construction-details/{studentConstructionDetail}', [StudentConstructionDetailController::class, 'update'])->name('student-construction-details.update');
    });

    Route::middleware('permission:delete student construction details')->group(function () {
        Route::delete('/student-construction-details/{studentConstructionDetail}', [StudentConstructionDetailController::class, 'destroy'])->name('student-construction-details.destroy');
    });
});
