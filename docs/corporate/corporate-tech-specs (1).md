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
   - Cancels approval requests past