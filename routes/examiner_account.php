<?php

use App\Http\Controllers\Examiner\ExaminerDashboardController;
use App\Http\Controllers\Examiner\ExaminerCompetitionController;
use App\Http\Controllers\Examiner\CompetitionExamController;
use Illuminate\Support\Facades\Route;

// ══════════════════ لوحة المختبر ══════════════════
Route::middleware(['auth', 'role:examiner'])
    ->prefix('examiner')
    ->name('examiner.')
    ->group(function () {
        Route::get('dashboard', [ExaminerDashboardController::class, 'index'])->name('dashboard');

        Route::get('competitions', [ExaminerCompetitionController::class, 'index'])->name('competitions.index');
        Route::get('competitions/{competition}/levels', [ExaminerCompetitionController::class, 'levels'])->name('competitions.levels');

        Route::get('levels/{competitionLevel}/participants', [CompetitionExamController::class, 'participants'])->name('participants.index');

        Route::get('participants/{participant}/exam', [CompetitionExamController::class, 'show'])->name('exam.show');
        Route::post('participants/{participant}/exam', [CompetitionExamController::class, 'store'])->name('exam.store');
        Route::get('participants/{participant}/exam/review', [CompetitionExamController::class, 'review'])->name('exam.review');
        Route::post('participants/{participant}/exam/finalize', [CompetitionExamController::class, 'finalize'])->name('exam.finalize');
    });
