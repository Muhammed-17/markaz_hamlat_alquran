<?php

use App\Http\Controllers\Admin\CompetitionAdminController;
use App\Http\Controllers\Admin\CompetitionExamAdminController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\CompetitionExaminerController;
use App\Http\Controllers\CompetitionExaminerQuestionController;
use App\Http\Controllers\CompetitionLevelController;
use App\Http\Controllers\CompetitionLevelQuestionController;
use App\Http\Controllers\CompetitionParticipantController;
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
    });

    /*
    |--------------------------------------------------------------------------
    | واجهة الأدمن — عرض فقط (بدون قيود إسناد المختبر)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::prefix('competitions')->name('competitions.')->group(function () {
            Route::get('/', [CompetitionAdminController::class, 'competitions'])->name('index');
            Route::get('/{competition}/levels', [CompetitionAdminController::class, 'levels'])->name('levels');
        });

        Route::prefix('competition-levels')->name('participants.')->group(function () {
            Route::get('/{competitionLevel}/participants', [CompetitionAdminController::class, 'participants'])->name('index');
        });

        Route::get('/participants/{participant}/result', [CompetitionAdminController::class, 'result'])->name('participants.result');

        /*
        |----------------------------------------------------------------
        | اختبار الأدمن — مستقل تمامًا عن روتس المختبر (examiner.exam.*)
        |----------------------------------------------------------------
        */
        Route::prefix('exam')->name('exam.')->group(function () {
            Route::get('/{participant}', [CompetitionExamAdminController::class, 'show'])->name('show');
            Route::post('/{participant}', [CompetitionExamAdminController::class, 'store'])->name('store');
            Route::get('/{participant}/review', [CompetitionExamAdminController::class, 'review'])->name('review');
            Route::post('/{participant}/finalize', [CompetitionExamAdminController::class, 'finalize'])->name('finalize');
        });

        /*
        |----------------------------------------------------------------
        | إدخال نتيجة يدوي — بدون المرور على أسئلة الاختبار
        |----------------------------------------------------------------
        | ملحوظة: كانت هذه المجموعة موضوعة خطأً خارج prefix('admin') في
        | آخر نسخة، فكان اسمها participants.manual-result.form بدل
        | admin.participants.manual-result.form وهو ما كسر الرابط في
        | صفحة المشاركين. تم نقلها هنا داخل مجموعة admin.* الصحيحة.
        */
        Route::prefix('participants')->name('participants.')->group(function () {
            Route::get('/{participant}/manual-result', [CompetitionAdminController::class, 'manualResultForm'])
                ->name('manual-result.form');

            Route::post('/{participant}/manual-result', [CompetitionAdminController::class, 'storeManualResult'])
                ->name('manual-result.store');
        });
    });
});