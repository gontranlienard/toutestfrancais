<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Import;
use App\Services\ProductImportService;

class ImportProducts extends Command
{
    protected $signature = 'import:products {filename}';
    protected $description = 'Import products JSON file';

    public function handle(ProductImportService $service)
    {
        $filename = $this->argument('filename');

        $import = Import::create([
            'site_slug' => 'auto',
            'filename' => $filename
        ]);

        $service->handle($import);

        $this->info('Import terminé.');
    }
}
