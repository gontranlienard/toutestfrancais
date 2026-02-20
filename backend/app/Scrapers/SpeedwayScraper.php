<?php

namespace App\Scrapers;

use App\Scrapers\Contracts\MotoScraperInterface;
use Symfony\Component\DomCrawler\Crawler;

class SpeedwayScraper extends BaseScraper implements MotoScraperInterface
{
    protected string $baseUrl = 'https://www.speedway.fr';

    public function getSiteCode(): string
    {
        return 'speedway';
    }

    /**
     * Catégories Speedway
     */
    public function getCategoryUrls(): array
    {
        return [
            'casques'  => $this->baseUrl . '/casques',
            'gants'    => $this->baseUrl . '/gants',
            'blousons' => $this->baseUrl . '/blousons',
        ];
    }

    /**
     * Récupère les URLs produits d'une catégorie
     */
    public function getProductUrls(string $categoryUrl): array
    {
        $urls = [];
        $page = 1;

        do {
            $html = $this->getHtml($categoryUrl . '?page=' . $page);
            $crawler = $this->crawler($html);

            $links = $crawler
                ->filter('.product-card a.product-card__link')
                ->each(function (Crawler $node) {
                    return $this->baseUrl . $node->attr('href');
                });

            $urls = array_merge($urls, $links);
            $page++;

        } while (count($links) > 0 && $page <= 25); // sécurité

        return array_unique($urls);
    }

    /**
     * Parsing fiche produit Speedway
     */
    public function parseProduct(string $url): array
    {
        $crawler = $this->crawler($this->getHtml($url));

        $brand = trim(
            $crawler->filter('.product-brand')->count()
                ? $crawler->filter('.product-brand')->text()
                : 'Inconnu'
        );

        $name = trim($crawler->filter('h1')->text());

        $price = (float) str_replace(
            ',',
            '.',
            preg_replace(
                '/[^0-9,]/',
                '',
                $crawler->filter('.price')->first()->text()
            )
        );

        $images = $crawler
            ->filter('.product-gallery img')
            ->each(fn (Crawler $n) => $n->attr('src'));

        return [
            'site'   => 'speedway',
            'brand'  => $brand,
            'model'  => $name,
            'price'  => $price,
            'images' => array_values(array_unique($images)),
            'link'   => $url,
        ];
    }
}


