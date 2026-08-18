<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BehavioralNoteController;

Route::middleware(['auth', 'verified', 'permission:view behavioral notes'])->group(function () {
    Route::get('/behavioral-notes', [BehavioralNoteController::class, 'index'])
        ->name('behavioral-notes.index');

    Route::middleware('permission:create behavioral notes')->group(function () {
        Route::get('/behavioral-notes/create', [BehavioralNoteController::class, 'create'])
            ->name('behavioral-notes.create');
        Route::post('/behavioral-notes', [BehavioralNoteController::class, 'store'])
            ->name('behavioral-notes.store');
    });

    Route::get('/behavioral-notes/{behavioralNote}', [BehavioralNoteController::class, 'show'])
        ->name('behavioral-notes.show');

    Route::middleware('permission:edit behavioral notes')->group(function () {
        Route::get('/behavioral-notes/{behavioralNote}/edit', [BehavioralNoteController::class, 'edit'])
            ->name('behavioral-notes.edit');
        Route::put('/behavioral-notes/{behavioralNote}', [BehavioralNoteController::class, 'update'])
            ->name('behavioral-notes.update');
    });

    Route::middleware('permission:delete behavioral notes')->group(function () {
        Route::delete('/behavioral-notes/{behavioralNote}', [BehavioralNoteController::class, 'destroy'])
            ->name('behavioral-notes.destroy');
    });

    Route::middleware('permission:approve behavioral notes')->group(function () {
        Route::get('/behavioral-notes/{behavioralNote}/action', [BehavioralNoteController::class, 'editAction'])
            ->name('behavioral-notes.edit-action');
        Route::put('/behavioral-notes/{behavioralNote}/action', [BehavioralNoteController::class, 'updateAction'])
            ->name('behavioral-notes.update-action');
    });
});