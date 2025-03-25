<?php

namespace App\Services\SettlementParser;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

abstract class BaseParser
{
    protected string $filePath;
    protected array $headers = [];
    protected string $provider;
    protected array $processedFiles = [];
    protected array $errors = [];

    /**
     * Initialize the parser with a file path
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Process the settlement file
     */
    public function process(): array
    {
        try {
            if (!file_exists($this->filePath)) {
                throw new \Exception("File not found: {$this->filePath}");
            }

            $handle = fopen($this->filePath, 'r');
            if ($handle === false) {
                throw new \Exception("Unable to open file: {$this->filePath}");
            }

            // Read and set headers
            $headers = fgetcsv($handle);
            Log::info("BaseParser: Reading headers (raw)", ['headers' => $headers]);
            
            if ($headers === false) {
                throw new Exception("Failed to read CSV headers");
            }
            
            // Remove BOM if present
            if (isset($headers[0])) {
                $headers[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $headers[0]);
            }
            
            $this->headers = $this->parseHeaders($headers);
            Log::info("BaseParser: Parsed headers", ['parsed_headers' => $this->headers]);
            
            $records = [];
            $row = 2; // Start from row 2 as row 1 is headers

            while (($data = fgetcsv($handle)) !== false) {
                Log::info("BaseParser: Reading row", [
                    'row_number' => $row,
                    'data' => $data
                ]);
                try {
                    // Validate row before parsing
                    $this->validateRow($data);
                    
                    $record = $this->parseRow($data);
                    if ($record) {
                        $records[] = $record;
                    }
                } catch (\Exception $e) {
                    $this->logError($row, $e->getMessage(), $data);
                }
                $row++;
            }

            fclose($handle);
            return $records;

        } catch (\Exception $e) {
            $this->logError(0, $e->getMessage());
            throw $e;
        }
    }

    /**
     * Parse CSV headers
     */
    protected function parseHeaders(array $headers): array
    {
        return array_map(function ($header) {
            return trim($header);
        }, $headers);
    }

    /**
     * Get column value by header name
     */
    protected function getColumnValue(array $data, string $columnName): ?string
    {
        $index = array_search($columnName, $this->headers);
        if ($index === false) {
            return null;
        }
        return isset($data[$index]) ? trim($data[$index]) : null;
    }

    /**
     * Log error with context
     */
    protected function logError(int $row, string $message, array $data = []): void
    {
        $error = [
            'file' => $this->filePath,
            'row' => $row,
            'message' => $message,
            'data' => $data,
        ];
        
        $this->errors[] = $error;
        Log::error('Settlement file parsing error', $error);
    }

    /**
     * Get all errors encountered during processing
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Convert date string to standard format
     */
    protected function parseDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Convert d/m/Y format to Y-m-d
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})/', $date, $matches)) {
                $day = $matches[1];
                $month = $matches[2];
                $year = $matches[3];
                $time = strpos($date, ' ') !== false ? substr($date, strpos($date, ' ')) : '';
                return date('Y-m-d H:i:s', strtotime("$year-$month-$day$time"));
            }
            
            return date('Y-m-d H:i:s', strtotime($date));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Clean amount string and convert to decimal
     */
    protected function parseAmount(?string $amount): ?float
    {
        if (empty($amount)) {
            return null;
        }

        // Remove any currency symbols and commas
        $amount = preg_replace('/[^0-9.-]/', '', $amount);
        
        return is_numeric($amount) ? (float) $amount : null;
    }

    /**
     * Parse a row of data into a settlement record
     */
    abstract protected function parseRow(array $data): array;

    /**
     * Validate row data before parsing
     */
    abstract protected function validateRow(array $data): void;

    /**
     * Move processed file to archive
     */
    protected function archiveFile(): void
    {
        $archivePath = storage_path('app/settlement_files/processed/' . $this->provider . '/' . date('Y/m/d'));
        if (!file_exists($archivePath)) {
            mkdir($archivePath, 0777, true);
        }

        $fileName = basename($this->filePath);
        $newPath = $archivePath . '/' . $fileName;

        // Add timestamp if file already exists
        if (file_exists($newPath)) {
            $pathInfo = pathinfo($newPath);
            $newPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_' . time() . '.' . $pathInfo['extension'];
        }

        rename($this->filePath, $newPath);
        $this->processedFiles[] = $newPath;
    }

    /**
     * Get list of processed files
     */
    public function getProcessedFiles(): array
    {
        return $this->processedFiles;
    }
}
