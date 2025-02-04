<?php

use App\Http\Controllers\AppTopCategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:5,1'])->group(function () {
    Route::get('/appTopCategory', [AppTopCategoryController::class, 'getTopCategoryPositions']);
});
