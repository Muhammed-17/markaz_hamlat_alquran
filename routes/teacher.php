<?php

use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'permission:view teachers'])->group(function () {

    // 1. المسارات الثابتة (Static / Generic Routes)
    Route::get('teachers', [TeacherController::class, 'index'])->name('teachers.index');

    // إنشاء معلم (يجب أن يكون قبل teachers/{teacher})
    Route::middleware('permission:create teachers')->group(function () {
        Route::get('teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::post('teachers', [TeacherController::class, 'store'])->name('teachers.store');
    });

    // 2. المسارات الديناميكية المرتبطة بمُعرِّف معلم معين (Dynamic Parameter Routes)
    Route::get('teachers/{teacher}', [TeacherController::class, 'show'])->name('teachers.show');

    // تعديل البيانات
    Route::middleware('permission:edit teachers')->group(function () {
        Route::get('teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::match(['put', 'patch'], 'teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
    });

    // تغيير الحالة (Active / Inactive)
    Route::middleware('permission:toggle teacher status')->group(function () {
        Route::patch('teachers/{teacher}/toggle', [TeacherController::class, 'toggle'])->name('teachers.toggle');
    });

    // الحذف
    Route::middleware('permission:delete teachers')->group(function () {
        Route::delete('teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
    });
});
