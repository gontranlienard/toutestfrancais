<?php

namespace App\Http\Controllers;

use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();

        return view('home', compact('categories'));
    }
}
