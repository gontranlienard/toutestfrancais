<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $menuCategories = Category::roots()
            ->with('children')
            ->orderBy('name')
            ->get();

        $query = Product::withMin('offers', 'price')
            ->whereHas('offers');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('brand', 'like', "%$search%");
            });
        }

        $products = $query
            ->orderBy('offers_min_price')
            ->paginate(20);

        return view('front.index', [
            'products' => $products,
            'currentCategory' => null,
            'menuCategories' => $menuCategories
        ]);
    }

    public function category($slug, Request $request)
    {
        $menuCategories = Category::roots()
            ->with('children')
            ->orderBy('name')
            ->get();

        $category = Category::where('slug', $slug)
            ->with('children')
            ->firstOrFail();

        $ids = [$category->id];
        $ids = array_merge($ids, $this->getAllChildrenIds($category));

        $query = Product::withMin('offers', 'price')
            ->whereHas('offers')
            ->whereIn('category_id', $ids);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('brand', 'like', "%$search%");
            });
        }

        $products = $query
            ->orderBy('offers_min_price')
            ->paginate(20);

        return view('front.index', [
            'products' => $products,
            'currentCategory' => $category,
            'menuCategories' => $menuCategories
        ]);
    }

    private function getAllChildrenIds($category)
    {
        $ids = [];

        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getAllChildrenIds($child));
        }

        return $ids;
    }
}
