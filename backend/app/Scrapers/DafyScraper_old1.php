<?php

namespace App\Scrapers;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

trait DafyScraper
{
    protected $baseUrl = 'https://www.dafy-moto.com';
    protected $categoryUrl = '/casques/casques-moto.html';
    protected $client;

    public function scrapeDafy()
    {
        $this->client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
            ]
        ]);

        // 1️⃣ Récupère toutes les URLs des produits
        $productUrls = $this->getAllProductUrls();

        foreach ($productUrls as $url) {
            try {
                $this->scrapeProduct($url);
            } catch (\Exception $e) {
                \Log::error("Erreur scraping $url: ".$e->getMessage());
            }
        }

        return "Scraping Dafy terminé !";
    }

    protected function getAllProductUrls()
    {
        $urls = [];
        $page = 1;

        do {
            $response = $this->client->get($this->baseUrl . $this->categoryUrl . "?p=$page");
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            $links = $crawler->filter('.product-card__title a')->each(function (Crawler $node) {
                return $this->baseUrl . $node->attr('href');
            });

            $urls = array_merge($urls, $links);
            $page++;

        } while (!empty($links)); // stop si plus de produits

        // Debug pour voir les URLs trouvées
        dd($urls);

        return $urls;
    }

    protected function scrapeProduct($url)
    {
        $response = $this->client->get($url);
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        // Debug du HTML complet pour vérifier ce qu'on récupère
        dd($crawler->html());

        // Exemple pour déboguer les données exactes du produit
        $data = [
            'brand' => $crawler->filter('.product-sheet__brand')->text(),
            'name' => $crawler->filter('span[itemprop="name"]')->text(),
            'color' => $crawler->filter('.js-current-color-title')->text(),
            'price' => $crawler->filter('meta[itemprop="price"]')->attr('content'),
            'currency' => $crawler->filter('meta[itemprop="priceCurrency"]')->attr('content'),
            'images' => $crawler->filter('div.carousel__item img')->each(function ($img) {
                return $img->attr('src');
            }),
            'sizes' => $crawler->filter('input[name="attribute2-value"]')->each(function ($node) {
                $label = $node->nextAll()->filter('label span')->first();
                return $label->count() ? $label->text() : null;
            }),
        ];
        $data['sizes'] = array_filter($data['sizes']);

        // Debug pour voir le produit
        dd($data);

        // Insère ou met à jour le produit en base
        \DB::table('products')->updateOrInsert(
            ['name' => $data['name'], 'color' => $data['color']],
            [
                'brand' => $data['brand'],
                'price' => $data['price'],
                'currency' => $data['currency'],
                'images' => json_encode($data['images']),
                'sizes' => json_encode($data['sizes']),
                'url' => $url
            ]
        );

        echo "Produit ajouté : {$data['brand']} - {$data['name']} - {$data['color']}\n";
    }
}
