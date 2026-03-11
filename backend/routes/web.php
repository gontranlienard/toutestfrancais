<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UnmappedCategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| FRONT
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/produit/{slug}', [ProductController::class, 'show'])
    ->name('product.show');

Route::get('/marque/{slug}', [BrandController::class, 'show'])
    ->name('brand.show');

Route::get('/categorie/{path}', [CategoryController::class, 'show'])
    ->where('path', '.*')
    ->name('category.show');

Route::view('/cgu', 'pages.cgu')->name('cgu');

Route::get('/contact', [ContactController::class, 'show'])
    ->name('contact');

Route::post('/contact', [ContactController::class, 'send'])
    ->name('contact.send');


/*
|--------------------------------------------------------------------------
| ADMIN (protégé)
|--------------------------------------------------------------------------
*/

Route::middleware([\App\Http\Middleware\AdminAuth::class])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Page catégories non mappées
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/unmapped-categories',
        [UnmappedCategoryController::class, 'index']
    )->name('admin.unmapped.categories');


    /*
    |--------------------------------------------------------------------------
    | Mapping simple
    |--------------------------------------------------------------------------
    */

    Route::post('/admin/unmapped-categories/map',
        [UnmappedCategoryController::class, 'map']
    )->name('admin.categories.map');


    /*
    |--------------------------------------------------------------------------
    | Mapping par lot (nouvelle fonctionnalité)
    |--------------------------------------------------------------------------
    */

    Route::post('/admin/unmapped-categories/map-bulk',
        [UnmappedCategoryController::class, 'mapBulk']
    )->name('admin.categories.mapBulk');
        Route::post('/admin/rebuild-categories',
                [UnmappedCategoryController::class,'rebuildCategories']
        )->name('admin.categories.rebuild');

});

