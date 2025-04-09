# Corporate Module Technical Specifications

## Overview

This document outlines the technical specifications for implementing the corporate module in the card-to-wallet system. This module will enable businesses to perform bulk disbursements to mobile wallets with preferential rates and multi-user access controls.

## Architecture

### Core Components

1. **Corporate User Management**
   - Extended user registration and authentication
   - Role-based access control
   - Company profile management

2. **Corporate Wallet System**
   - Central balance management
   - Transaction history and reconciliation
   - Deposit and withdrawal workflows

3. **Bulk Disbursement Engine**
   - File processing and validation
   - Transaction batching and execution
   - Reporting and status tracking

4. **Approval Workflow Engine**
   - Configurable approval rules
   - Notification system
   - Audit trail

5. **Preferential Rate System**
   - Volume-based tier management
   - Company-specific rate assignments
   - Rate calculation engine

## Technical Implementation

### Routes Structure

```
/corporate
  /dashboard          # Corporate dashboard
  /register           # Corporate registration
  /users              # User management
    /{user}/edit      # Edit user
    /invite           # Invite new user
  /wallet             # Corporate wallet
    /transactions     # Wallet transactions
    /deposit          # Deposit funds
  /disbursements      # Bulk disbursements
    /create           # Create new disbursement
    /{id}             # View disbursement details
    /{id}/items       # View disbursement items
  /approvals          # Approval workflows
    /pending          # Pending approvals
    /{id}             # Approval details
  /settings           # Corporate settings
    /profile          # Company profile
    /roles            # Role management
    /approvals        # Approval configuration
```

### Controllers

1. **CorporateController**
   - Dashboard display and metrics
   - Corporate-specific views

2. **CorporateAuthController**
   - Registration
   - User invitation
   - Role assignment

3. **CorporateUserController**
   - User management within company
   - Permission assignments
   - User status management

4. **CorporateWalletController**
   - Balance management
   - Transaction history
   - Deposit instructions

5. **BulkDisbursementController**
   - File upload and validation
   - Disbursement creation and management
   - Status tracking and reporting

6. **ApprovalController**
   - Approval queue management
   - Request processing
   - Notification handling

7. **CorporateSettingsController**
   - Company profile management
   - Approval workflow configuration
   - Rate tier display

### Models

1. **Company**
   - Relationships:
     * Users (hasMany)
     * CorporateWallet (hasOne)
     * Documents (hasMany)
     * BulkDisbursements (hasMany)
     * RateAssignment (hasOne)

2. **CorporateWallet**
   - Relationships:
     * Company (belongsTo)
     * Transactions (hasMany)
     * BulkDisbursements (hasMany)

3. **CorporateRole**
   - Relationships:
     * UserRoles (hasMany)

4. **CorporateUserRole**
   - Relationships:
     * User (belongsTo)
     * Company (belongsTo)
     * Role (belongsTo)

5. **BulkDisbursement**
   - Relationships:
     * Company (belongsTo)
     * CorporateWallet (belongsTo)
     * DisbursementItems (hasMany)
     * Initiator (belongsTo User)
     * Approver (belongsTo User)
     * ApprovalRequest (hasOne)

6. **DisbursementItem**
   - Relationships:
     * BulkDisbursement (belongsTo)
     * Transaction (belongsTo)
     * WalletProvider (belongsTo)

7. **ApprovalWorkflow**
   - Relationships:
     * Company (belongsTo)
     * ApprovalRequests (hasMany)

8. **ApprovalRequest**
   - Relationships:
     * Company (belongsTo)
     * Requester (belongsTo User)
     * ApprovalActions (hasMany)
     * MorphTo relation to approvable entity

9. **ApprovalAction**
   - Relationships:
     * ApprovalRequest (belongsTo)
     * Approver (belongsTo User)

10. **CorporateRateTier**
    - Relationships:
      * CompanyRateAssignments (hasMany)

11. **CompanyRateAssignment**
    - Relationships:
      * Company (belongsTo)
      * RateTier (belongsTo)
      * Assigner (belongsTo User)

### Services

1. **CorporateRegistrationService**
   - Process company registration
   - Create initial user with admin role
   - Generate default approval workflows

2. **BulkFileProcessingService**
   - Validate file format and contents
   - Parse recipient and amount data
   - Generate validation reports

3. **DisbursementExecutionService**
   - Process approved disbursements
   - Manage transaction batching
   - Handle partial successes and failures

4. **ApprovalWorkflowService**
   - Determine approval requirements
   - Process approval actions
   - Trigger post-approval actions

5. **CorporateRateCalculationService**
   - Determine applicable rate tier
   - Calculate fees for transactions
   - Manage rate transitions

6. **CorporateNotificationService**
   - Send approval requests
   - Notify transaction results
   - Send wallet balance alerts

7. **CorporateReportingService**
   - Generate disbursement reports
   - Track volume for rate tiers
   - Provide transaction summaries

### Middleware

1. **CorporateAccess**
   - Verify user belongs to a corporate account
   - Redirect to appropriate views

2. **CorporateRoleCheck**
   - Verify user has required role for action
   - Handle unauthorized access attempts

3. **ApprovalRequired**
   - Intercept actions requiring approval
   - Generate approval requests

### Events

1. **CompanyRegistered**
   - Triggered when new company is registered
   - Handlers: Setup default settings, notify admins

2. **CorporateUserInvited**
   - Triggered when user is invited to company
   - Handlers: Send invitation email

3. **BulkDisbursementCreated**
   - Triggered when new disbursement is created
   - Handlers: Create approval request, notify approvers

4. **BulkDisbursementApproved**
   - Triggered when disbursement is approved
   - Handlers: Queue disbursement processing

5. **DisbursementCompleted**
   - Triggered when disbursement is fully processed
   - Handlers: Send reports, update wallet balance

6. **ApprovalRequested**
   - Triggered when any entity needs approval
   - Handlers: Notify approvers, set expiration

7. **ApprovalCompleted**
   - Triggered when approval process finishes
   - Handlers: Execute approved action, notify requester

### Jobs

1. **ProcessBulkDisbursementJob**
   - Handles batch processing of disbursements
   - Implements retry logic for failures

2. **UpdateCorporateRateTierJob**
   - Periodic job to evaluate and update rate tiers
   - Runs monthly to calculate transaction volume

3. **ExpireApprovalRequestsJob**
   - Cancels approval requests past expiration date
   - Notifies requesters of expired requests

4. **SyncCorporateWalletBalanceJob**
   - Recalculates wallet balance from transaction history
   - Flags discrepancies for review

5. **SendDisbursementReportJob**
   - Generates and emails disbursement completion reports
   - Includes success/failure statistics

### File Processing

1. **Supported File Formats**
   - CSV with defined column structure
   - Excel (XLSX) with predefined template

2. **Upload Process**
   - Chunked file upload for large files
   - Server-side validation before processing
   - Virus scanning

3. **Validation Rules**
   - Required columns: mobile_number, amount, recipient_name (optional)
   - Phone number format validation by provider
   - Amount validation (min/max per transaction)
   - Duplicate entry detection

4. **Error Handling**
   - Line-by-line validation errors
   - Exportable error report
   - Option to proceed with valid entries only

### Approval Workflow Logic

1. **Approval Triggers**
   - New bulk disbursement above threshold
   - New user invitation
   - Role changes
   - Rate tier changes
   - Wallet withdrawals

2. **Approval Rules**
   - Configurable minimum approvers
   - Amount-based escalation
   - Role-based authorization
   - Prevention of self-approval

3. **Approval States**
   - Pending: Awaiting required approvals
   - Approved: Met all requirements
   - Rejected: Any approver rejected
   - Cancelled: Requester cancelled or expired

4. **Post-Approval Actions**
   - Automatic execution of approved actions
   - Notification to all stakeholders
   - Audit log entries

### Rate Calculation System

1. **Rate Tier Determination**
   - Based on previous month's transaction volume
   - Default to standard tier for new companies
   - Manual override capability for special arrangements

2. **Fee Calculation Logic**
   - Apply company-specific rate to transaction amount
   - Honor minimum fee requirements
   - Apply volume discounts within batch transactions

3. **Rate Tier Transitions**
   - Monthly evaluation of qualification
   - Notification of tier changes
   - Grace period for downgrade protection

### Security Considerations

1. **Role Segregation**
   - Separation of initiation and approval
   - Minimum two users for proper controls
   - System administrator oversight

2. **Transaction Limits**
   - Company-level maximum transaction limits
   - User-level authorization limits
   - Daily and monthly caps

3. **Audit Trail**
   - Comprehensive logging of all actions
   - User, timestamp, IP address tracking
   - Before/after state recording

4. **Data Access Controls**
   - Company data isolation
   - Role-based UI restrictions
   - Field-level access control

### Integration Points

1. **Payment Gateway**
   - Corporate deposit options
   - Reconciliation with wallet balance

2. **Mobile Money Providers**
   - Batch transaction capabilities
   - Enhanced API access for corporate accounts
   - Priority processing queues

3. **Notification Systems**
   - Email notifications for approvals
   - SMS alerts for critical actions
   - In-app notification center

4. **Accounting Systems** (Future)
   - Transaction export in accounting-friendly formats
   - Potential direct API integration with popular accounting software

### Reporting Capabilities

1. **Disbursement Reports**
   - Success/failure rates
   - Processing times
   - Fee summaries
   - Recipient breakdowns

2. **Financial Reports**
   - Wallet statement
   - Fee expenditure
   - Monthly volume tracking
   - Rate tier progress

3. **User Activity Reports**
   - Action logs by user
   - Login history
   - Approval patterns
   - User productivity metrics

4. **Compliance Reports**
   - Transaction pattern analysis
   - Large transaction tracking
   - Regulatory reporting templates

### Performance Considerations

1. **Batch Processing**
   - Queue-based processing for large files
   - Background execution with progress tracking
   - Rate limiting for provider APIs
   - Parallel processing where possible

2. **Database Optimization**
   - Proper indexing for common queries
   - Pagination for large datasets
   - Efficient joins for complex reports
   - Consider partitioning for transaction history

3. **Caching Strategy**
   - Cache rate calculations
   - Cache company settings and permissions
   - Cache frequently accessed reports
   - Implement cache invalidation on data changes

### Implementation Phases

#### Phase 1: Core Corporate Functionality
- Corporate registration and profile management
- Basic user role management
- Corporate wallet with manual deposits
- Simple bulk disbursement with file upload

#### Phase 2: Advanced Workflows
- Complete approval workflow system
- Enhanced reporting capabilities
- Rate tier implementation
- Improved validation and error handling

#### Phase 3: Integration & Optimization
- API access for corporate clients
- Enhanced accounting integration
- Performance optimizations for large volumes
- Advanced fraud detection for corporate accounts

### Testing Requirements

1. **Unit Testing**
   - Service method testing
   - Model relationship verification
   - Calculation accuracy validation

2. **Integration Testing**
   - File upload and processing
   - Approval workflow execution
   - Wallet balance management
   - End-to-end disbursement process

3. **Performance Testing**
   - Large file handling (1000+ recipients)
   - Concurrent approval processing
   - Transaction throughput capacity
   - System behavior under load

4. **Security Testing**
   - Role-based access control validation
   - Data isolation between companies
   - Input validation and sanitization
   - Approval bypass attempts