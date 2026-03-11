<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    public function show($path)
    {
        $slugs = explode('/', $path);

        $parentId = null;
        $category = null;

        foreach ($slugs as $slug) {
            $category = Category::where('slug', $slug)
                ->where('parent_id', $parentId)
                ->firstOrFail();

            $parentId = $category->id;
        }

        /*
        |--------------------------------------------------------------------------
        | Récupérer TOUS les descendants récursivement
        |--------------------------------------------------------------------------
        */

        $categoryIds = $this->getAllDescendantIds($category);

        /*
        |--------------------------------------------------------------------------
        | Produits (exactement comme la home)
        |--------------------------------------------------------------------------
        */

        $products = Product::whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->with(['brand', 'variants.offers'])
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('category.show', [
            'category' => $category,
            'products' => $products
        ]);
    }

    private function getAllDescendantIds($category)
    {
        $ids = collect([$category->id]);

        foreach ($category->children as $child) {
            $ids = $ids->merge($this->getAllDescendantIds($child));
        }

        return $ids;
    }
}