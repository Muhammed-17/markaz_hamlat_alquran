<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CircleController;

Route::resource('/circles', CircleController::class)->middleware(['auth', 'verified']);