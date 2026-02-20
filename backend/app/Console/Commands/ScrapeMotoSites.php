<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Product;
use App\Scrapers\DafyScraper;

class ScrapeMotoSites extends Command
{
    protected $signature = 'scrape:moto';
    protected $description = 'Scraping Dafy multi-catégories';

    public function handle()
    {
        $this->info("🚀 Début du scraping");

        $scraper = new DafyScraper($this);

        foreach ($scraper->getCategories() as $slug => $url) {

            $category = Category::where('slug', $slug)->first();

            if (!$category) {
                $this->warn("⚠️ Catégorie inconnue : {$slug}");
                continue;
            }

            $this->line("📂 Catégorie : {$slug}");

            $urls = $scraper->getProductUrls($url, 3);
            $this->line("📦 URLs trouvées : " . count($urls));

            foreach ($urls as $productUrl) {
                try {
                    $data = $scraper->parseProduct($productUrl, $slug);

                    Product::updateOrCreate(
                        ['link' => $data['link']],
                        [
                            'name'     => $data['name'],
                            'price'    => $data['price'],
                            'images'   => json_encode($data['images']),
                            'site'     => 'dafy',
                        ]
                    );

                    $product = Product::where('link', $data['link'])->first();
                    $product->categories()->syncWithoutDetaching([$category->id]);

                    $this->info("✅ {$data['name']}");

                } catch (\Throwable $e) {
                    $this->warn("⚠️ Produit partiel ignoré : {$productUrl}");
                }
            }
        }

        $this->info("✅ Scraping terminé");
    }
}













