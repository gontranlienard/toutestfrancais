<?php

namespace App\Scrapers;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

abstract class BaseScraper
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 20,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (compatible; ToutEstFrancais/1.0)',
            ],
        ]);
    }

    protected function getHtml(string $url): string
    {
        return $this->client->get($url)->getBody()->getContents();
    }

    protected function crawler(string $html): Crawler
    {
        return new Crawler($html);
    }
}
