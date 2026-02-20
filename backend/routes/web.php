<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

Route::get('/categorie/{slug}', [CategoryController::class, 'show'])
    ->name('category.show');
