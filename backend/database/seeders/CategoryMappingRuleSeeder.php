<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\CategoryMappingRule;

class CategoryMappingRuleSeeder extends Seeder
{
    public function run(): void
    {
        CategoryMappingRule::truncate();

        $rules = [

            // CASQUES
            ['keyword' => 'integral', 'slug' => 'integral', 'priority' => 100],
            ['keyword' => 'modulable', 'slug' => 'modulable', 'priority' => 100],
            ['keyword' => 'jet', 'slug' => 'jet', 'priority' => 100],
            ['keyword' => 'cross', 'slug' => 'cross', 'priority' => 100],
            ['keyword' => 'enduro', 'slug' => 'enduro', 'priority' => 100],

            // BLOUSONS
            ['keyword' => 'blouson cuir', 'slug' => 'blousons-cuir', 'priority' => 90],
            ['keyword' => 'blouson textile', 'slug' => 'blousons-textile', 'priority' => 90],

            // PANTALONS
            ['keyword' => 'pantalon cuir', 'slug' => 'pantalons-cuir', 'priority' => 80],
            ['keyword' => 'pantalon textile', 'slug' => 'pantalons-textile', 'priority' => 80],
            ['keyword' => 'jean moto', 'slug' => 'jeans-moto', 'priority' => 80],

            // PNEUS
            ['keyword' => 'pneu route', 'slug' => 'route', 'priority' => 70],
            ['keyword' => 'pneu sport', 'slug' => 'sport', 'priority' => 70],
            ['keyword' => 'pneu trail', 'slug' => 'trail', 'priority' => 70],

            // BAGAGERIE
            ['keyword' => 'top case', 'slug' => 'top-case', 'priority' => 60],
            ['keyword' => 'sacoche reservoir', 'slug' => 'sacoche-reservoir', 'priority' => 60],
            ['keyword' => 'sacoche selle', 'slug' => 'sacoche-selle', 'priority' => 60],

            // ACCESSOIRES
            ['keyword' => 'support telephone', 'slug' => 'supports-telephone', 'priority' => 50],
            ['keyword' => 'intercom', 'slug' => 'intercom', 'priority' => 50],
        ];

        foreach ($rules as $rule) {

            $category = Category::where('slug', $rule['slug'])->first();

            if (!$category) continue;

            CategoryMappingRule::create([
                'site_id' => null,
                'keyword' => $rule['keyword'],
                'category_id' => $category->id,
                'priority' => $rule['priority']
            ]);
        }
    }
}
