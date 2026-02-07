<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeMotoSites extends Command
{
    protected $signature = 'scrape:moto';
    protected $description = 'Scraping des sites moto européens';

  use App\Scrapers\DafyScraper;

public function handle()
{
    $this->info('Scraping Dafy Moto...');
    (new DafyScraper())->scrape();
    $this->info('Scraping terminé');
}

}
