<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductIngestor;
use Symfony\Component\DomCrawler\Crawler;

class TestInsertDafy extends Command
{
    protected $signature = 'test:dafy {limit=30}';
    protected $description = 'Test insertion de casques Dafy depuis HTML';

    public function handle(ProductIngestor $ingestor)
    {
        $path = storage_path('app/dafy');

        if (!is_dir($path)) {
            $this->error("❌ Dossier introuvable : $path");
            return;
        }

        $files = collect(glob($path.'/*.html'))->take((int)$this->argument('limit'));

        $this->info("📂 {$files->count()} fichiers trouvés");

        foreach ($files as $file) {
            $html = file_get_contents($file);
            $crawler = new Crawler($html);

            try {
                $name = trim($crawler->filter('h1')->text());
                $price = (float) str_replace(',', '.', preg_replace('/[^0-9,]/', '',
                    $crawler->filter('.price')->text()
                ));

                preg_match('/([A-Z]{2,})/', $name, $brandMatch);
                preg_match('/(FF[0-9]+)/', $name, $modelMatch);

                $data = [
                    'site_id' => 1, // Dafy
                    'brand' => $brandMatch[1] ?? 'UNKNOWN',
                    'model_code' => $modelMatch[1] ?? null,
                    'name' => $name,
                    'price' => $price,
                    'url' => 'dafy-local-test',
                    'image' => null,
                ];

                $ingestor->ingest($data);

                $this->line("✅ {$data['brand']} {$data['model_code']} - {$price}€");

            } catch (\Throwable $e) {
                $this->error("❌ Erreur fichier ".basename($file));
            }
        }

        $this->info('🏁 Test terminé');
    }
}
