<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Import;
use App\Services\ProductImportService;

class ImportGlobalProducts extends Command
{
    protected $signature = 'import:global';
    protected $description = 'Import all product JSON files';

    protected $files = [
        'dafy-products.json',
        'maxxess-products.json',
		'motoblouz-products.json',
        'moto-axxe-products.json',
    ];

    public function handle()
    {
        if (Import::where('status', 'running')->exists()) {
            $this->error('Un import est déjà en cours.');
            return;
        }

        foreach ($this->files as $filename) {

            $this->info("Import de : {$filename}");

            $import = Import::create([
                'site_slug' => str_replace('-products','', pathinfo($filename, PATHINFO_FILENAME)),
                'filename' => $filename,
                'status' => 'pending',
                'total_products' => 0,
                'processed_products' => 0,
                'success_products' => 0,
                'failed_products' => 0,
                'errors' => null,
            ]);

            try {
                app(ProductImportService::class)->handle($import);
            } catch (\Exception $e) {

                $import->update([
                    'status' => 'failed',
                    'errors' => $e->getMessage(),
                    'finished_at' => now()
                ]);

                $this->error("Erreur sur {$filename}");
            }
        }

        $this->info('Import global terminé.');
    }
}