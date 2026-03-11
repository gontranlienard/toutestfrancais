<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->q;
        $sort = $request->sort ?? 'relevance';

        $products = Product::query()
            ->withCount('offers')
            ->withMin('offers', 'price')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('brand', 'like', "%{$query}%")
                        ->orWhere('ean', 'like', "%{$query}%");
                });
            });

        if ($sort === 'price_asc') {
            $products->orderBy('offers_min_price', 'asc');
        } elseif ($sort === 'price_desc') {
            $products->orderBy('offers_min_price', 'desc');
        } else {
            $products->orderBy('offers_count', 'desc');
        }

        $products = $products->paginate(24)->withQueryString();

        return view('search', compact('products'));
    }
}

