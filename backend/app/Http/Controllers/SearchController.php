<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->q);
        $sort = $request->sort ?? 'relevance';

        $products = Product::query()
            ->with('brand', 'variants')
            ->withCount('offers')
            ->withMin('offers', 'price');

        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if (!empty($search)) {

            $searchLower = strtolower($search);
            $words = array_values(array_filter(explode(' ', $searchLower)));

            if (!empty($words)) {

                // 🔥 mot principal (type produit)
                $main = array_shift($words);

                // 🔹 FILTRE PRINCIPAL (obligatoire)
                $products->where(function ($query) use ($main) {
                    $query->where('products.name', 'like', "%{$main}%")
                          ->orWhere('products.normalized_name', 'like', "%{$main}%");
                });

                // 🔹 FILTRES SECONDAIRES
                foreach ($words as $word) {

                    if (strlen($word) < 3) continue;

                    $products->where(function ($sub) use ($word) {

                        $sub->where('products.name', 'like', "%{$word}%")
                            ->orWhere('products.normalized_name', 'like', "%{$word}%")

                            // 🔥 MARQUE
                            ->orWhereHas('brand', function ($b) use ($word) {
                                $b->where('name', 'like', "%{$word}%");
                            })

                            // 🔥 VARIANTS
                            ->orWhereHas('variants', function ($v) use ($word) {
                                $v->where('color', 'like', "%{$word}%")
                                  ->orWhere('size', 'like', "%{$word}%");
                            });

                    });
                }

                /*
                |--------------------------------------------------------------------------
                | SCORE DE PERTINENCE
                |--------------------------------------------------------------------------
                */

                $scoreSql = "CASE";

                // 🥇 match exact
                $scoreSql .= " WHEN products.name LIKE '%{$searchLower}%' THEN 100";

                // 🥈 produit + marque
                foreach ($words as $word) {
                    $scoreSql .= " WHEN products.name LIKE '%{$main}%' 
                                    AND EXISTS (
                                        SELECT 1 FROM brands 
                                        WHERE brands.id = products.brand_id
                                        AND brands.name LIKE '%{$word}%'
                                    ) THEN 90";
                }

                // 🥈 produit + variante
                foreach ($words as $word) {
                    $scoreSql .= " WHEN products.name LIKE '%{$main}%' 
                                    AND EXISTS (
                                        SELECT 1 FROM variants 
                                        WHERE variants.product_id = products.id
                                        AND (
                                            variants.color LIKE '%{$word}%'
                                            OR variants.size LIKE '%{$word}%'
                                        )
                                    ) THEN 80";
                }

                // 🥉 produit seul
                $scoreSql .= " WHEN products.name LIKE '%{$main}%' THEN 50";

                $scoreSql .= " ELSE 10 END as relevance_score";

                $products->select('products.*')
                         ->selectRaw($scoreSql);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TRI
        |--------------------------------------------------------------------------
        */

        if ($sort === 'price_asc') {
            $products->orderBy('offers_min_price', 'asc');
        } elseif ($sort === 'price_desc') {
            $products->orderBy('offers_min_price', 'desc');
        } else {
            // 🔥 TRI PAR PERTINENCE
            $products->orderByDesc('relevance_score')
                     ->orderBy('offers_min_price', 'asc');
        }

        $products = $products->paginate(30)->withQueryString();

        return view('search', compact('products'));
    }
}