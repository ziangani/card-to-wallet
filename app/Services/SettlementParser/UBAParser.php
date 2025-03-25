<?php

namespace App\Services\SettlementParser;

class UBAParser extends BaseParser
{
    protected string $provider = 'UBA';

    /**
     * Parse a row of data into a settlement record
     */
    protected function parseRow(array $data): array
    {
        // Store original row data
        $rawData = array_combine($this->headers, $data);

        return [
            'provider' => $this->provider,
            'file_name' => basename($this->filePath),
            'batch_id' => $this->getColumnValue($data, 'BATCHID'),
            
            // Merchant Information
            'merchant_id' => $this->getColumnValue($data, 'CARDACCEPTORID'),
            'merchant_name' => $this->getColumnValue($data, 'MERCHANTTITLE'),
            'merchant_location' => $this->formatLocation($data),
            'terminal_id' => $this->getColumnValue($data, 'TERMNAME'),
            
            // Transaction Details
            'transaction_date' => $this->parseDate($this->getColumnValue($data, 'ORIGTIME')),
            'processing_date' => $this->parseDate($this->getColumnValue($data, 'ORIGCLEARDATE')),
            'transaction_type' => $this->formatTransactionType($data),
            'transaction_reference' => $this->getColumnValue($data, 'RRN'),
            'authorization_code' => $this->getColumnValue($data, 'APPROVALCODE'),
            'card_number' => $this->getColumnValue($data, 'MASKEDPAN'),
            
            // Amount Information
            'original_amount' => $this->parseAmount($this->getColumnValue($data, 'ORIGINALAMT')),
            'original_currency' => $this->getColumnValue($data, 'ORIGINALCCY'),
            'settlement_amount' => $this->parseAmount($this->getColumnValue($data, 'DESTCLEARAMT')),
            'settlement_currency' => $this->getColumnValue($data, 'DESTCLEARCCY'),
            
            // Additional Information
            'transaction_source' => $this->getColumnValue($data, 'TRANTYPE_DESC'),
            'arn_reference' => $this->getColumnValue($data, 'EXPARN'),
            
            // Status
            'status' => 'pending',
            
            // Store raw data
            'raw_data' => json_encode($rawData),
        ];
    }

    /**
     * Format location from multiple fields
     */
    protected function formatLocation(array $data): string
    {
        $location = [];
        
        if ($city = $this->getColumnValue($data, 'MERCHANTCITY')) {
            $location[] = $city;
        }
        
        if ($region = $this->getColumnValue($data, 'MERCHANTREGION')) {
            $location[] = $region;
        }
        
        if ($zip = $this->getColumnValue($data, 'MERCHANTZIP')) {
            $location[] = $zip;
        }

        return implode(', ', array_filter($location));
    }

    /**
     * Format transaction type from TRANTYPE and TRANTYPE_DESC
     */
    protected function formatTransactionType(array $data): string
    {
        $type = $this->getColumnValue($data, 'TRANTYPE_DESC');
        $code = $this->getColumnValue($data, 'TRANTYPE');
        
        return $type ? $type : ($code ? "Type {$code}" : 'Unknown');
    }

    /**
     * Validate required fields
     */
    protected function validateRow(array $data): void
    {
        $required = [
            'RRN',
            'CARDACCEPTORID',
            'ORIGINALAMT',
            'ORIGINALCCY',
            'ORIGTIME',
            'BATCHID'
        ];

        foreach ($required as $field) {
            if (empty($this->getColumnValue($data, $field))) {
                throw new \Exception("Missing required field: {$field}");
            }
        }

        // Validate amount
        $amount = $this->parseAmount($this->getColumnValue($data, 'ORIGINALAMT'));
        if ($amount === null) {
            throw new \Exception("Invalid transaction amount");
        }

        // Validate date
        $date = $this->parseDate($this->getColumnValue($data, 'ORIGTIME'));
        if ($date === null) {
            throw new \Exception("Invalid transaction date");
        }
    }
}
