<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;
use App\Models\Brand;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {

            /*
            |---------------------------------
            | Extraction des marques
            |---------------------------------
            */
			$brands = cache()->remember('brands_with_real_competition', 3600, function () {

                return DB::table('brands')
					->where('visible', 1)
                    ->whereExists(function ($query) {

                        $query->select(DB::raw(1))
                            ->from('products')
                            ->join('variants', 'variants.product_id', '=', 'products.id')
                            ->join('offers', 'offers.variant_id', '=', 'variants.id')
                            ->whereColumn('products.brand_id', 'brands.id')
                            ->groupBy('products.id')
                            ->havingRaw('COUNT(DISTINCT offers.site_id) >= 2');

                    })
                    ->orderBy('name')
                    ->get();;
});

$view->with('brands', $brands);
						
			/*
            |---------------------------------
            | Compter produits par catégorie
            |---------------------------------
            */

            $counts = cache()->remember('category_product_counts', 3600, function () {

				return DB::table('product_category')
					->select('category_id', DB::raw('COUNT(DISTINCT product_id) as total'))
					->groupBy('category_id')
					->pluck('total', 'category_id');

			});

            /*
            |---------------------------------
            | Charger catégories
            |---------------------------------
            */

			$categories = cache()->remember('categories_tree', 3600, function () {

				return Category::whereNull('parent_id')
					->with('childrenRecursive')
					->get();

			});

            /*
            |---------------------------------
            | Calcul récursif parent
            |---------------------------------
            */

            foreach ($categories as $cat) {
                $this->calculateCounts($cat, $counts);
            }

            /*
            |---------------------------------
            | Wishlist utilisateur
            |---------------------------------
            */

            $wishlist = [];

            if (Auth::check()) {
                $wishlist = Wishlist::where('user_id', Auth::id())
                    ->pluck('variant_id')
                    ->toArray();
            }

            /*
            |---------------------------------
            | Envoyer aux vues
            |---------------------------------
            */

            $view->with([
                'categories' => $categories,
                'wishlist' => $wishlist
            ]);
        });
    }

    private function calculateCounts($category, $counts)
    {
        $direct = $counts[$category->id] ?? 0;

        if (!$category->childrenRecursive->count()) {

            $category->product_count = $direct;

            return $direct;
        }

        $total = 0;

        foreach ($category->childrenRecursive as $child) {

            $total += $this->calculateCounts($child, $counts);

        }

        $category->product_count = $total;

        return $total;
    }
}