<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\XlsxImport;

class ImportXlsxCommand extends Command
{
    protected $signature = 'import:xlsx {filename}';
    protected $description = 'Import XLSX file from storage/app/xlsx';

    public function handle()
    {
        $filename = $this->argument('filename');
        $path = storage_path("app/xlsx/{$filename}");

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return;
        }
        $this->info("Processing file: {$path}");
        try {
            Excel::import(new XlsxImport, $path);
            $this->info("XLSX import completed successfully.");
        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
        }
    }
}
