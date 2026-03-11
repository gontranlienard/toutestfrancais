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
    protected $description = 'Classe automatiquement les produits dans les catégories (intelligent)';

    private $categories;
    private $categoriesByName;
    private $leafCategories;

    private $siteRules = [];
    private $globalRules;

    public function handle()
    {
        $this->info("🚀 Début classification produits");
		// 🔥 reset unmapped
		DB::table('unmapped_categories')->truncate();

        // 🔥 Cache catégories
        $this->categories = Category::all()->keyBy('id');

        $this->categoriesByName = Category::all()
            ->keyBy(fn($c) => Str::lower(trim($c->name)));

        // 🔥 Détection leaf en mémoire
        $parentIds = Category::whereNotNull('parent_id')->pluck('parent_id')->toArray();
        $this->leafCategories = $this->categories->reject(fn($c) => in_array($c->id, $parentIds));

        // 🔥 règles globales
        $this->globalRules = DB::table('category_mapping_rules')
            ->whereNull('site_id')
            ->orderByDesc('priority')
            ->get();

        // 🔥 règles par site
        $rules = DB::table('category_mapping_rules')
            ->whereNotNull('site_id')
            ->orderByDesc('priority')
            ->get()
            ->groupBy('site_id');

        $this->siteRules = $rules->toArray();

        Product::chunk(500, function ($products) {

            foreach ($products as $product) {

                $category = $this->findCategory($product);

                if ($category) {

                    $this->attachCategory($product->id, $category);

                } else {

                    $this->storeUnmapped($product);
                }
            }
        });

        $this->info("✅ Classification terminée");
    }

    private function findCategory($product)
    {
        $text = Str::lower(
            ($product->site_category_path ?? '') . ' ' .
            ($product->name ?? '') . ' ' .
            ($product->normalized_name ?? '')
        );

        /*
        |--------------------------------------------------------------------------
        | 🔎 récupérer site_id du produit
        |--------------------------------------------------------------------------
        */

        $siteId = DB::table('offers')
            ->join('variants','variants.id','=','offers.variant_id')
            ->where('variants.product_id',$product->id)
            ->value('site_id');

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ règles spécifiques au site
        |--------------------------------------------------------------------------
        */

        if ($siteId && isset($this->siteRules[$siteId])) {

            foreach ($this->siteRules[$siteId] as $rule) {

                if (!$rule->keyword || !$rule->category_id) {
                    continue;
                }

                if (Str::contains($text, Str::lower($rule->keyword))) {

                    $category = $this->categories[$rule->category_id] ?? null;

                    if ($category) {
                        return $category;
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ règles globales
        |--------------------------------------------------------------------------
        */

        foreach ($this->globalRules as $rule) {

            if (!$rule->keyword || !$rule->category_id) {
                continue;
            }

            if (Str::contains($text, Str::lower($rule->keyword))) {

                $category = $this->categories[$rule->category_id] ?? null;

                if ($category) {
                    return $category;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ Mapping via site_category_path
        |--------------------------------------------------------------------------
        */

        if (!empty($product->site_category_path)) {

            $parts = explode('/', $product->site_category_path);

            foreach ($parts as $part) {

                $normalized = Str::lower(trim($part));

                foreach ($this->leafCategories as $category) {

                    if (Str::contains($normalized, Str::lower($category->name))) {
                        return $category;
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ fallback keywords
        |--------------------------------------------------------------------------
        */

        $fallback = [
            'casque' => 'casques',
            'gants' => 'gants',
            'blouson' => 'blousons',
            'veste' => 'blousons',
            'botte' => 'bottes & chaussures',
            'chaussure' => 'bottes & chaussures',
            'pantalon' => 'pantalons',
            'combinaison' => 'combinaisons',
            'dorsale' => 'protections',
        ];

        foreach ($fallback as $word => $catName) {

            if (Str::contains($text, $word)) {

                $category = $this->categoriesByName[Str::lower($catName)] ?? null;

                if ($category) {
                    return $category;
                }
            }
        }

        return null;
    }

    private function attachCategory($productId, $category)
    {
        DB::table('product_category')->updateOrInsert(
            [
                'product_id' => $productId,
                'category_id' => $category->id
            ],
            []
        );
    }

    private function storeUnmapped($product)
    {
        $siteId = DB::table('offers')
            ->join('variants','variants.id','=','offers.variant_id')
            ->where('variants.product_id',$product->id)
            ->value('site_id');

        if (!$siteId) {
            return;
        }

        $rawCategory = $product->site_category_path ?? 'unknown';

        DB::table('unmapped_categories')->updateOrInsert(
            [
                'site_id' => $siteId,
                'raw_category' => $rawCategory
            ],
            [
                'occurrences' => DB::raw('occurrences + 1'),
                'updated_at' => now(),
                'created_at' => now()
            ]
        );
    }
}