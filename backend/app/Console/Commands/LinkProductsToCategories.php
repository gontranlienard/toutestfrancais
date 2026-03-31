<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LinkProductsToCategories extends Command
{
   	protected $signature = 'products:link-categories';
    protected $description = 'Classe automatiquement les produits dans les catégories';

    private $categories;
    private $categoriesByName;
    private $rules;

    // 🔥 MATCH EXACT (mot complet, + pluriel simple)
    private function matchKeyword($text, $kw)
    {
        $kw = Str::lower($kw);

        return preg_match('/\b' . preg_quote($kw, '/') . 's?\b/', $text);
    }

    // 🔥 nettoyage des keywords
    private function cleanKeyword($k)
    {
        $k = Str::lower($k);
        $k = str_replace(['>', '/', '-', '_'], ' ', $k);
        $k = preg_replace('/[^a-z0-9 ]/', '', $k);
        return trim($k);
    }

    public function handle()
    {
        $this->info("🚀 Début classification produits");

        /*
        |--------------------------------------------------------------------------
        | RESET TABLES
        |--------------------------------------------------------------------------
        */

        DB::table('product_category')->truncate();
        DB::table('unmapped_categories')->truncate();

        /*
        |--------------------------------------------------------------------------
        | LOAD DATA
        |--------------------------------------------------------------------------
        */

        $this->categories = Category::all()->keyBy('id');

        $this->categoriesByName = Category::all()
            ->keyBy(fn($c) => Str::lower(trim($c->name)));

        $this->rules = DB::table('category_mapping_rules')->get();

        /*
        |--------------------------------------------------------------------------
        | PROCESS PRODUCTS
        |--------------------------------------------------------------------------
        */

        Product::chunk(500, function ($products) {

            foreach ($products as $product) {

                $title = Str::lower($product->name);
                $title = str_replace(['>', '/', '-', '_'], ' ', $title);
                $title = preg_replace('/[^a-z0-9 ]/', '', $title);
                $title = trim($title);

                $categoryId = null;

                /*
                |--------------------------------------------------------------------------
                | 1. PRIORITÉ AUX RÈGLES (SCORING)
                |--------------------------------------------------------------------------
                */

                $bestMatch = null;
                $bestScore = 0;

                foreach ($this->rules as $rule) {

                    $keywords = array_filter(array_map(function ($k) {
                        $k = Str::lower($k);
                        $k = str_replace(['>', '/', '-', '_'], ' ', $k);
                        $k = preg_replace('/[^a-z0-9 ]/', '', $k);
                        return trim($k);
                    }, explode(',', $rule->keyword)));

                    if (count($keywords) === 0) {
                        continue;
                    }

                    $matches = 0;

                    foreach ($keywords as $kw) {
                        if ($this->matchKeyword($title, $kw)) {
                            $matches++;
                        }
                    }

                    // 🔥 PRIORITÉ MATCH COMPLET
                    if ($matches === count($keywords)) {
                        $categoryId = $rule->category_id;
                        break;
                    }

                    // fallback scoring (moins important)
                    if ($matches > $bestScore) {
                        $bestScore = $matches;
                        $bestMatch = $rule;
                    }
                }

                if (!$categoryId && $bestMatch && $bestScore > 1) {
                    $categoryId = $bestMatch->category_id;
                }

                /*
                |--------------------------------------------------------------------------
                | 2. MATCH RÈGLES SUR SITE_CATEGORY_PATH
                |--------------------------------------------------------------------------
                */

                if (!$categoryId && $product->site_category_path) {

                    $path = Str::lower($product->site_category_path);
                    $path = str_replace(['>', '/', '-', '_'], ' ', $path);
                    $path = preg_replace('/[^a-z0-9 ]/', '', $path);
                    $path = trim($path);

                    foreach ($this->rules as $rule) {

                        $keywords = array_filter(array_map(
                            fn($k) => $this->cleanKeyword($k),
                            preg_split('/\s*,\s*/', $rule->keyword)
                        ));

                        if (count($keywords) === 0) {
                            continue;
                        }

                        $matches = 0;

                        foreach ($keywords as $kw) {
                            if ($this->matchKeyword($path, $kw)) {
                                $matches++;
                            }
                        }

                        if ($matches === count($keywords)) {
                            $categoryId = $rule->category_id;
                            break;
                        }
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | 3. FALLBACK OPTIMISÉ
                |--------------------------------------------------------------------------
                */

                if (!$categoryId && $product->site_category_path) {

                    $path = str_replace(' / ', ' > ', $product->site_category_path);
                    $parts = explode('>', $path);

                    $parts = array_reverse($parts);

                    $ignore = [
                        'sport', 'road', 'racing', 'performance',
                        'accessoire', 'accessoires', 'moto',
                        'piece', 'pieces', 'pneus', 'pneu'
                    ];

                    foreach ($parts as $part) {

                        $clean = Str::lower(trim($part));

                        if (in_array($clean, $ignore)) {
                            continue;
                        }

                        if (isset($this->categoriesByName[$clean])) {

                            $found = $this->categoriesByName[$clean];

                            if ($found->children()->count() === 0) {
                                $categoryId = $found->id;
                                break;
                            }
                        }
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | 4. INSERTION
                |--------------------------------------------------------------------------
                */

                if ($categoryId) {

                    DB::table('product_category')->updateOrInsert([
                        'product_id' => $product->id,
                        'category_id' => $categoryId
                    ]);

                } else {

                    $siteId = DB::table('offers')
                        ->join('variants', 'variants.id', '=', 'offers.variant_id')
                        ->where('variants.product_id', $product->id)
                        ->value('offers.site_id');

                    if (!$siteId) {
                        continue;
                    }

                    $rawCategory = $product->site_category_path ?: 'inconnue';

                    $existing = DB::table('unmapped_categories')
                        ->where('raw_category', $rawCategory)
                        ->where('site_id', $siteId)
                        ->first();

                    if ($existing) {

                        DB::table('unmapped_categories')
                            ->where('id', $existing->id)
                            ->update([
                                'occurrences' => $existing->occurrences + 1,
                                'example_product_id' => $product->id,
                                'updated_at' => now(),
                            ]);

                    } else {

                        DB::table('unmapped_categories')->insert([
                            'raw_category' => $rawCategory,
                            'site_id' => $siteId,
                            'occurrences' => 1,
                            'example_product_id' => $product->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

        });

        $this->info("✅ Classification terminée");
    }
}