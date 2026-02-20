<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Category;

class FixCategorySlugs extends Command
{
    protected $signature = 'fix:category-slugs';
    protected $description = 'Génère les slugs manquants pour les catégories';

    public function handle()
    {
        $this->info('🔧 Génération des slugs catégories...');

        $categories = Category::whereNull('slug')
            ->orWhere('slug', '')
            ->get();

        foreach ($categories as $category) {
            $category->slug = Str::slug($category->name);
            $category->save();

            $this->line("✅ {$category->name} → {$category->slug}");
        }

        $this->info('🎉 Slugs générés avec succès');
    }
}
