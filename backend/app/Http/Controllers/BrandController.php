<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;

class BrandController extends Controller
{
    public function show($slug)
    {
        $brand = Brand::where('slug', $slug)->firstOrFail();

        $products = Product::where('brand_id', $brand->id)
            ->with(['brand', 'variants.offers'])
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('brand.show', [
            'brand' => $brand,
            'products' => $products
        ]);
    }
}