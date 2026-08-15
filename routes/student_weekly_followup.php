<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentWeeklyFollowupController;
use App\Models\StudentWeeklyFollowup;

/*
|--------------------------------------------------------------------------
| Student Weekly Followup Routes
|--------------------------------------------------------------------------
|
| These routes handle the Student Weekly Followup module for managing
| weekly memorization plans, sessions, assessments, and recommendations.
|
| GROUP PLANS: Use batch_id for show/edit/update/destroy
| INDIVIDUAL PLANS: Use studentWeeklyFollowup model binding
|
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // Explicit model binding for individual plans
    Route::model('studentWeeklyFollowup', StudentWeeklyFollowup::class);

    // =============================================================
    // Index
    // =============================================================

    Route::get('student-weekly-followups', [StudentWeeklyFollowupController::class, 'indexGroup'])
        ->name('student-weekly-followups.index-group');

    Route::get('student-weekly-followups-individual', [StudentWeeklyFollowupController::class, 'indexIndividual'])
        ->name('student-weekly-followups.index-individual');

    // =============================================================
    // Group Plans (batch-based)
    // =============================================================
    // =============================================================
    // Group Plans (batch-based)
    // =============================================================
    Route::prefix('student-weekly-followups/group')->group(function () {
        Route::get('/create', [StudentWeeklyFollowupController::class, 'createGroup'])
            ->name('student-weekly-followups.create-group');
        Route::post('/store', [StudentWeeklyFollowupController::class, 'storeGroup'])
            ->name('student-weekly-followups.store-group');

        Route::get('/circles/{circle}/students', [StudentWeeklyFollowupController::class, 'studentsForCircle'])
            ->name('student-weekly-followups.students-for-circle');

        Route::get('/{batchId}', [StudentWeeklyFollowupController::class, 'showGroup'])
            ->name('student-weekly-followups.show-group');
        Route::get('/{batchId}/edit', [StudentWeeklyFollowupController::class, 'editGroup'])
            ->name('student-weekly-followups.edit-group');
        Route::put('/{batchId}/update', [StudentWeeklyFollowupController::class, 'updateGroup'])
            ->name('student-weekly-followups.update-group');
        Route::delete('/{batchId}', [StudentWeeklyFollowupController::class, 'destroyGroup'])
            ->name('student-weekly-followups.destroy-group');
    });

    // =============================================================
    // Individual Plans (model-based)
    // =============================================================
    Route::prefix('student-weekly-followups/individual')->group(function () {
        Route::get('/create', [StudentWeeklyFollowupController::class, 'createIndividual'])
            ->name('student-weekly-followups.create-individual');
        Route::post('/store', [StudentWeeklyFollowupController::class, 'storeIndividual'])
            ->name('student-weekly-followups.store-individual');
        Route::get('/student-weekly-followups/{studentWeeklyFollowup}/show-individual', [StudentWeeklyFollowupController::class, 'showIndividual'])
            ->name('student-weekly-followups.show-individual');
    });

    Route::get('/student-weekly-followups/{studentWeeklyFollowup}/edit-individual', [StudentWeeklyFollowupController::class, 'editIndividual'])
        ->name('student-weekly-followups.edit-individual');
    Route::put('/student-weekly-followups/{studentWeeklyFollowup}/update-individual', [StudentWeeklyFollowupController::class, 'updateIndividual'])
        ->name('student-weekly-followups.update-individual');

    // =============================================================
    // Shared Routes
    // =============================================================
    Route::get('/student-weekly-followups/{studentWeeklyFollowup}', [StudentWeeklyFollowupController::class, 'show'])
        ->name('student-weekly-followups.show');
    Route::get('/student-weekly-followups/{studentWeeklyFollowup}/edit', [StudentWeeklyFollowupController::class, 'edit'])
        ->name('student-weekly-followups.edit');
    Route::put('/student-weekly-followups/{studentWeeklyFollowup}', [StudentWeeklyFollowupController::class, 'update'])
        ->name('student-weekly-followups.update');
    Route::delete('/student-weekly-followups/{studentWeeklyFollowup}', [StudentWeeklyFollowupController::class, 'destroy'])
        ->name('student-weekly-followups.destroy');
});
