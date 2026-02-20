<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Category;

class ImportDafyCategories extends Command
{
    protected $signature = 'import:dafy-categories';
    protected $description = 'Import des catégories Dafy depuis le méga menu';

    public function handle()
    {
        $path = base_path('scraper/output/dafy-mega-menu.json');

        if (!file_exists($path)) {
            $this->error('❌ Fichier dafy-mega-menu.json introuvable');
            return;
        }

        $categories = json_decode(file_get_contents($path), true);

        if (!$categories || !count($categories)) {
            $this->error('❌ Aucune catégorie à importer');
            return;
        }

        DB::table('categories')->where('site', 'dafy')->delete();

        $this->info('🧹 Catégories Dafy existantes supprimées');

        foreach ($categories as $cat) {
            Category::create([
                'site'      => 'dafy',
                'name'      => $cat['name'],
                'slug'      => Str::slug($cat['name']),
                'url'       => $cat['url'],
                'parent_id' => null
            ]);

            $this->line('✅ ' . $cat['name']);
        }

        $this->info('🎉 Import terminé : ' . count($categories) . ' catégories');
    }
}
