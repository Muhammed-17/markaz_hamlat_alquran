<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurahTestController;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/surah-tests', [SurahTestController::class, 'indexIndividual'])
        ->name('surah-tests.index.individual');

    Route::get('/surah-tests/group', [SurahTestController::class, 'indexGroup'])
        ->name('surah-tests.index.group');

    Route::get('/surah-tests/create/individual', [SurahTestController::class, 'create'])
        ->name('surah-tests.create.individual')
        ->defaults('type', 'individual');

    Route::get('/surah-tests/create/group', [SurahTestController::class, 'create'])
        ->name('surah-tests.create.group')
        ->defaults('type', 'group');

    Route::get('/surah-tests/create', [SurahTestController::class, 'create'])->name('surah-tests.create');

    Route::post('/surah-tests', [SurahTestController::class, 'store'])->name('surah-tests.store');

    Route::get('/surah-tests/repeat-students', [SurahTestController::class, 'repeatStudents'])
        ->name('surah-tests.repeat-students');

    Route::get('/surah-tests/{surah_test}', [SurahTestController::class, 'show'])->name('surah-tests.show');
    Route::get('/surah-tests/{surah_test}/edit', [SurahTestController::class, 'edit'])->name('surah-tests.edit');
    Route::put('/surah-tests/{surah_test}', [SurahTestController::class, 'update'])->name('surah-tests.update');
    Route::delete('/surah-tests/{surah_test}', [SurahTestController::class, 'destroy'])->name('surah-tests.destroy');

    Route::get('/circles/{circle}/students', [SurahTestController::class, 'studentsForCircle'])
        ->name('circles.students');
});
