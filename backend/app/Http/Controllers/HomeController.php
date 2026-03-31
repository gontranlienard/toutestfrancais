<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 🔎 Récupérer les produits présents sur EXACTEMENT 2 sites (mis en cache)
        $comparedProductIds = Cache::remember('products_on_two_sites', 86400, function () {

            return DB::table('products')
                ->join('variants', 'variants.product_id', '=', 'products.id')
                ->join('offers', 'offers.variant_id', '=', 'variants.id')
                ->select('products.id')
                ->groupBy('products.id')
                ->havingRaw('COUNT(DISTINCT offers.site_id) = 2')
                ->pluck('products.id');

        });

        $query = Product::with('brand')
            ->withMin('offers', 'price')
            ->whereIn('id', $comparedProductIds);

        // 🔎 Recherche
        if ($request->filled('q')) {

            $search = trim($request->q);

            $query->where(function ($q) use ($search) {

                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('normalized_name', 'LIKE', "%{$search}%")
                  ->orWhere('ean', 'LIKE', "%{$search}%")
                  ->orWhere('model_reference', 'LIKE', "%{$search}%")
                  ->orWhereHas('brand', function ($b) use ($search) {
                      $b->where('name', 'LIKE', "%{$search}%");
                  });

            });
        }

        $products = $query
            ->latest()
            ->simplePaginate(30)
            ->withQueryString();

        // 🔢 Compteur
        $countCompared = $comparedProductIds->count();

        // ⭐ Wishlist
        $wishlist = [];

        if (Auth::check()) {
            $wishlist = Wishlist::where('user_id', Auth::id())
                ->pluck('variant_id')
                ->toArray();
        }

        return view('home', [
            'products' => $products,
            'wishlist' => $wishlist,
            'countCompared' => $countCompared
        ]);
    }
}