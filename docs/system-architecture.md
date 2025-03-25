# TechPay Core System Architecture

## System Overview

TechPay Core is a payment processing system that handles various payment channels, merchant integrations, and transaction processing. The system is built with Laravel and follows a modular architecture.

```mermaid
graph TD
    A[Payment Channels] --> B[TechPay Core]
    B --> C[Transaction Processing]
    B --> D[Settlement Processing]
    B --> E[Charging System]
    B --> G[Merchant Financial Tracking]
    B --> F[Reporting]
    
    subgraph "Payment Channels"
    A1[Mobile Money] --> A
    A2[Cards] --> A
    end
    
    subgraph "Transaction Processing"
    C --> C1[Payment Requests]
    C --> C2[Transaction Status]
    C --> C3[Callbacks]
    end
    
    subgraph "Settlement Processing"
    D --> D1[Settlement Files]
    D --> D2[Reconciliation]
    end
    
    subgraph "Charging System"
    E --> E1[Channel Charges]
    E --> E2[Company Charges]
    E --> E3[Merchant Charges]
    end
    
    subgraph "Merchant Financial Tracking"
    G --> G1[Rolling Reserves]
    G --> G2[Merchant Payouts]
    G --> G3[Card Scheme Fines]
    G --> G4[Profitability Analysis]
    end
    
    subgraph "Reporting"
    F --> F1[Transaction Reports]
    F --> F2[Settlement Reports]
    F --> F3[Charge Reports]
    F --> F4[Financial Reports]
    end
```

## Core Components

### 1. Payment Processing
- Handles payment requests through various channels (Mobile Money, Cards)
- Manages transaction lifecycle and status updates
- Processes callbacks and notifications
- See: `app/Http/Controllers/Frontend/CheckOutController.php`

### 2. Settlement Processing
- Processes settlement files from different providers
- Handles reconciliation
- Manages settlement status and records
- See: `app/Models/SettlementRecord.php`

### 3. Charging System
- Configures and calculates transaction charges
- Supports hierarchical charge rules
- Processes charges for transactions and settlements
- See: [Charging System Architecture](charging-system-architecture.md)

### 4. Merchant Financial Tracking
- Tracks the financial relationship with merchants
- Manages rolling reserves and their return to merchants
- Handles payouts, remittance fees, and card scheme fines
- Calculates merchant profitability metrics
- See: [Merchant Financial Tracking](merchant-financial-tracking.md)

### 5. Reporting
- Generates transaction and settlement reports
- Provides charge breakdowns and summaries
- Handles report scheduling and distribution

## Database Architecture

```mermaid
erDiagram
    MERCHANTS ||--o{ PAYMENT_REQUESTS : "processes"
    MERCHANTS ||--o{ TRANSACTIONS : "has"
    MERCHANTS ||--o{ SETTLEMENT_RECORDS : "has"
    MERCHANTS ||--o{ CHARGES : "has"
    MERCHANTS ||--o{ MERCHANT_RECONCILIATIONS : "has"
    MERCHANTS ||--o{ MERCHANT_PAYOUTS : "receives"
    MERCHANTS ||--o{ MERCHANT_FINES : "incurs"
    COMPANY_DETAILS ||--o{ MERCHANTS : "owns"
    COMPANY_DETAILS ||--o{ CHARGES : "has"
    
    PAYMENT_REQUESTS ||--|| TRANSACTIONS : "creates"
    TRANSACTIONS ||--o{ TRANSACTION_CHARGES : "has"
    SETTLEMENT_RECORDS ||--o{ TRANSACTION_CHARGES : "has"
    CHARGES ||--o{ TRANSACTION_CHARGES : "calculates"
    
    MERCHANT_RECONCILIATIONS ||--o{ MERCHANT_PAYOUTS : "generates"
    SETTLEMENT_RECORDS ||--o{ MERCHANT_RECONCILIATIONS : "reconciles"
```

## Integration Points

### 1. Payment Providers
- MPGS (Mastercard Payment Gateway)
- Cybersource
- Airtel Money
- MTN Mobile Money
- See: `app/Integrations/`

### 2. External Systems
- Settlement file processors
- Reporting systems
- Notification services

## Processing Flows

### 1. Payment Flow
```mermaid
sequenceDiagram
    participant M as Merchant
    participant T as TechPay
    participant P as Provider
    
    M->>T: Payment Request
    T->>T: Validate Request
    T->>P: Initiate Payment
    P-->>T: Payment Response
    T-->>M: Payment Status
    P->>T: Payment Callback
    T->>T: Process Charges
    T->>M: Final Status
```

### 2. Settlement Flow
```mermaid
sequenceDiagram
    participant P as Provider
    participant T as TechPay
    participant M as Merchant
    
    P->>T: Settlement File
    T->>T: Process File
    T->>T: Calculate Charges
    T->>T: Reconcile
    T->>M: Settlement Report
```

### 3. Rolling Reserve Return Flow
```mermaid
sequenceDiagram
    participant S as System
    participant R as Reconciliation
    participant P as Payout
    participant M as Merchant
    
    S->>S: Schedule rolling-reserve:generate-returns
    S->>R: Find reconciliations with reserves from 120 days ago
    R->>P: Create pending payouts for each reserve
    P->>P: Calculate remittance fee
    S->>S: Admin reviews pending payouts
    S->>P: Mark payout as completed
    P->>M: Reserve returned to merchant
```

## Security Architecture

1. **Authentication**
   - API authentication using tokens
   - User authentication via Laravel sanctum
   - Role-based access control

2. **Data Protection**
   - Encryption at rest for sensitive data
   - Secure communication channels
   - PCI compliance measures

## Monitoring and Logging

1. **System Monitoring**
   - Transaction monitoring
   - Performance monitoring
   - Error tracking and alerts

2. **Logging**
   - Transaction logs
   - API request logs
   - Error logs
   - Audit trails

## Development Guidelines

1. **Code Organization**
   - Follow Laravel conventions
   - Use service classes for business logic
   - Implement repository pattern where appropriate

2. **Testing**
   - Unit tests for core functionality
   - Integration tests for APIs
   - End-to-end tests for critical flows

3. **Documentation**
   - API documentation
   - Architecture documentation
   - Code documentation

## Deployment Architecture

```mermaid
graph TD
    A[Load Balancer] --> B1[Web Server 1]
    A --> B2[Web Server 2]
    B1 --> C[Database]
    B2 --> C
    B1 --> D[Redis Cache]
    B2 --> D
    B1 --> E[Queue Worker]
    B2 --> E
```

## Configuration Management

1. **Environment Configuration**
   - Development
   - Staging
   - Production

2. **Feature Flags**
   - Payment channel toggles
   - Provider-specific features
   - Charge processing options

## Future Considerations

1. **Scalability**
   - Horizontal scaling of web servers
   - Database sharding
   - Caching improvements

2. **New Features**
   - Additional payment channels
   - Enhanced reporting
   - Advanced charge rules

3. **Integration Expansion**
   - New payment providers
   - Additional settlement processors
   - External system integrations
