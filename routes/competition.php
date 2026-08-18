<?php

use App\Http\Controllers\Competition\CompetitionAnswerController;
use App\Http\Controllers\Competition\CompetitionExamAnswerController;
use App\Http\Controllers\Competition\CompetitionController;
use App\Http\Controllers\Competition\CompetitionExaminerController;
use App\Http\Controllers\Competition\CompetitionExaminerQuestionController;
use App\Http\Controllers\Competition\CompetitionLevelController;
use App\Http\Controllers\Competition\CompetitionLevelQuestionController;
use App\Http\Controllers\Competition\CompetitionParticipantController;
use App\Http\Controllers\CompetitionResultController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::prefix('competitions')->name('competitions.')->group(function () {

        // المسابقات الأساسية
        Route::get('/', [CompetitionController::class, 'index'])->name('index');
        Route::get('/create', [CompetitionController::class, 'create'])->name('create');
        Route::post('/', [CompetitionController::class, 'store'])->name('store');
        Route::get('/{competition}/edit', [CompetitionController::class, 'edit'])->name('edit');
        Route::put('/{competition}', [CompetitionController::class, 'update'])->name('update');
        Route::delete('/{competition}', [CompetitionController::class, 'destroy'])->name('destroy');
        Route::post('/{competition}/duplicate', [CompetitionController::class, 'duplicate'])->name('duplicate');

        // المستويات
        Route::get('/{competition}/levels', [CompetitionLevelController::class, 'index'])->name('levels');
        Route::get('/{competition}/levels/select', [CompetitionLevelController::class, 'edit'])->name('levels.select');
        Route::put('/{competition}/levels', [CompetitionLevelController::class, 'update'])->name('levels.update');
        Route::delete('/{competition}/levels/{competitionLevel}', [CompetitionLevelController::class, 'destroy'])->name('levels.destroy');

        // المختبرون
        Route::get('/{competition}/examiners', [CompetitionExaminerController::class, 'index'])->name('examiners');
        Route::get('/{competition}/examiners/search', [CompetitionExaminerController::class, 'searchExaminers'])->name('examiners.search');
        Route::get('/{competition}/examiners/create', [CompetitionExaminerController::class, 'create'])->name('examiners.create');
        Route::post('/{competition}/examiners', [CompetitionExaminerController::class, 'store'])->name('examiners.store');
        Route::get('/{competition}/examiners/{competitionExaminer}/edit', [CompetitionExaminerController::class, 'edit'])->name('examiners.edit');
        Route::put('/{competition}/examiners/{competitionExaminer}', [CompetitionExaminerController::class, 'update'])->name('examiners.update');
        Route::delete('/{competition}/examiners/{competitionExaminer}', [CompetitionExaminerController::class, 'destroy'])->name('examiners.destroy');

        // اختيار الأسئلة الخاصة بمختبر معين
        Route::get('/{competition}/examiners/{competitionExaminer}/questions', [CompetitionExaminerQuestionController::class, 'index'])->name('questions');
        Route::post('/{competition}/examiners/{competitionExaminer}/questions/{question}/toggle-claim', [CompetitionExaminerQuestionController::class, 'toggleClaim'])->name('questions.toggle-claim');

        // أسئلة المستوى (مستقلة عن المختبر — إنشاء/تعديل/حذف)
        Route::get('/{competition}/level-questions', [CompetitionLevelQuestionController::class, 'index'])->name('level-questions');
        Route::get('/{competition}/level-questions/create', [CompetitionLevelQuestionController::class, 'create'])->name('level-questions.create');
        Route::post('/{competition}/level-questions', [CompetitionLevelQuestionController::class, 'store'])->name('level-questions.store');
        Route::get('/{competition}/level-questions/{question}', [CompetitionLevelQuestionController::class, 'show'])->name('level-questions.show');
        Route::get('/{competition}/level-questions/{question}/edit', [CompetitionLevelQuestionController::class, 'edit'])->name('level-questions.edit');
        Route::put('/{competition}/level-questions/{question}', [CompetitionLevelQuestionController::class, 'update'])->name('level-questions.update');
        Route::delete('/{competition}/level-questions/{question}', [CompetitionLevelQuestionController::class, 'destroy'])->name('level-questions.destroy');

        // المشاركون
        Route::get('/participants/search-students', [CompetitionParticipantController::class, 'searchStudents'])->name('participants.search-students');
        Route::get('/participants/search-external', [CompetitionParticipantController::class, 'searchExternalParticipants'])->name('participants.search-external');
        Route::get('/{competition}/participants', [CompetitionParticipantController::class, 'index'])->name('participants');
        Route::get('/{competition}/participants/create', [CompetitionParticipantController::class, 'create'])->name('participants.create');
        Route::get('/{competition}/participants/export', [CompetitionParticipantController::class, 'exportExcel'])->name('participants.export');
        Route::post('/{competition}/participants', [CompetitionParticipantController::class, 'store'])->name('participants.store');
        Route::get('/{competition}/participants/{participant}/edit', [CompetitionParticipantController::class, 'edit'])->name('participants.edit');
        Route::put('/{competition}/participants/{participant}', [CompetitionParticipantController::class, 'update'])->name('participants.update');
        Route::delete('/{competition}/participants/{participant}', [CompetitionParticipantController::class, 'destroy'])->name('participants.destroy');

        // النتائج
        Route::get('/{competition}/results', [CompetitionResultController::class, 'index'])->name('results');
        Route::get('/{competition}/results/export', [CompetitionResultController::class, 'exportExcel'])->name('results.export');

        /*
        |--------------------------------------------------------------------------
        | واجهة عرض النتائج والاختبار (سابقًا كانت تحت prefix admin)
        | تم دمجها هنا تحت نفس prefix('competitions') بدون تكرار كلمة admin
        |--------------------------------------------------------------------------
        */

        // عرض المسابقات ومستوياتها (واجهة العرض العامة، بدون قيود إسناد المختبر)
        Route::get('/overview', [CompetitionAnswerController::class, 'competitions'])->name('overview.index');
        Route::get('/{competition}/overview/levels', [CompetitionAnswerController::class, 'levels'])->name('overview.levels');

        Route::get('/levels/{competitionLevel}/participants', [CompetitionAnswerController::class, 'participants'])->name('level-participants.index');

        Route::get('/participants/{participant}/result', [CompetitionAnswerController::class, 'result'])->name('participants.result');

        /*
        |----------------------------------------------------------------
        | اختبار الأدمن — مستقل تمامًا عن روتس المختبر (examiner.exam.*)
        |----------------------------------------------------------------
        */
        Route::prefix('exam')->name('exam.')->group(function () {
            Route::get('/{participant}', [CompetitionExamAnswerController::class, 'show'])->name('show');
            Route::post('/{participant}', [CompetitionExamAnswerController::class, 'store'])->name('store');
            Route::get('/{participant}/review', [CompetitionExamAnswerController::class, 'review'])->name('review');
            Route::post('/{participant}/finalize', [CompetitionExamAnswerController::class, 'finalize'])->name('finalize');
        });

        /*
        |----------------------------------------------------------------
        | إدخال نتيجة يدوي — بدون المرور على أسئلة الاختبار
        |----------------------------------------------------------------
        */
        Route::get('/participants/{participant}/manual-result', [CompetitionAnswerController::class, 'manualResultForm'])
            ->name('participants.manual-result.form');

        Route::post('/participants/{participant}/manual-result', [CompetitionAnswerController::class, 'storeManualResult'])
            ->name('participants.manual-result.store');
    });
});