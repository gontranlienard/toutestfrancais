<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UnmappedCategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\WishlistController;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;


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

Route::post('/wishlist/{variant}', [WishlistController::class,'toggle'])
    ->name('wishlist.toggle');


/*
|--------------------------------------------------------------------------
| ADMIN (protégé)
|--------------------------------------------------------------------------
*/

Route::middleware([\App\Http\Middleware\AdminAuth::class])->group(function () {

    Route::get('/admin/unmapped-categories',
        [UnmappedCategoryController::class, 'index']
    )->name('admin.unmapped.categories');

    Route::post('/admin/unmapped-categories/map',
        [UnmappedCategoryController::class, 'map']
    )->name('admin.categories.map');

    Route::post('/admin/unmapped-categories/map-bulk',
        [UnmappedCategoryController::class, 'mapBulk']
    )->name('admin.categories.mapBulk');

    Route::post('/admin/rebuild-categories',
        [UnmappedCategoryController::class,'rebuildCategories']
    )->name('admin.categories.rebuild');

});


/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store']);

});


Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| DASHBOARD UTILISATEUR
|--------------------------------------------------------------------------
*/

Route::get('/mon-compte', function () {
    return view('account.dashboard');
})
->middleware('auth')
->name('account.dashboard');