# TechPay Core Platform

TechPay Core is a comprehensive payment processing platform specifically designed for the Zambian market, focusing on mobile money transactions and card payments. Built on Laravel, the platform provides robust transaction processing, monitoring, and merchant management capabilities.

## Key Features

### Payment Processing
- Mobile money transaction processing
- Card payments through MPGS and Cybersource
- Future integration with Absa Cybersource
- Multi-channel support (USSD, WhatsApp, Web, POS, Kiosks)

### Transaction Monitoring
- Real-time transaction tracking
- Natural transaction monitoring system
- Comprehensive reporting and analytics
- Reconciliation tools

### Merchant Management
- Hierarchical structure: Groups → Companies → Merchants
- Merchant onboarding and KYC/documentation tracking
- Merchant portal for cash outs and account management
- Payment link generation (email and WhatsApp)

### Core Platform Features
- Double-entry ledger system
- Hosted checkout
- Auditing and logging
- API layer for transaction processing
- Fee and charge maintenance

## Future Development Plans
- Integration of Absa Cybersource payment processor
- Enhanced natural transaction monitoring capabilities
- Advanced merchant analytics and reporting
- Expanded mobile money integrations

## Platform Structure

The platform follows a hierarchical structure to manage organizations and merchants:

```
Group (Conglomerate)
    |
    Company
        |
        Merchant
            |
            Merchant IDs
```

Each group can contain multiple companies, and each company can have multiple merchants with associated merchant IDs.

## Setup Instructions

1. Clone the repository:
