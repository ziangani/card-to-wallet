<?php

namespace App\Console\Commands;

use App\Models\SettlementRecord;
use App\Services\SettlementParser\ABSAParser;
use App\Services\SettlementParser\FNBParser;
use App\Services\SettlementParser\UBAParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{DB, Log, Storage};
use Symfony\Component\Console\Command\Command as CommandAlias;

class ProcessSettlementFiles extends Command
{
    protected $signature = 'settlement:import {provider : The provider type (uba|absa|fnb)}';
    protected $description = 'Import settlement files from specified provider';

    private array $parsers = [
        'uba' => UBAParser::class,
        'absa' => ABSAParser::class,
        'fnb' => FNBParser::class,
    ];

    public function handle(): int
    {
        $provider = strtolower($this->argument('provider'));

        if (!array_key_exists($provider, $this->parsers)) {
            $this->error("Invalid provider. Must be one of: " . implode(', ', array_keys($this->parsers)));
            return CommandAlias::FAILURE;
        }

        // Handle case-insensitive directory names
        $baseDir = storage_path("app/settlement_files");
        $dirs = glob($baseDir . '/*', GLOB_ONLYDIR);
        $providerDir = null;
        
        foreach ($dirs as $dir) {
            if (strtolower(basename($dir)) === $provider) {
                $providerDir = $dir;
                break;
            }
        }

        if (!$providerDir) {
            $this->error("Provider directory not found for: {$provider}");
            return CommandAlias::FAILURE;
        }

        $path = $providerDir . "/in";
        if (!file_exists($path)) {
            $this->error("Directory not found: {$path}");
            return CommandAlias::FAILURE;
        }

        $files = glob("{$path}/*.csv");
        if (empty($files)) {
            $this->info("No CSV files found in {$path}");
            return CommandAlias::SUCCESS;
        }

        $this->info("Found " . count($files) . " files to process");
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        $totalProcessed = 0;
        $totalSkipped = 0;
        $totalErrors = 0;

        foreach ($files as $file) {
            try {
                $result = $this->processFile($file, $provider);
                $totalProcessed += $result['processed'];
                $totalSkipped += $result['skipped'];
                $totalErrors += $result['errors'];
            } catch (\Exception $e) {
                $this->error("\nError processing file {$file}: " . $e->getMessage());
                Log::error("Settlement file processing error", [
                    'file' => $file,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $totalErrors++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // Display summary
        $this->info("\nProcessing complete!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Files Processed', count($files)],
                ['Records Processed', $totalProcessed],
                ['Records Skipped', $totalSkipped],
                ['Errors', $totalErrors],
            ]
        );

        return CommandAlias::SUCCESS;
    }

    private function processFile(string $file, string $provider): array
    {
        $parserClass = $this->parsers[$provider];
        $parser = new $parserClass($file);
        
        $processed = 0;
        $skipped = 0;
        $errors = 0;

        $records = $parser->process();
        $processedRecords = [];

        // First pass: validate all records
        foreach ($records as $record) {
            try {
                // Skip empty records (like ABSA payment records)
                if (empty($record)) {
                    $skipped++;
                    continue;
                }

                // Check for duplicates based on provider
                $query = SettlementRecord::where([
                    'provider' => $record['provider'],
                    'merchant_id' => $record['merchant_id'],
                    'transaction_reference' => $record['transaction_reference'],
                ]);

                // For non-FNB providers, also check arn_reference
                if ($record['provider'] !== 'FNB') {
                    $query->where('arn_reference', $record['arn_reference'] ?? null);
                }

                $exists = $query->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                $processedRecords[] = $record;
            } catch (\Exception $e) {
                Log::error("Error validating record", [
                    'file' => $file,
                    'record' => $record,
                    'error' => $e->getMessage()
                ]);
                $errors++;
            }
        }

        // Second pass: save valid records in transaction
        if (!empty($processedRecords)) {
            DB::beginTransaction();
            try {
                foreach ($processedRecords as $record) {
                    SettlementRecord::create($record);
                    $processed++;
                }

                // Move file to processed directory
                $processedPath = storage_path("app/settlement_files/{$provider}/processed/" . date('Y/m/d'));
                if (!file_exists($processedPath)) {
                    mkdir($processedPath, 0777, true);
                }

                $filename = basename($file);
                $newPath = $processedPath . '/' . $filename;

                // Add timestamp if file already exists
                if (file_exists($newPath)) {
                    $pathInfo = pathinfo($newPath);
                    $newPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_' . time() . '.' . $pathInfo['extension'];
                }

                rename($file, $newPath);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error saving records", [
                    'file' => $file,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        }

        return [
            'processed' => $processed,
            'skipped' => $skipped,
            'errors' => $errors
        ];
    }
}
