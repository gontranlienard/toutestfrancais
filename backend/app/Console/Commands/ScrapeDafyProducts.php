<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class ImportDafyProducts extends Command
{
    protected $signature = 'import:dafy-products';
    protected $description = 'Import produits Dafy depuis JSON';

    public function handle()
    {
        $file = base_path('scraper/output/dafy-products.json');

        if (!file_exists($file)) {
            $this->error('Fichier JSON introuvable');
            return;
        }

        $products = json_decode(file_get_contents($file), true);

        foreach ($products as $p) {
            Product::firstOrCreate(
                ['url' => $p['url']],
                [
                    'site' => 'dafy',
                    'name' => basename($p['url']),
                    'category_slug' => $p['category']
                ]
            );
        }

        $this->info('✅ Produits Dafy importés');
    }
}

