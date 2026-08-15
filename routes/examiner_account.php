<?php

use App\Http\Controllers\Examiner\ExaminerDashboardController;
use App\Http\Controllers\Examiner\ExaminerCompetitionController;
use App\Http\Controllers\Examiner\CompetitionExamController;
use App\Http\Controllers\Admin\CompetitionAnswerController;
use App\Http\Controllers\Admin\CompetitionResultController;
use App\Http\Controllers\Admin\CompetitionFinalizationController;
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

// ══════════════════ لوحة الإدارة (نتائج المسابقات) ══════════════════
Route::middleware(['auth', 'role:admin|general_manager|manager|supervisor'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('competition-results', [CompetitionResultController::class, 'index'])->name('competition-results.index');
        Route::get('competition-results/{participantId}', [CompetitionResultController::class, 'show'])->name('competition-results.show');
        Route::get('competition-results/{participantId}/edit', [CompetitionResultController::class, 'edit'])->name('competition-results.edit');
        Route::put('competition-results/{participantId}', [CompetitionResultController::class, 'update'])->name('competition-results.update');

        Route::get('competition-answers/{answer}/edit', [CompetitionAnswerController::class, 'edit'])->name('competition-answers.edit');
        Route::put('competition-answers/{answer}', [CompetitionAnswerController::class, 'update'])->name('competition-answers.update');

        Route::get('competitions/{competition}/levels/{competitionLevel}/finalization', [CompetitionFinalizationController::class, 'show'])
            ->name('competitions.finalization.show');
        Route::put('competitions/{competition}/levels/{competitionLevel}/finalization', [CompetitionFinalizationController::class, 'update'])
            ->name('competitions.finalization.update');
    });
