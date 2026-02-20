<?php

namespace App\Scrapers;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class DafyScraper
{
    protected Client $client;
    protected $command;

    protected string $baseUrl = 'https://www.dafy-moto.com';

    public function __construct($command = null)
    {
        $this->command = $command;
        $this->client = new Client([
            'timeout' => 20,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (MotoComparatorBot)',
            ],
        ]);
    }

    public function getCategories(): array
    {
        return [
            'casques-moto' => $this->baseUrl . '/casques/casques-moto.html',
            'gants'        => $this->baseUrl . '/equipement-moto/gants-moto.html',
            'blousons'     => $this->baseUrl . '/equipement-moto/blousons-moto.html',
        ];
    }

    public function getProductUrls(string $categoryUrl, int $maxPages = 3): array
    {
        $urls = [];

        for ($page = 1; $page <= $maxPages; $page++) {
            $url = $categoryUrl . '?p=' . $page;
            $this->command?->line("📄 Page {$page} → {$url}");

            try {
                $html = $this->client->get($url)->getBody()->getContents();
            } catch (\Throwable) {
                break;
            }

            $crawler = new Crawler($html);

            $crawler->filter('a[href^="/"]')->each(function ($a) use (&$urls) {
                $href = $a->attr('href');

                if (
                    str_contains($href, '-all-one') ||
                    str_contains($href, '-scorpion') ||
                    str_contains($href, '-ls2') ||
                    str_contains($href, '-arai') ||
                    str_contains($href, '-hjc') ||
                    str_contains($href, '-shark')
                ) {
                    $urls[] = 'https://www.dafy-moto.com' . strtok($href, '?');
                }
            });
        }

        return array_values(array_unique($urls));
    }

    public function parseProduct(string $url, string $category): array
    {
        $html = $this->client->get($url)->getBody()->getContents();
        $crawler = new Crawler($html);

        if (!$crawler->filter('h1')->count()) {
            throw new \Exception('Nom absent');
        }

        $name = trim($crawler->filter('h1')->text());

        /* ---------- PRIX (fallbacks) ---------- */
        $price = null;

        if ($crawler->filter('[itemprop=price]')->count()) {
            $price = (float) $crawler->filter('[itemprop=price]')->attr('content');
        } elseif ($crawler->filter('.price')->count()) {
            $raw = $crawler->filter('.price')->text();
            $price = (float) preg_replace('/[^0-9,.]/', '', str_replace(',', '.', $raw));
        }

        if (!$price || $price <= 0) {
            throw new \Exception('Prix manquant');
        }

        /* ---------- IMAGES (OPTIONNEL) ---------- */
        $images = [];

        $crawler->filter('img')->each(function ($img) use (&$images) {
            $src = $img->attr('data-src') ?? $img->attr('src');
            if ($src && str_contains($src, '/images/product')) {
                $images[] = $src;
            }
        });

        return [
            'site'     => 'dafy',
            'category' => $category,
            'name'     => $name,
            'price'    => $price,
            'images'   => array_values(array_unique($images)),
            'link'     => $url,
        ];
    }
}























