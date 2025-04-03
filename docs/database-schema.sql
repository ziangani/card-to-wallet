# Database Schema for Card-to-Wallet System

## Overview

This document outlines the database schema for the Card-to-Wallet system. The schema is designed for PostgreSQL and focuses on core functionality without card storage.

## Tables

### 1. users

Stores user account information.

| Column Name | Data Type | Constraints | Description |
|-------------|-----------|------------|-------------|
| id | SERIAL | PRIMARY KEY | Unique identifier |
| uuid | UUID | NOT NULL, DEFAULT gen_random_uuid() | Public identifier |
| name | VARCHAR(100) | NOT NULL | User's combined name |
| first_name | VARCHAR(100) | NOT NULL | User's first name |
| last_name | VARCHAR(100) | NOT NULL | User's last name |
| email | VARCHAR(255) | UNIQUE, NOT NULL | User's email address |
| phone_number | VARCHAR(20) | UNIQUE, NOT NULL | User's phone number |
| password_hash | VARCHAR(255) | NOT NULL | Hashed password |
| date_of_birth | DATE | NOT NULL | User's date of birth |
| address | TEXT | NULL | User's physical address |
| city | VARCHAR(100) | NULL | User's city |
| country | VARCHAR(100) | DEFAULT 'Zambia' | User's country |
| verification_level | VARCHAR(20) | DEFAULT 'basic', CHECK (verification_level IN ('basic', 'verified')) | User verification level |
| is_active | BOOLEAN | DEFAULT TRUE | Account status |
| is_email_verified | BOOLEAN | DEFAULT FALSE | Email verification status |
| is_phone_verified | BOOLEAN | DEFAULT FALSE | Phone verification status |
| login_attempts | INTEGER | DEFAULT 0 | Failed login attempts count |
| last_login_at | TIMESTAMP WITH TIME ZONE | NULL | Last login timestamp |
| created_at | TIMESTAMP WITH TIME ZONE | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |
| updated_at | TIMESTAMP WITH TIME ZONE | DEFAULT CURRENT_TIMESTAMP | Record update timestamp |

### 2. kyc_documents

Stores user verification documents.

| Column Name | Data Type | Constraints | Description |
|-------------|-----------|------------|-------------|
| id | SERIAL | PRIMARY KEY | Unique identifier |
| user_id | INTEGER | REFERENCES users(id) ON DELETE CASCADE | User reference |
| document_type | VARCHAR(50) | NOT NULL, CHECK (document_type IN ('national_id', 'passport', 'drivers_license', 'proof_of_address', 'selfie')) | Type of document |
| document_number | VARCHAR(100) | NULL | ID/document number |
| file_path | VARCHAR(255) | NOT NULL | Path to stored document |
| status | VARCHAR(20) | DEFAULT 'pending', CHECK (status IN ('pending', 'approved', 'rejected')) | Verification status |
| review_notes | TEXT | NULL | Admin review notes |
| reviewed_by | INTEGER | REFERENCES users(id) | Admin who reviewed |
| reviewed_at | TIMESTAMP WITH TIME ZONE | NULL | Review timestamp |
| expiry_date | DATE | NULL | Document expiry date |
| created_at | TIMESTAMP WITH TIME ZONE | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |
| updated_at | TIMESTAMP WITH TIME ZONE | DEFAULT CURRENT_TIMESTAMP | Record update timestamp |

### 3. wallet_providers

Stores supported mobile money providers.

| Column Name | Data Type | Constraints | Description |
|-------------|-----------|------------|-------------|
| id | SERIAL | PRIMARY KEY | Unique identifier |
| name | VARCHAR(100) | NOT NULL | Provider name (e.g., Airtel Money) |
| api_code | VARCHAR(50) | UNIQUE, NOT NULL | Provider API code |
| is_active | BOOLEAN | DEFAULT TRUE | Provider availability |
| created_at | TIMESTAMP WITH TIME ZONE | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |
| updated_at | TIMESTAMP WITH TIME ZONE | DEFAULT CURRENT_TIMESTAMP | Record update timestamp |

### 4. beneficiaries

Stores saved wallet recipients.

| Column Name | Data Type | Constraints | Description |
|-------------|-----------|------------|-------------|
| id | SERIAL | PRIMARY KEY | Unique identifier |
| user_id | INTEGER | REFERENCES users(id) ON DELETE CASCADE | User reference |
| wallet_provider_id | INTEGER | REFERENCES wallet_providers(id) | Provider reference |
| wallet_number | VARCHAR(50) | NOT NULL | Mobile wallet number |
| recipient_name | VARCHAR(100) | NOT NULL | Name of recipient |
| is_favorite | BOOLEAN | DEFAULT FALSE | Favorite status |
| notes | TEXT | NULL | User notes |
| created_at | TIMESTAMP WITH TIME ZONE | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |
| updated_at | TIMESTAMP WITH TIME ZONE | DEFAULT CURRENT_TIMESTAMP | Record update timestamp |
| CONSTRAINT | | UNIQUE(user_id, wallet_provider_id, wallet_number) | Prevent duplicate entries |

### 5. transactions

Stores all transaction records.

| Column Name | Data Type | Constraints | Description |
|-------------|-----------|------------|-------------|
| id | SERIAL | PRIMARY KEY | Unique identifier |
| uuid | UUID | NOT NULL, DEFAULT gen_random_uuid() | Public identifier |
| user_id | INTEGER | REFERENCES users(id) | User reference |
| transaction_type | VARCHAR(20) | DEFAULT 'card_to_wallet' | Type of transaction |
| wallet_provider_id | INTEGER | REFERENCES wallet_providers(id) | Provider reference |
| wallet_number | VARCHAR(50) | NOT NULL | Recipient wallet number |
| recipient_name | VARCHAR(100) | NOT NULL | Name of recipient |
| amount | DECIMAL(12, 2) | NOT NULL | Transaction amount |
| fee_amount | DECIMAL(12, 2) | NOT NULL | Fee charged |
| total_amount | DECIMAL(12, 2) | NOT NULL | Total amount (amount + fee) |
| currency | VARCHAR(3) | DEFAULT 'ZMW' | Currency code |
| status | VARCHAR(20) | DEFAULT 'pending', CHECK (status IN ('pending', 'payment_initiated', 'payment_completed', 'payment_failed', 'funding_initiated', 'completed', 'failed')) | Transaction status |
| mpgs_order_id | VARCHAR(100) | UNIQUE | MPGS order reference |
| mpgs_result_code | VARCHAR(20) | NULL | MPGS result code |
| provider_reference | VARCHAR(100) | NULL | Mobile money provider reference |
| failure_reason | TEXT | NULL | Reason for failure if applicable |
| ip_address | VARCHAR(45) | NULL | User's IP address |
| user_agent | TEXT | NULL | User's browser agent |
| created_at | TIMESTAMP WITH TIME ZONE | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |
| updated_at | TIMESTAMP WITH TIME ZONE | DEFAULT CURRENT_TIMESTAMP | Record update timestamp |

### 6. transaction_statuses

Tracks transaction status history.

| Column Name | Data Type | Constraints | Description |
|-------------|-----------|------------|-------------|
| id | SERIAL | PRIMARY KEY | Unique identifier |
| transaction_id | INTEGER | REFERENCES transactions(id) ON DELETE CASCADE | Transaction reference |
| status | VARCHAR(20) | NOT NULL | Status value |
| notes | TEXT | NULL | Status notes |
| created_at | TIMESTAMP WITH TIME ZONE | DEFAULT CURRENT_TIMESTAMP | Status timestamp |

### 7. transaction_limits

Defines transaction limits by verification level.

| Column Name | Data Type | Constraints | Description |
|-------------|-----------|------------|-------------|
| id | SERIAL | PRIMARY KEY | Unique identifier |
| verification_level | VARCHAR(20) | NOT NULL | User verification level |
| max_amount_per_transaction | DECIMAL(12, 2) | NOT NULL | Maximum single transaction amount |
| daily_max_amount | DECIMAL(12, 2) | NOT NULL | Maximum daily total |
| daily_transaction_count | INTEGER | NOT NULL | Maximum daily transactions |
| monthly_max_amount | DECIMAL(15, 2) | NOT NULL | Maximum monthly total |
| monthly_transaction_count | INTEGER | NOT NULL | Maximum monthly transactions |
| is_active | BOOLEAN | DEFAULT TRUE | Limit enforcement status |
| created_at | TIMESTAMP WITH TIME ZONE | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |
| updated_at | TIMESTAMP WITH TIME ZONE | DEFAULT CURRENT_TIMESTAMP | Record update timestamp |

### 8. system_settings

Stores system configuration settings.

| Column Name | Data Type | Constraints | Description |
|-------------|-----------|------------|-------------|
| id | SERIAL | PRIMARY KEY | Unique identifier |
| setting_key | VARCHAR(100) | UNIQUE, NOT NULL | Setting identifier |
| setting_value | TEXT | NOT NULL | Setting value |
| description | TEXT | NULL | Setting description |
| is_encrypted | BOOLEAN | DEFAULT FALSE | Encryption status |
| created_at | TIMESTAMP WITH TIME ZONE | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |
| updated_at | TIMESTAMP WITH TIME ZONE | DEFAULT CURRENT_TIMESTAMP | Record update timestamp |

### 9. audit_logs

Tracks system activity for auditing.

| Column Name | Data Type | Constraints | Description |
|-------------|-----------|------------|-------------|
| id | SERIAL | PRIMARY KEY | Unique identifier |
| user_id | INTEGER | REFERENCES users(id) | User reference |
| action | VARCHAR(100) | NOT NULL | Action performed |
| entity_type | VARCHAR(50) | NOT NULL | Type of entity affected |
| entity_id | INTEGER | NULL | ID of entity affected |
| old_values | JSONB | NULL | Previous values |
| new_values | JSONB | NULL | New values |
| ip_address | VARCHAR(45) | NULL | User's IP address |
| user_agent | TEXT | NULL | User's browser agent |
| created_at | TIMESTAMP WITH TIME ZONE | DEFAULT CURRENT_TIMESTAMP | Action timestamp |

## Indexes

```sql
-- Create indexes for performance
CREATE INDEX idx_transactions_user_id ON transactions(user_id);
CREATE INDEX idx_transactions_status ON transactions(status);
CREATE INDEX idx_transactions_created_at ON transactions(created_at);
CREATE INDEX idx_kyc_documents_user_id ON kyc_documents(user_id);
CREATE INDEX idx_kyc_documents_status ON kyc_documents(status);
CREATE INDEX idx_beneficiaries_user_id ON beneficiaries(user_id);
CREATE INDEX idx_audit_logs_user_id ON audit_logs(user_id);
CREATE INDEX idx_audit_logs_created_at ON audit_logs(created_at);
```

## Initial Data

```sql
-- Insert wallet providers
INSERT INTO wallet_providers (name, api_code)
VALUES
('Airtel Money', 'airtel'),
('MTN Mobile Money', 'mtn'),
('Zamtel Kwacha', 'zamtel');

-- Insert transaction limits
INSERT INTO transaction_limits
(verification_level, max_amount_per_transaction, daily_max_amount, daily_transaction_count, monthly_max_amount, monthly_transaction_count)
VALUES
('basic', 1000.00, 2000.00, 3, 5000.00, 20),
('verified', 5000.00, 10000.00, 10, 50000.00, 100);

-- Insert system settings
INSERT INTO system_settings (setting_key, setting_value, description)
VALUES
('fee_percentage', '4.00', 'Transaction fee percentage'),
('platform_fee_percentage', '1.00', 'Platform fee percentage (part of total fee)'),
('bank_fee_percentage', '3.00', 'Bank fee percentage (part of total fee)'),
('maintenance_mode', 'false', 'System maintenance mode flag'),
('min_transaction_amount', '10.00', 'Minimum transaction amount');
```
