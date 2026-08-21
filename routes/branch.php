<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BranchController;

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::resource('branches', BranchController::class);
});
