<?php

namespace App\Scrapers;

use App\Models\Category;
use App\Models\Product;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class DafyScraper
{
    public function scrape()
    {
        $client = new Client([
            'timeout' => 10,
            'headers' => [
                'User-Agent' => 'ToutEstFrancaisBot/1.0'
            ]
        ]);

        $category = Category::firstOrCreate([
            'name' => 'Casques moto'
        ]);

        $html = $client->get('https://www.dafy-moto.com/casque-moto.html')
                       ->getBody()
                       ->getContents();

        $crawler = new Crawler($html);

        $crawler->filter('.product-item')->each(function (Crawler $node) use ($category) {

            $name = trim($node->filter('.product-item-name')->text());
            $priceText = $node->filter('.price')->text();

            $price = (float) str_replace(',', '.', preg_replace('/[^\d,]/', '', $priceText));
            $link = $node->filter('a')->attr('href');

            Product::updateOrCreate(
                ['link' => $link],
                [
                    'category_id' => $category->id,
                    'name'        => $name,
                    'price'       => $price,
                ]
            );
        });
    }
}
