<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::create(['name' => 'Casques moto']);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Casque intégral Exemple',
            'price' => 199.99,
            'link' => 'https://www.example.com/casque-exemple'
        ]);
    }
}

