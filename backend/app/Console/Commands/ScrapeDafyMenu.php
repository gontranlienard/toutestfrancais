<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Category;

class ScrapeDafyMenu extends Command
{
    protected $signature = 'scrape:dafy-menu';
    protected $description = 'Scrape le vrai menu header Dafy';

    public function handle()
    {
        $this->info('🚀 Scraping du vrai menu header Dafy');

        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (compatible; DafyBot/1.0)',
        ])->get('https://www.dafy-moto.com');

        if (!$response->successful()) {
            $this->error('❌ Impossible de charger la page Dafy');
            return;
        }

        $crawler = new Crawler($response->body());

        /**
         * Le menu principal est dans le header
         * On cible UNIQUEMENT les entrées de navigation produits
         */
        $crawler->filter('header nav a[href^="/"]')->each(function (Crawler $node) {

            $name = trim($node->text());
            $href = trim($node->attr('href'));

            if (!$name || !$href) {
                return;
            }

            // On ignore les liens non produits
            if (Str::contains($href, [
                'compte',
                'connexion',
                'panier',
                'magasin',
                'service-client',
                'blog',
                'tests',
                'marques',
                'bons-plans'
            ])) {
                return;
            }

            // URL absolue
            $url = Str::startsWith($href, 'http')
                ? $href
                : 'https://www.dafy-moto.com' . $href;

            Category::updateOrCreate(
                [
                    'site' => 'dafy',
                    'slug' => Str::slug($name),
                ],
                [
                    'name' => $name,
                    'url' => $url,
                    'parent_id' => null,
                ]
            );

            $this->info("📂 $name → $url");
        });

        $this->info('✅ Menu header Dafy importé avec succès');
    }
}




