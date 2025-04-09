-- Companies Table
CREATE TABLE companies (
    id SERIAL PRIMARY KEY,
    uuid UUID DEFAULT gen_random_uuid() NOT NULL,
    name VARCHAR(255) NOT NULL,
    registration_number VARCHAR(100) NOT NULL,
    tax_id VARCHAR(100),
    industry VARCHAR(100),
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) DEFAULT 'Zambia',
    postal_code VARCHAR(20),
    phone_number VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    website VARCHAR(255),
    logo_path VARCHAR(255),
    verification_status VARCHAR(20) DEFAULT 'pending' CHECK (verification_status IN ('pending', 'approved', 'rejected')),
    status VARCHAR(20) DEFAULT 'active' CHECK (status IN ('active', 'suspended', 'inactive')),
    notes TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Extend Users Table (Alter existing table)
ALTER TABLE users ADD COLUMN user_type VARCHAR(20) DEFAULT 'individual' CHECK (user_type IN ('individual', 'corporate'));
ALTER TABLE users ADD COLUMN company_id INTEGER REFERENCES companies(id) ON DELETE SET NULL;

-- Corporate User Roles
CREATE TABLE corporate_roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Insert default corporate roles
INSERT INTO corporate_roles (name, description) VALUES 
('admin', 'Full control of corporate account, users, and transactions'),
('approver', 'Can approve transactions and user management actions'),
('initiator', 'Can initiate transactions but requires approval');

-- Link Users to Corporate Roles
CREATE TABLE corporate_user_roles (
    id SERIAL PRIMARY KEY,
    company_id INTEGER NOT NULL REFERENCES companies(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    role_id INTEGER NOT NULL REFERENCES corporate_roles(id),
    is_primary BOOLEAN DEFAULT FALSE,
    assigned_by INTEGER REFERENCES users(id),
    assigned_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(company_id, user_id, role_id)
);

-- Company Documents
CREATE TABLE company_documents (
    id SERIAL PRIMARY KEY,
    company_id INTEGER NOT NULL REFERENCES companies(id) ON DELETE CASCADE,
    document_type VARCHAR(50) NOT NULL CHECK (document_type IN ('certificate_of_incorporation', 'tax_clearance', 'business_license', 'company_profile', 'director_id', 'other')),
    document_number VARCHAR(100),
    file_path VARCHAR(255) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected')),
    review_notes TEXT,
    reviewed_by INTEGER REFERENCES users(id),
    reviewed_at TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Corporate Wallets
CREATE TABLE corporate_wallets (
    id SERIAL PRIMARY KEY,
    company_id INTEGER NOT NULL REFERENCES companies(id) ON DELETE CASCADE,
    balance DECIMAL(15, 2) NOT NULL DEFAULT 0,
    currency VARCHAR(3) DEFAULT 'ZMW',
    status VARCHAR(20) DEFAULT 'active' CHECK (status IN ('active', 'suspended', 'inactive')),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(company_id, currency)
);

-- Corporate Wallet Transactions
CREATE TABLE corporate_wallet_transactions (
    id SERIAL PRIMARY KEY,
    uuid UUID DEFAULT gen_random_uuid() NOT NULL,
    corporate_wallet_id INTEGER NOT NULL REFERENCES corporate_wallets(id) ON DELETE CASCADE,
    transaction_type VARCHAR(50) NOT NULL CHECK (transaction_type IN ('deposit', 'withdrawal', 'transfer', 'fee', 'adjustment')),
    amount DECIMAL(15, 2) NOT NULL,
    balance_after DECIMAL(15, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'ZMW',
    description TEXT,
    reference_number VARCHAR(100),
    performed_by INTEGER REFERENCES users(id),
    status VARCHAR(20) DEFAULT 'completed' CHECK (status IN ('pending', 'completed', 'failed', 'reversed')),
    related_entity_type VARCHAR(50),
    related_entity_id INTEGER,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Corporate Rate Tiers
CREATE TABLE corporate_rate_tiers (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    monthly_volume_minimum DECIMAL(15, 2) NOT NULL,
    fee_percentage DECIMAL(5, 2) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Insert default rate tiers
INSERT INTO corporate_rate_tiers (name, monthly_volume_minimum, fee_percentage, description) VALUES 
('Standard', 0.00, 3.50, 'Default rate for corporate accounts'),
('Silver', 100000.00, 3.00, 'Reduced rate for medium volume'),
('Gold', 500000.00, 2.50, 'Preferred rate for high volume'),
('Platinum', 1000000.00, 2.00, 'Premium rate for very high volume');

-- Company Rate Assignments
CREATE TABLE company_rate_assignments (
    id SERIAL PRIMARY KEY,
    company_id INTEGER NOT NULL REFERENCES companies(id) ON DELETE CASCADE,
    rate_tier_id INTEGER NOT NULL REFERENCES corporate_rate_tiers(id),
    override_fee_percentage DECIMAL(5, 2),
    assigned_by INTEGER REFERENCES users(id),
    effective_from TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    effective_to TIMESTAMP WITH TIME ZONE,
    notes TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Bulk Disbursements
CREATE TABLE bulk_disbursements (
    id SERIAL PRIMARY KEY,
    uuid UUID DEFAULT gen_random_uuid() NOT NULL,
    company_id INTEGER NOT NULL REFERENCES companies(id),
    corporate_wallet_id INTEGER NOT NULL REFERENCES corporate_wallets(id),
    name VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(255),
    total_amount DECIMAL(15, 2) NOT NULL,
    total_fee DECIMAL(15, 2) NOT NULL,
    transaction_count INTEGER NOT NULL,
    currency VARCHAR(3) DEFAULT 'ZMW',
    status VARCHAR(20) DEFAULT 'draft' CHECK (status IN ('draft', 'pending_approval', 'approved', 'processing', 'completed', 'partially_completed', 'failed', 'cancelled')),
    initiated_by INTEGER NOT NULL REFERENCES users(id),
    approved_by INTEGER REFERENCES users(id),
    approved_at TIMESTAMP WITH TIME ZONE,
    completed_at TIMESTAMP WITH TIME ZONE,
    reference_number VARCHAR(100) UNIQUE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Disbursement Items
CREATE TABLE disbursement_items (
    id SERIAL PRIMARY KEY,
    bulk_disbursement_id INTEGER NOT NULL REFERENCES bulk_disbursements(id) ON DELETE CASCADE,
    transaction_id INTEGER REFERENCES transactions(id),
    wallet_provider_id INTEGER REFERENCES wallet_providers(id),
    wallet_number VARCHAR(50) NOT NULL,
    recipient_name VARCHAR(255),
    amount DECIMAL(12, 2) NOT NULL,
    fee DECIMAL(12, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'ZMW',
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'processing', 'completed', 'failed')),
    error_message TEXT,
    reference VARCHAR(100) NOT NULL,
    row_number INTEGER, -- Original row in uploaded file
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Approval Workflows
CREATE TABLE approval_workflows (
    id SERIAL PRIMARY KEY,
    company_id INTEGER NOT NULL REFERENCES companies(id) ON DELETE CASCADE,
    entity_type VARCHAR(50) NOT NULL CHECK (entity_type IN ('bulk_disbursement', 'user_role', 'rate_change', 'wallet_withdrawal')),
    min_approvers INTEGER NOT NULL DEFAULT 1,
    amount_threshold DECIMAL(15, 2),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Approval Requests
CREATE TABLE approval_requests (
    id SERIAL PRIMARY KEY,
    uuid UUID DEFAULT gen_random_uuid() NOT NULL,
    company_id INTEGER NOT NULL REFERENCES companies(id) ON DELETE CASCADE,
    entity_type VARCHAR(50) NOT NULL,
    entity_id INTEGER NOT NULL,
    requested_by INTEGER NOT NULL REFERENCES users(id),
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected', 'cancelled')),
    required_approvals INTEGER NOT NULL DEFAULT 1,
    received_approvals INTEGER NOT NULL DEFAULT 0,
    description TEXT,
    expires_at TIMESTAMP WITH TIME ZONE,
    completed_at TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Approval Actions
CREATE TABLE approval_actions (
    id SERIAL PRIMARY KEY,
    approval_request_id INTEGER NOT NULL REFERENCES approval_requests(id) ON DELETE CASCADE,
    approver_id INTEGER NOT NULL REFERENCES users(id),
    action VARCHAR(20) NOT NULL CHECK (action IN ('approved', 'rejected')),
    comments TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Company API Tokens (for potential future API access)
CREATE TABLE company_api_tokens (
    id SERIAL PRIMARY KEY,
    company_id INTEGER NOT NULL REFERENCES companies(id) ON DELETE CASCADE,
    token_name VARCHAR(100) NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    permissions JSONB NOT NULL DEFAULT '{}',
    created_by INTEGER NOT NULL REFERENCES users(id),
    last_used_at TIMESTAMP WITH TIME ZONE,
    expires_at TIMESTAMP WITH TIME ZONE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for performance
CREATE INDEX idx_users_company_id ON users(company_id);
CREATE INDEX idx_users_user_type ON users(user_type);
CREATE INDEX idx_corporate_user_roles_company_id ON corporate_user_roles(company_id);
CREATE INDEX idx_corporate_user_roles_user_id ON corporate_user_roles(user_id);
CREATE INDEX idx_company_documents_company_id ON company_documents(company_id);
CREATE INDEX idx_company_documents_status ON company_documents(status);
CREATE INDEX idx_corporate_wallets_company_id ON corporate_wallets(company_id);
CREATE INDEX idx_corporate_wallet_transactions_wallet_id ON corporate_wallet_transactions(corporate_wallet_id);
CREATE INDEX idx_bulk_disbursements_company_id ON bulk_disbursements(company_id);
CREATE INDEX idx_bulk_disbursements_status ON bulk_disbursements(status);
CREATE INDEX idx_disbursement_items_disbursement_id ON disbursement_items(bulk_disbursement_id);
CREATE INDEX idx_disbursement_items_status ON disbursement_items(status);
CREATE INDEX idx_approval_requests_company_id ON approval_requests(company_id);
CREATE INDEX idx_approval_requests_entity ON approval_requests(entity_type, entity_id);
CREATE INDEX idx_approval_requests_status ON approval_requests(status);
