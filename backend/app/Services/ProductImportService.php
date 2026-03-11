<?php

namespace App\Services;

use App\Models\Import;
use App\Models\Site;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Offer;
use App\Models\PriceHistory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductImportService
{
    public function handle(Import $import)
    {
        $import->update([
            'status' => 'running',
            'started_at' => now(),
            'processed_products' => 0,
            'success_products' => 0,
            'failed_products' => 0,
            'errors' => null
        ]);

        $path = storage_path('app/imports/' . $import->filename);

        if (!file_exists($path)) {
            return $this->failImport($import, 'File not found');
        }

        $data = json_decode(file_get_contents($path), true);

        if (!$data || !is_array($data)) {
            return $this->failImport($import, 'Invalid JSON');
        }

        $import->update([
            'total_products' => count($data)
        ]);

        foreach ($data as $productData) {

            try {

                DB::beginTransaction();

                /*
                |--------------------------------------------------------------------------
                | SITE
                |--------------------------------------------------------------------------
                */
                $siteSlug = $productData['site'] ?? 'unknown';

                $site = Site::firstOrCreate(
                    ['slug' => $siteSlug],
                    [
                        'name' => $productData['site_name'] ?? ucfirst($siteSlug),
                        'base_url' => parse_url($productData['url'] ?? '', PHP_URL_HOST)
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | BRAND
                |--------------------------------------------------------------------------
                */
                $brandName = trim($productData['brand'] ?? 'Unknown');
                $brandSlug = Str::slug($brandName);

                $brand = Brand::firstOrCreate(
                    ['slug' => $brandSlug],
                    ['name' => $brandName]
                );

                /*
                |--------------------------------------------------------------------------
                | PRODUCT
                |--------------------------------------------------------------------------
                */
                $productName = trim($productData['name'] ?? '');

                if (!$productName) {
                    throw new \Exception('Missing product name');
                }

                $normalizedName = $this->normalize($productName);

                // Priorité référence constructeur si présente
                $reference = $productData['mpn']
                    ?? $productData['sku']
                    ?? null;

                if ($reference) {
                    $modelKey = $this->normalize($brandName . ' ' . $reference);
                } else {
                    $modelKey = $this->generateModelKey($brandName, $productName);
                }

                $product = Product::firstOrCreate(
                    [
                        'brand_id' => $brand->id,
                        'model_key' => $modelKey
                    ],
                    [
                        'name' => $productName,
                        'slug' => $this->uniqueSlug($productName),
                        'image' => $productData['image'] ?? null,
                        'normalized_name' => $normalizedName,
                        'site_category_path' => $productData['category_path'] ?? ''
                    ]
                );

                $offersCreated = 0;

                /*
                |--------------------------------------------------------------------------
                | PRODUIT AVEC VARIANTES
                |--------------------------------------------------------------------------
                */
                if (!empty($productData['variants'])) {

                    foreach ($productData['variants'] as $variantData) {

                        $price = $variantData['price'] ?? null;

                        if (!$price || $price <= 0) {
                            continue;
                        }

                        $variantKey = $this->normalize(
                            ($variantData['ean'] ?? '') .
                            ($variantData['sku'] ?? '') .
                            $modelKey
                        );

                        $variant = Variant::firstOrCreate(
                            [
                                'product_id' => $product->id,
                                'normalized_variant' => $variantKey
                            ],
                            [
                                'ean' => $variantData['ean'] ?? null,
                                'sku' => $variantData['sku'] ?? null
                            ]
                        );

                        $offer = Offer::updateOrCreate(
                            [
                                'variant_id' => $variant->id,
                                'site_id' => $site->id
                            ],
                            [
                                'price' => $price,
                                'currency' => $variantData['currency'] ?? 'EUR',
                                'availability' => $variantData['availability'] ?? true,
                                'url' => $productData['url'] ?? '#'
                            ]
                        );

                        PriceHistory::firstOrCreate(
                            [
                                'offer_id' => $offer->id,
                                'price' => $price
                            ]
                        );

                        $offersCreated++;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | PRODUIT SIMPLE
                |--------------------------------------------------------------------------
                */
                if (empty($productData['variants']) && !empty($productData['price'])) {

                    $price = $productData['price'];

                    if ($price > 0) {

                        $variantKey = $this->normalize($modelKey);

                        $variant = Variant::firstOrCreate(
                            [
                                'product_id' => $product->id,
                                'normalized_variant' => $variantKey
                            ]
                        );

                        $offer = Offer::updateOrCreate(
                            [
                                'variant_id' => $variant->id,
                                'site_id' => $site->id
                            ],
                            [
                                'price' => $price,
                                'currency' => $productData['currency'] ?? 'EUR',
                                'availability' => true,
                                'url' => $productData['url'] ?? '#'
                            ]
                        );

                        PriceHistory::firstOrCreate(
                            [
                                'offer_id' => $offer->id,
                                'price' => $price
                            ]
                        );

                        $offersCreated++;
                    }
                }

                if ($offersCreated === 0) {
                    DB::rollBack();
                    continue;
                }

                DB::commit();
                $import->increment('success_products');

            } catch (\Exception $e) {

                DB::rollBack();

                $import->increment('failed_products');

                $import->update([
                    'errors' => ($import->errors ?? '') . "\n" . $e->getMessage()
                ]);
            }

            $import->increment('processed_products');
        }

        $import->update([
            'status' => 'completed',
            'finished_at' => now()
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    private function normalize($value)
    {
        return Str::lower(preg_replace('/[^a-zA-Z0-9]/', '', $value));
    }

    private function generateModelKey($brand, $name)
{
    $brand = strtolower($brand);
    $nameUpper = strtoupper($name);

    preg_match_all('/\b[A-Z0-9\-]{3,}\b/', $nameUpper, $matches);

    $bestCandidate = null;

    foreach ($matches[0] as $candidate) {

        $candidate = str_replace('-', '', $candidate);

        if (
            preg_match('/[A-Z]/', $candidate) &&
            preg_match('/\d/', $candidate)
        ) {
            if (strlen($candidate) >= 3 && strlen($candidate) <= 10) {
                $bestCandidate = $candidate;
                break;
            }
        }
    }

    if ($bestCandidate) {
        return $this->normalize($brand . ' ' . $bestCandidate);
    }

    // fallback nettoyage
    $text = strtolower($brand . ' ' . $name);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);

    $generic = [
        'blouson','casque','gant','gants',
        'veste','pantalon','homme','femme',
        'man','lady','men','women',
        'solid','evo','tech','air',
        'uni','black','white','carbon'
    ];

    foreach ($generic as $word) {
        $text = preg_replace('/\b'.$word.'s?\b/', '', $text);
    }

    return $this->normalize($text);
}

    private function uniqueSlug($name)
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    private function failImport($import, $message)
    {
        $import->update([
            'status' => 'failed',
            'errors' => $message,
            'finished_at' => now()
        ]);
    }
}