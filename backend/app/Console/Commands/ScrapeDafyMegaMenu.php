<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Category;
use Illuminate\Support\Str;

class ScrapeDafyMegaMenu extends Command
{
    protected $signature = 'scrape:dafy-mega-menu';
    protected $description = 'Scrape le vrai mega menu header Dafy';

    public function handle()
    {
        $this->info('🚀 Scraping du méga menu header Dafy');

        // Nettoyage propre
        Category::where('site', 'dafy')->delete();

        $html = Http::get('https://www.dafy-moto.com')->body();
        $crawler = new Crawler($html);

        /**
         * Le mega menu Dafy est dans le header
         * On cible les LI racines du menu
         */
        $crawler
            ->filter('header nav ul')
            ->children('li')
            ->each(function (Crawler $level1) {

                // Lien principal
                if (!$level1->filter('a')->count()) {
                    return;
                }

                $link = $level1->filter('a')->first();

                $name = trim($link->text());
                $url  = $link->attr('href');

                if (!$name || !$url || str_contains($url, '/test')) {
                    return;
                }

                $parent = Category::create([
                    'site'      => 'dafy',
                    'name'      => $name,
                    'slug'      => Str::slug($name),
                    'url'       => $this->absoluteUrl($url),
                    'parent_id' => null,
                ]);

                $this->info("📂 $name");

                /**
                 * Sous-catégories (si mega menu ouvert)
                 */
                if ($level1->filter('ul li a')->count()) {
                    $level1->filter('ul li a')->each(function (Crawler $child) use ($parent) {

                        $childName = trim($child->text());
                        $childUrl  = $child->attr('href');

                        if (
                            !$childName ||
                            !$childUrl ||
                            str_contains($childUrl, '/test')
                        ) {
                            return;
                        }

                        Category::create([
                            'site'      => 'dafy',
                            'name'      => $childName,
                            'slug'      => Str::slug($childName),
                            'url'       => $this->absoluteUrl($childUrl),
                            'parent_id' => $parent->id,
                        ]);

                        $this->line("   └─ $childName");
                    });
                }
            });

        $this->info('✅ Méga menu Dafy importé proprement');
    }

    private function absoluteUrl(string $url): string
    {
        if (str_starts_with($url, 'http')) {
            return $url;
        }

        return 'https://www.dafy-moto.com' . $url;
    }
}

