# Card-to-Wallet System Overview

## Project Description

This system enables users to fund mobile money wallets in Zambia directly from their credit/debit cards. The platform provides a secure, convenient way to transfer funds from bank cards to mobile wallets without storing sensitive card information.

## Core Features

1. **User Authentication & Management**
   - User registration and login
   - Basic and verified user levels
   - Profile management
   - KYC document submission and verification

2. **Secure Payment Processing**
   - Integration with Mastercard Payment Gateway Services (MPGS) hosted checkout
   - 3DS-secured transactions
   - No card storage on our platform
   - Transaction receipt generation

3. **Mobile Wallet Integration**
   - Support for major Zambian mobile money providers
   - Wallet number validation
   - Beneficiary management (save frequent recipients)
   - Real-time wallet funding

4. **Transaction Management**
   - Transaction history and tracking
   - Detailed receipt generation
   - Status updates and notifications
   - Failed transaction handling

5. **Security & Compliance**
   - KYC verification for higher transaction limits
   - Anti-fraud measures
   - Transaction limits based on verification status
   - Regulatory compliance

## Technology Stack

- **Frontend**: Bootstrap, Tailwind CSS
- **Backend**: Laravel (PHP)
- **Database**: PostgreSQL
- **Deployment**: Laravel Forge
- **Payment Gateway**: Mastercard Payment Gateway Services (MPGS)
- **Mobile Money APIs**: Integration with local providers

## User Tiers & Limits

1. **Basic Users** (Phone & Email Verification Only)
   - Lower transaction limits
   - Limited functionality

2. **Verified Users** (Full KYC)
   - Higher transaction limits
   - Full platform access

## Fee Structure

- Total fee: 4% per transaction
  - 3% bank/processing fee
  - 1% platform commission

## Future Expansions (Phase 2)

- Mobile application
- Corporate bulk disbursement feature
- Additional payment methods
- Enhanced reporting capabilities

## Core Value Proposition

Providing a secure, convenient bridge between traditional banking and mobile money ecosystems, enabling instant funding of mobile wallets from bank cards with transparent fees and user-friendly experience.
