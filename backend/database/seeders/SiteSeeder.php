<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;

class SiteSeeder extends Seeder
{
    public function run(): void
    {
        Site::updateOrCreate(
            ['slug' => 'dafy'],
            [
                'name' => 'Dafy Moto',
                'base_url' => 'https://www.dafy-moto.com'
            ]
        );
    }
}