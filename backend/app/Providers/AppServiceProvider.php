<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

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
            | Compter produits par catégorie
            |---------------------------------
            */

            $counts = DB::table('product_category')
                ->select('category_id', DB::raw('COUNT(DISTINCT product_id) as total'))
                ->groupBy('category_id')
                ->pluck('total', 'category_id');

            /*
            |---------------------------------
            | Charger catégories
            |---------------------------------
            */

            $categories = Category::whereNull('parent_id')
                ->with('childrenRecursive')
                ->get();

            /*
            |---------------------------------
            | Calcul récursif parent
            |---------------------------------
            */

            foreach ($categories as $cat) {
                $this->calculateCounts($cat, $counts);
            }

            $view->with('categories', $categories);
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