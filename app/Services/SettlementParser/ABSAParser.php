<?php

namespace App\Services\SettlementParser;

class ABSAParser extends BaseParser
{
    protected string $provider = 'ABSA';

    /**
     * Parse a row of data into a settlement record
     */
    protected function parseRow(array $data): array
    {
        // Store original row data
        $rawData = array_combine($this->headers, $data);

        // Skip payment records as they are summaries
        if ($this->getColumnValue($data, 'TRXN_TYPE') === 'Payment') {
            return [];
        }

        return [
            'provider' => $this->provider,
            'file_name' => basename($this->filePath),
            'batch_id' => $this->extractBatchFromFilename(),
            
            // Merchant Information
            'parent_merchant_id' => $this->getColumnValue($data, 'ACCOUNT_NO'),
            'outlet_id' => $this->getColumnValue($data, 'LOCATION_NO'),
            'merchant_id' => $this->getColumnValue($data, 'LOCATION_NO'),
            'merchant_name' => $this->getColumnValue($data, 'LEGAL_NAME'),
            'legal_name' => $this->getColumnValue($data, 'LEGAL_NAME'),
            'terminal_id' => $this->getColumnValue($data, 'TERMINAL_ID'),
            
            // Transaction Details
            'transaction_date' => $this->parseDate($this->getColumnValue($data, 'TXN_DATE')),
            'processing_date' => $this->parseDate($this->getColumnValue($data, 'PROCESSING_DATE')),
            'settlement_date' => $this->parseDate($this->getColumnValue($data, 'PAYMENT_DATE')),
            'transaction_type' => $this->getColumnValue($data, 'TRXN_TYPE'),
            'transaction_reference' => $this->getColumnValue($data, 'RETRIEVAL_REF_NO'),
            'authorization_code' => $this->getColumnValue($data, 'AUTH_ID'),
            'card_number' => $this->getColumnValue($data, 'CARD_NO'),
            'card_scheme' => $this->getColumnValue($data, 'SCHEME'),
            
            // Amount Information
            'original_amount' => $this->parseAmount($this->getColumnValue($data, 'AMOUNT')),
            'original_currency' => $this->getColumnValue($data, 'CURRENCY'),
            'settlement_amount' => $this->parseAmount($this->getColumnValue($data, 'NET_AMOUNT')),
            'settlement_currency' => $this->getColumnValue($data, 'CURRENCY'),
            'commission_amount' => $this->parseAmount($this->getColumnValue($data, 'COMMISSION')),
            'net_amount' => $this->parseAmount($this->getColumnValue($data, 'NET_AMOUNT')),
            
            // Additional Information
            'card_present' => $this->getColumnValue($data, 'CARD_PRESENT') === 'Y',
            'transaction_source' => $this->getColumnValue($data, 'TRXN_SOURCE'),
            'arn_reference' => $this->getColumnValue($data, 'ARN_REFERENCE'),
            
            // Status
            'status' => 'pending',
            
            // Store raw data
            'raw_data' => json_encode($rawData),
        ];
    }

    /**
     * Extract batch ID from filename
     * Example: "Absa Sample Settlement File - 00015926 TECHPAY LIMITED_REPORT_2024-12-10.csv"
     */
    protected function extractBatchFromFilename(): string
    {
        $filename = basename($this->filePath);
        if (preg_match('/(\d{8})/', $filename, $matches)) {
            return $matches[1];
        }
        return pathinfo($filename, PATHINFO_FILENAME);
    }

    /**
     * Validate required fields
     */
    protected function validateRow(array $data): void
    {
        // Skip validation for payment records
        if ($this->getColumnValue($data, 'TRXN_TYPE') === 'Payment') {
            return;
        }

        $required = [
            'LOCATION_NO',
            'RETRIEVAL_REF_NO',
            'AMOUNT',
            'CURRENCY',
            'TXN_DATE',
            'TRXN_TYPE'
        ];

        foreach ($required as $field) {
            if (empty($this->getColumnValue($data, $field))) {
                throw new \Exception("Missing required field: {$field}");
            }
        }

        // Validate amount
        $amount = $this->parseAmount($this->getColumnValue($data, 'AMOUNT'));
        if ($amount === null) {
            throw new \Exception("Invalid transaction amount");
        }

        // Validate date
        $date = $this->parseDate($this->getColumnValue($data, 'TXN_DATE'));
        if ($date === null) {
            throw new \Exception("Invalid transaction date");
        }
    }
}
