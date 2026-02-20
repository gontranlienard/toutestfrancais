<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // NIVEAU 1
        $equipement = Category::create([
            'name' => 'Equipement moto',
            'slug' => Str::slug('Equipement moto')
        ]);

        $casques = Category::create([
            'name' => 'Casques',
            'slug' => Str::slug('Casques')
        ]);

        $bagagerie = Category::create([
            'name' => 'Bagagerie',
            'slug' => Str::slug('Bagagerie')
        ]);

        // NIVEAU 2
        $equipementMotard = Category::create([
            'name' => 'Equipement motard',
            'slug' => Str::slug('Equipement motard'),
            'parent_id' => $equipement->id
        ]);

        // NIVEAU 3
        $blouson = Category::create([
            'name' => 'Blouson',
            'slug' => Str::slug('Blouson'),
            'parent_id' => $equipementMotard->id
        ]);

        // NIVEAU 4
        $blousonCuir = Category::create([
            'name' => 'Blouson cuir',
            'slug' => Str::slug('Blouson cuir'),
            'parent_id' => $blouson->id
        ]);

        // NIVEAU 5
        $goreTex = Category::create([
            'name' => 'Gore-Tex',
            'slug' => Str::slug('Gore-Tex'),
            'parent_id' => $blousonCuir->id
        ]);

        // NIVEAU 6
        Category::create([
            'name' => 'Hiver',
            'slug' => Str::slug('Hiver'),
            'parent_id' => $goreTex->id
        ]);
    }
}
