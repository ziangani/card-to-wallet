<?php

namespace App\Services\SettlementParser;

use Illuminate\Support\Facades\Log;

class FNBParser extends BaseParser
{
    protected string $provider = 'FNB';

    /**
     * Parse a row of data into a settlement record
     */
    protected function parseRow(array $data): array
    {
        // Debug logging
        Log::info("FNBParser: Processing row", [
            'headers' => $this->headers,
            'data' => $data
        ]);

        // Store original row data
        $rawData = array_combine($this->headers, $data);

        return [
            'provider' => $this->provider,
            'file_name' => basename($this->filePath),
            'batch_id' => $this->getColumnValue($data, 'REMITTANCE_NUMBER'),
            
            // Merchant Information
            'parent_merchant_id' => $this->getColumnValue($data, 'MERCHANT_NUMBER'),
            'outlet_id' => $this->getColumnValue($data, 'OUTLET_NUMBER'),
            'merchant_id' => $this->getColumnValue($data, 'TERMINAL_ID'),
            'merchant_name' => $this->getColumnValue($data, 'MERCHANT_ACRONYM'),
            'terminal_id' => $this->getColumnValue($data, 'TERMINAL_ID'),
            
            // Transaction Details
            'transaction_date' => $this->parseDate($this->getColumnValue($data, 'TRANSACTION_DATE')),
            'settlement_date' => $this->parseDate($this->getColumnValue($data, 'ACQUIRER_SETTLEMENT_DATE')),
            'transaction_type' => $this->getColumnValue($data, 'TRANSACTION_DESCRIPTION'),
            'transaction_reference' => $this->getColumnValue($data, 'MICROFILM_REF_NUMBER'),
            'authorization_code' => $this->getColumnValue($data, 'AUTHORIZATION_CODE'),
            'card_number' => $this->getColumnValue($data, 'CARD_NUMBER'),
            
            // Amount Information
            'original_amount' => $this->parseAmount($this->getColumnValue($data, 'TRANSACTION_AMOUNT')),
            'original_currency' => $this->getColumnValue($data, 'TRANSACTION_CURRENCY'),
            'settlement_amount' => $this->parseAmount($this->getColumnValue($data, 'TRANSACTION_AMOUNT')),
            'settlement_currency' => $this->getColumnValue($data, 'TRANSACTION_CURRENCY'),
            
            // Additional Information
            'remittance_number' => $this->getColumnValue($data, 'REMITTANCE_NUMBER'),
            'merchant_account_number' => $this->getColumnValue($data, 'MERCHANT_ACCOUNT_NUMBER'),
            'merchant_account_bank_code' => $this->getColumnValue($data, 'MERCHANT_ACCOUNT_BANK_CODE'),
            
            // Status
            'status' => 'pending',
            
            // Store raw data
            'raw_data' => json_encode($rawData),
        ];
    }

    /**
     * Validate required fields
     */
    protected function validateRow(array $data): void
    {
        // Debug logging
        Log::info("FNBParser: Validating row", [
            'data' => $data
        ]);

        $required = [
            'MICROFILM_REF_NUMBER',
            'TERMINAL_ID',
            'TRANSACTION_AMOUNT',
            'TRANSACTION_CURRENCY',
            'TRANSACTION_DATE',
        ];

        foreach ($required as $field) {
            if (empty($this->getColumnValue($data, $field))) {
                throw new \Exception("Missing required field: {$field}");
            }
        }

        // Validate amount
        $amount = $this->parseAmount($this->getColumnValue($data, 'TRANSACTION_AMOUNT'));
        if ($amount === null) {
            throw new \Exception("Invalid transaction amount");
        }

        // Validate date
        $date = $this->parseDate($this->getColumnValue($data, 'TRANSACTION_DATE'));
        if ($date === null) {
            throw new \Exception("Invalid transaction date");
        }
    }
}
