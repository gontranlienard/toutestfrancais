<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $structure = [

            'Casques' => [
                'Intégral',
                'Modulable',
                'Jet',
                'Cross / Enduro',
                'Enfant',
                'Replica'
            ],

            'Vêtements Moto' => [
                'Blousons Cuir',
                'Blousons Textile',
                'Vestes Touring',
                'Pantalons',
                'Jeans Moto',
                'Pluie'
            ],

            'Gants' => [
                'Été',
                'Hiver',
                'Mi-saison',
                'Racing',
                'Cross'
            ],

            'Bottes & Chaussures' => [
                'Bottes Racing',
                'Bottes Touring',
                'Bottes Cross',
                'Chaussures Moto'
            ],

            'Combinaisons' => [
                '1 Pièce',
                '2 Pièces'
            ],

            'Protections' => [
                'Dorsales',
                'Coudières',
                'Genouillères',
                'Airbag'
            ],

            'Bagagerie' => [
                'Sacoches',
                'Top Case',
                'Sacs à dos',
                'Sacoches Réservoir'
            ],

            'Accessoires Moto' => [
                'Intercom',
                'Visières',
                'Antivol',
                'Housses'
            ],

            'Pièces & Entretien' => [
                'Batterie',
                'Huile',
                'Filtre',
                'Chaîne',
                'Plaquettes'
            ],

            'Pneus' => [
                'Route',
                'Sport',
                'Trail',
                'Cross'
            ],
        ];

        foreach ($structure as $parentName => $children) {

            $parent = Category::create([
                'name' => $parentName,
                'slug' => Str::slug($parentName),
                'parent_id' => null
            ]);

            foreach ($children as $childName) {
                Category::create([
                    'name' => $childName,
                    'slug' => Str::slug($childName),
                    'parent_id' => $parent->id
                ]);
            }
        }
    }
}

