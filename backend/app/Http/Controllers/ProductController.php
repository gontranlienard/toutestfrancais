<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    // PAGE ACCUEIL
    public function index()
    {
        $products = Product::with([
                'brand',
                'variants.offers'
            ])
            ->inRandomOrder()
            ->paginate(30);

        return view('home', compact('products'));
    }

    // PAGE PRODUIT
    public function show($slug)
{
    $product = \App\Models\Product::with([
        'brand',
        'variants.offers.site'
    ])
    ->where('slug', $slug)
    ->firstOrFail();

    // 🔥 On récupère toutes les offres de toutes les variantes
    $allOffers = collect();

    foreach ($product->variants as $variant) {
        foreach ($variant->offers as $offer) {
            $allOffers->push($offer);
        }
    }

    // 🔥 Grouper par site et garder la moins chère
    $offers = $allOffers
        ->groupBy('site_id')
        ->map(function ($offersPerSite) {
            return $offersPerSite->sortBy('price')->first();
        })
        ->sortBy('price');

    return view('product.show', compact('product', 'offers'));
}

    // PAGE CATEGORIE
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $products = $category->products()
            ->with(['brand', 'variants.offers'])
            ->latest()
            ->paginate(12)
            ->withQueryString(); // IMPORTANT pour garder paramètres

        return view('home', compact('products', 'category'));
    }
}

