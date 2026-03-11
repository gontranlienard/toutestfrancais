<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('brand');

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
            ->paginate(30)
            ->withQueryString();

        return view('home', compact('products'));
    }
}