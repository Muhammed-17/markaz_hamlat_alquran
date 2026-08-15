<?php

use App\Http\Controllers\ExaminerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::resource('examiners', ExaminerController::class);
});