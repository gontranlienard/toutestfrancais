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

    // 🔥 NOUVELLE PAGE A → Z
    public function index()
    {
        $brands = Brand::orderBy('name')->get(); // ⚠️ TOUTES les marques

        $grouped = $brands->groupBy(function ($brand) {
            return strtoupper(substr($brand->name, 0, 1));
        });

        $letters = range('A', 'Z');

        return view('brand.index', [
            'grouped' => $grouped,
            'letters' => $letters
        ]);
    }
}