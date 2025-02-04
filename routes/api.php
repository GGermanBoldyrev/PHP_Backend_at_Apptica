<?php

use App\Http\Controllers\AppTopCategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/appTopCategory', [AppTopCategoryController::class, 'getTopCategoryPositions']);
