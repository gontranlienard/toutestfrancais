<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $products = $category->products()
            ->withMin('offers', 'price')
            ->paginate(20);

        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->get();

        return view('category.show', [
            'category'   => $category,
            'products'   => $products,
            'categories' => $categories
        ]);
    }
}
