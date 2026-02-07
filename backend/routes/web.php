<?php

use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $categories = Category::with('products')->get();
    return view('home', compact('categories'));
});

Route::get('/lang/{lang}', function ($lang) {
    session(['locale' => $lang]);
    return redirect('/');
});
