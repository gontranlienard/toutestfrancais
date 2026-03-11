<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\CategoryMappingRule;

class HybridAutoCategorize extends Command
{
    protected $signature = 'products:auto-categorize-hybrid';
    protected $description = 'Auto catégorisation hybride via URL + nom produit';

    public function handle()
    {
        $rules = CategoryMappingRule::orderByDesc('priority')->get();

        $products = Product::with('variants.offers', 'categories')->get();

        foreach ($products as $product) {

            // Skip si déjà catégorisé
            if ($product->categories->count()) {
                continue;
            }

            $matched = false;

            /*
            |--------------------------------------------------------------------------
            | 1️⃣ Matching via URL (PRIORITÉ HAUTE)
            |--------------------------------------------------------------------------
            */
            foreach ($product->variants as $variant) {

                foreach ($variant->offers as $offer) {

                    $url = strtolower($offer->url ?? '');

                    if (!$url) {
                        continue;
                    }

                    $path = parse_url($url, PHP_URL_PATH);
                    $segments = explode('/', trim($path, '/'));

                    foreach ($rules as $rule) {

                        $keyword = strtolower($rule->keyword);

                        foreach ($segments as $segment) {

                            if (str_contains($segment, $keyword)) {

                                $product->categories()
                                    ->syncWithoutDetaching([$rule->category_id]);

                                $this->info("URL match → {$product->name}");
                                $matched = true;

                                break 4; // sort de toutes les boucles
                            }
                        }
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 2️⃣ Fallback via nom produit
            |--------------------------------------------------------------------------
            */
            if (!$matched) {

                $name = strtolower($product->name);

                foreach ($rules as $rule) {

                    if (str_contains($name, strtolower($rule->keyword))) {

                        $product->categories()
                            ->syncWithoutDetaching([$rule->category_id]);

                        $this->info("NAME match → {$product->name}");

                        break;
                    }
                }
            }
        }

        $this->info('Catégorisation hybride terminée.');
    }
}
