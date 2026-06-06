<?php

use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');
    Route::post('/settings/dry-run', [SettingsController::class, 'dryRun'])->name('settings.dry-run');
    Route::post('/settings/force-send', [SettingsController::class, 'forceSend'])->name('settings.force-send');
    Route::post('/settings/dry-run-json', [SettingsController::class, 'dryRunJson'])->name('settings.dry-run-json');
    Route::post('/settings/force-send-json', [SettingsController::class, 'forceSendJson'])->name('settings.force-send-json');

    Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');

    // ✅ الاتنين هنا بس، بدون أي group تاني
    Route::get('/roles/{role}/permissions', [RolePermissionController::class, 'edit'])->name('roles.permissions.edit');
    Route::put('/roles/{role}/permissions', [RolePermissionController::class, 'updateRolePermissions'])->name('roles.permissions.update');
});
