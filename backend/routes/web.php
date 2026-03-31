<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UnmappedCategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\SearchController;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;


/*
|--------------------------------------------------------------------------
| FRONT
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/search', [SearchController::class, 'index'])
	->name('search');

Route::get('/produit/{slug}', [ProductController::class, 'show'])
    ->name('product.show');

Route::get('/marque/{slug}', [BrandController::class, 'show'])
    ->name('brand.show');
	
Route::get('/marques', [BrandController::class, 'index'])
	->name('brand.index');

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
	
Route::get('/go/{offer}', [AffiliateController::class,'redirect']);

Route::view('/mentions-legales', 'pages.mentions-legales')
	->name('mentions-legales');

Route::view('/cookies', 'legal.cookies')
		->name('cookies');
		
Route::post('/log-event', function (\Illuminate\Http\Request $request) {

    // 🔥 sécurité : on ignore si données absentes
    if (!$request->input('type') || !$request->input('url')) {
        return response()->json(['ignored' => true]);
    }

    // 🔥 IP exclusion (déjà fait)
    $excludedIps = ['37.66.41.54', '127.0.0.1'];

    if (in_array($request->ip(), $excludedIps)) {
        return response()->json(['ignored' => true]);
    }

    DB::table('visitor_logs')->insert([
        'session_id' => session()->getId(),
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'url' => $request->input('url'),
        'referrer' => $request->input('referrer'),
        'event_type' => $request->input('type'),
        'event_value' => $request->input('value'),
        'created_at' => now(),
    ]);

    return response()->json(['ok' => true]);
});

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
	Route::get('/admin/mapping-rules', [UnmappedCategoryController::class, 'rules']);
	Route::post('/admin/mapping-rules/update', [UnmappedCategoryController::class, 'updateRule']);
	Route::post('/admin/mapping-rules/delete', [UnmappedCategoryController::class, 'deleteRule']);
	Route::post('/admin/mapping-rules/delete-bulk', [UnmappedCategoryController::class, 'deleteBulkRules']);
	Route::post('/admin/mapping-rules/create', [UnmappedCategoryController::class, 'createRule']);
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

Route::get('/mon-compte/favoris', [WishlistController::class, 'index'])
    ->middleware('auth')
    ->name('account.wishlist');

Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])
    ->middleware('auth')
    ->name('wishlist.delete');