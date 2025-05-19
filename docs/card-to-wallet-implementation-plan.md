# Card-to-Wallet Implementation Plan

## Overview

This document outlines the implementation plan for repurposing the TechPay Core system into a Card-to-Wallet system for Zambia. The system will allow users to fund mobile money wallets directly from their credit/debit cards.

## Core Components Implemented

### 1. Models

- **User**: Stores user account information with verification levels
- **Transaction**: Records all card-to-wallet transactions
- **TransactionStatus**: Tracks transaction status history
- **Beneficiary**: Stores saved wallet recipients
- **WalletProvider**: Stores supported mobile money providers
- **KycDocument**: Stores user verification documents
- **Charge**: Configures transaction fees
- **TransactionCharge**: Records calculated charges for transactions

### 2. Controllers

#### Authentication Controllers
- **RegisterController**: Handles user registration with custom fields
- **LoginController**: Handles login with email/phone and security features
- **ForgotPasswordController**: Handles password reset requests
- **ResetPasswordController**: Processes password resets
- **VerifyEmailController**: Handles email verification
- **VerificationController**: Handles phone verification

#### Core Functionality Controllers
- **HomeController**: Manages public pages (landing, about, contact)
- **DashboardController**: Displays user dashboard with transaction stats
- **TransactionController**: Handles the complete transaction flow
- **BeneficiaryController**: Manages saved wallet recipients
- **ProfileController**: Handles user profile and KYC management
- **SupportController**: Manages support requests and FAQs

### 3. Routes

- **Public Routes**: Landing page, about, contact, terms, privacy
- **Authentication Routes**: Login, register, password reset, verification
- **Protected Routes**: Dashboard, transactions, beneficiaries, profile, support

## Transaction Flow

1. **Initiation**:
   - User selects mobile money provider
   - User enters wallet number and amount
   - System calculates fees (4% total)

2. **Confirmation**:
   - Display recipient details and total amount
   - Option to save beneficiary

3. **Payment**:
   - Redirect to MPGS hosted checkout
   - Process card payment securely

4. **Completion**:
   - Process wallet funding via provider API
   - Update transaction status
   - Display success/failure message

## Security Features

- Email verification required
- Phone verification required
- KYC document submission for higher limits
- Account locking after failed login attempts
- Transaction limits based on verification level
- No card storage on platform

## Corporate Module

### 1. Corporate Models
- **Company**: Stores company information and verification status
- **CorporateWallet**: Manages corporate wallet balance and transactions
- **CorporateRole**: Defines roles for corporate users (admin, approver, initiator)
- **CorporateUserRole**: Links users to roles within a company
- **BulkDisbursement**: Manages bulk payment operations
- **DisbursementItem**: Stores individual transactions within a bulk disbursement
- **ApprovalWorkflow**: Configures approval requirements for different actions
- **ApprovalRequest**: Tracks approval processes for various entities
- **ApprovalAction**: Records individual approval or rejection actions
- **CompanyDocument**: Stores company verification documents
- **CorporateRateTier**: Defines fee tiers based on transaction volume
- **CompanyRateAssignment**: Assigns rate tiers to companies
- **CorporateWalletTransaction**: Records all corporate wallet transactions

### 2. Corporate Controllers
- **CorporateController**: Manages corporate dashboard and metrics
- **CorporateWalletController**: Handles wallet operations and deposits
- **BulkDisbursementController**: Processes bulk payments to mobile wallets
- **ApprovalController**: Manages approval workflows and actions
- **CorporateUserController**: Handles user management within companies
- **CorporateSettingsController**: Manages company settings and configurations
- **CorporateReportController**: Generates reports for corporate activities

### 3. Corporate Features
- **Corporate Registration**: Business registration with company details
- **Role-Based Access Control**: Different permissions for corporate users
- **Wallet Management**: Centralized wallet for corporate transactions
- **Bulk Disbursements**: Process multiple payments in a single operation
- **Approval Workflows**: Multi-level approvals for sensitive operations
- **Corporate Reporting**: Detailed reports for corporate activities
- **User Management**: Invite and manage users with different roles
- **Rate Tiers**: Volume-based pricing for corporate clients

### 4. Corporate Commands
- **ProcessBulkDisbursements**: Processes approved bulk disbursements by:
  - Finding disbursements with "approved" status
  - Checking wallet balance for each item
  - Creating transaction records for each disbursement item
  - Deducting funds from corporate wallet
  - Posting funds to mobile wallets
  - Handling failures and refunds
  - Updating statuses of disbursements and items

### 5. Admin Interface
- **BulkDisbursementResource**: Filament admin panel for managing bulk disbursements
  - View all disbursements with filtering and sorting
  - Process disbursements manually
  - View disbursement details and status
  - Track processing status and results

- **CompanyResource**: Filament admin panel for managing corporate companies
  - View and manage company information
  - Edit company details and status
  - Track company creation and updates

- **CorporateWalletResource**: Filament admin panel for managing corporate wallets
  - View wallet balances and transactions
  - Deposit and withdraw funds manually
  - Track wallet activity
  - Manage wallet status

## Bulk Disbursement Flow

1. **File Upload**:
   - Corporate user uploads CSV file with wallet numbers, amounts, and recipient names
   - System validates the file format and data

2. **Validation**:
   - System checks each row for valid wallet numbers and amounts
   - Displays validation results with errors and valid items

3. **Review**:
   - User reviews the validated data
   - System shows breakdown by provider and total amounts

4. **Submission**:
   - User submits the disbursement for approval
   - System creates disbursement items and approval request

5. **Approval**:
   - Approver reviews and approves the disbursement
   - System marks the disbursement as approved and ready for processing

6. **Processing**:
   - `ProcessBulkDisbursements` command picks up approved disbursements
   - For each item:
     - Checks wallet balance
     - Creates a transaction record
     - Deducts funds from corporate wallet
     - Posts funds to recipient's mobile wallet
     - Updates statuses based on success/failure
   - Updates the overall disbursement status

7. **Monitoring**:
   - Admin can monitor disbursement status through Filament admin panel
   - Corporate users can view disbursement status and results

## Next Steps

1. **Frontend Implementation**:
   - Implement view templates for all pages
   - Add form validation
   - Implement responsive design

2. **Integration Testing**:
   - Test MPGS integration
   - Test mobile money provider APIs
   - Test transaction flow end-to-end
   - Test corporate bulk disbursement process
   - Test approval workflows

3. **Deployment**:
   - Set up production environment
   - Configure SSL certificates
   - Set up monitoring and logging

4. **Launch**:
   - Soft launch with limited users
   - Monitor for issues
   - Full public launch
