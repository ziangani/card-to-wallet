# Corporate Module User Flows

This document outlines the key user flows within the corporate module, detailing the step-by-step processes for each major feature.

## 1. Corporate Registration Flow

### User Action: Register as a corporate user

1. **Entry Point**
   - User clicks "Register as a Corporate User" option on registration page

2. **Basic Information**
   - User enters email address and creates password
   - User provides personal information (name, phone)
   - User clicks "Next" to proceed

3. **Company Information**
   - User enters company name
   - User enters registration number
   - User enters tax ID (optional)
   - User selects industry/sector
   - User provides company address
   - User enters company phone and email
   - User clicks "Next" to proceed

4. **Document Upload**
   - User uploads Certificate of Incorporation
   - User uploads Tax Clearance (if available)
   - User uploads Business License
   - User uploads Company Profile (optional)
   - User clicks "Next" to proceed

5. **Review & Submit**
   - User reviews all entered information
   - User accepts terms and conditions
   - User clicks "Submit Registration"

6. **Confirmation**
   - System displays confirmation message
   - System sends verification email
   - User is informed about verification timeline

7. **Account Activation**
   - Admin reviews registration details and documents
   - Admin approves or rejects the application
   - System notifies user of approval decision
   - Upon approval, user can access corporate dashboard

## 2. Corporate User Management Flow

### User Action: Add team member to corporate account

1. **Initiate Invitation**
   - Admin navigates to User Management section
   - Admin clicks "Invite User" button

2. **Enter User Details**
   - Admin enters user's email address
   - Admin enters user's name
   - Admin selects role(s) for the user
   - Admin adds optional message
   - Admin clicks "Send Invitation"

3. **Approval Process** (if required)
   - System checks if invitation requires approval
   - If required, creates approval request
   - Notifies other admins or approvers
   - Invitation remains pending until approved

4. **Invitation Delivery**
   - System sends email invitation to user
   - Email contains temporary access link
   - Invitation has expiration date (7 days)

5. **User Registration**
   - Recipient clicks link in email
   - System presents registration form with email pre-filled
   - User completes registration (personal details, password)
   - User accepts terms and conditions

6. **Account Setup**
   - System creates user account with corporate role
   - System links user to company
   - User is directed to corporate dashboard
   - Limited access based on assigned role

### User Action: Modify user permissions

1. **Access User Management**
   - Admin navigates to User Management section
   - Admin views list of company users

2. **Select User**
   - Admin locates target user
   - Admin clicks "Edit" or user's name

3. **Modify Roles**
   - Admin views current roles
   - Admin adds/removes roles via checkboxes
   - Admin adds optional note for change reason
   - Admin clicks "Save Changes"

4. **Approval Process** (if required)
   - System checks if role change requires approval
   - If required, creates approval request
   - Notifies approvers
   - Changes remain pending until approved

5. **Notification**
   - System notifies affected user of role changes
   - System logs changes in audit trail

## 3. Bulk Disbursement Flow

### User Action: Create and process bulk disbursement

1. **Initiate Disbursement**
   - User navigates to Disbursements section
   - User clicks "New Bulk Disbursement"

2. **Basic Information**
   - User enters disbursement name
   - User adds description (optional)
   - User clicks "Continue"

3. **File Preparation**
   - User downloads template (if needed)
   - System displays format requirements
   - User prepares CSV/Excel file with recipient data

4. **File Upload**
   - User clicks upload area or "Browse" button
   - User selects prepared file
   - System uploads and begins validation
   - Progress indicator shows validation status

5. **Validation Review**
   - System displays validation results
   - Shows valid entries count and error count
   - Lists specific errors with row references
   - User can download error report

6. **Error Handling** (if needed)
   - User downloads error report
   - User corrects issues in file
   - User re-uploads file or proceeds with valid entries

7. **Disbursement Preview**
   - System shows disbursement summary:
     * Total recipients
     * Total amount
     * Fee calculation (based on corporate rate)
     * Net amount to be debited
   - User reviews details
   - User can view detailed recipient list

8. **Confirmation**
   - User confirms disbursement details
   - User clicks "Submit for Approval" or "Process" (if no approval needed)

9. **Approval Process** (if required)
   - System creates approval request
   - Notifies designated approvers
   - Disbursement remains pending until approved

10. **Processing**
    - Upon approval, system begins processing
    - Checks corporate wallet balance
    - Processes transactions in batches
    - Updates status in real-time

11. **Completion**
    - System displays completion summary
    - Success/failure statistics
    - Option to download detailed report
    - Email notification sent with results

## 4. Corporate Wallet Management Flow

### User Action: Deposit funds to corporate wallet

1. **Access Wallet**
   - User navigates to Wallet section
   - Views current balance and transaction history
   - Clicks "Deposit Funds" button

2. **Deposit Information**
   - User enters deposit amount
   - System displays deposit instructions:
     * Bank account details
     * Reference number to use
     * Processing timeframe
   - Option to download instructions

3. **Notification**
   - User notifies system of payment (optional)
   - User can upload proof of payment (optional)
   - User submits deposit notification

4. **Processing**
   - Finance team verifies deposit
   - Admin confirms deposit receipt
   - System updates wallet balance
   - System creates wallet transaction record

5. **Confirmation**
   - System notifies user of deposit confirmation
   - Updated balance reflected in wallet
   - Transaction appears in history

### User Action: View transaction history

1. **Access Wallet**
   - User navigates to Wallet section
   - Clicks "Transaction History" tab

2. **View Transactions**
   - System displays list of transactions
   - Shows date, type, amount, reference, status
   - Paginated list with newest first

3. **Filter & Search**
   - User can filter by date range
   - User can filter by transaction type
   - User can search by reference or amount
   - User applies filters to refine list

4. **Export**
   - User clicks "Export" button
   - Selects format (CSV, Excel, PDF)
   - System generates and downloads report

## 5. Approval Management Flow

### User Action: Review and approve requests

1. **Access Approvals**
   - Approver sees notification of pending items
   - Navigates to Approvals section
   - Views list of pending approval requests

2. **Review Request**
   - Approver selects request from list
   - System displays complete request details:
     * Requester information
     * Request type and details
     * Associated data (disbursement, user, etc.)
     * Approval status (pending approvals remaining)

3. **Make Decision**
   - Approver reviews all information
   - Approver can add comments
   - Approver selects "Approve" or "Reject"
   - Confirms decision

4. **Completion**
   - System records approval action
   - Updates approval request status
   - If all required approvals received, executes action
   - Notifies requester of outcome

### User Action: Configure approval workflows

1. **Access Settings**
   - Admin navigates to Settings section
   - Selects "Approval Workflows" tab

2. **View Current Workflows**
   - System displays list of configured workflows
   - Shows entity type, thresholds, approver requirements

3. **Modify Workflow**
   - Admin selects workflow to edit
   - Adjusts minimum approver count
   - Sets amount thresholds if applicable
   - Sets which roles can approve
   - Saves changes

4. **Create New Workflow**
   - Admin clicks "Add Workflow" button
   - Selects entity type (disbursement, user management, etc.)
   - Configures approval requirements
   - Sets amount thresholds if applicable
   - Saves new workflow

## 6. Reporting Flow

### User Action: Generate disbursement report

1. **Access Reports**
   - User navigates to Reports section
   - Selects "Disbursement Reports" option

2. **Configure Report**
   - User selects date range
   - User chooses grouping options (daily, weekly, monthly)
   - User selects status filter (all, completed, failed)
   - User clicks "Generate Report"

3. **View Report**
   - System displays report with:
     * Summary statistics
     * Success/failure rates
     * Amount totals
     * Fee totals
     * Graphical representations

4. **Detailed Analysis**
   - User can drill down into specific data points
   - View transaction-level details
   - Filter and sort results
   - Identify patterns or issues

5. **Export Report**
   - User clicks "Export" button
   - Selects format (CSV, Excel, PDF)
   - Chooses export options (full data, summary only)
   - System generates and downloads report

## 7. Corporate Settings Flow

### User Action: Update company profile

1. **Access Settings**
   - Admin navigates to Settings section
   - Selects "Company Profile" tab

2. **View Current Information**
   - System displays current company information
   - Shows verification status
   - Lists uploaded documents

3. **Edit Information**
   - Admin clicks "Edit Profile" button
   - Updates company details as needed
   - Uploads new documents if required
   - Submits changes

4. **Verification Process**
   - Major changes may require re-verification
   - System notifies if verification needed
   - Admin receives confirmation of update

### User Action: View rate tier information

1. **Access Settings**
   - User navigates to Settings section
   - Selects "Rates & Fees" tab

2. **View Current Rate**
   - System displays current rate tier
   - Shows current fee percentage
   - Displays monthly transaction volume
   - Shows progress toward next tier

3. **Rate Tier Information**
   - User can view all available rate tiers
   - System displays qualification requirements
   - Shows comparison between tiers
   - Provides estimated savings with higher tiers

## Error Handling Flows

### Scenario: Insufficient wallet balance for disbursement

1. **Detection**
   - System checks balance before processing disbursement
   - Identifies insufficient funds

2. **Notification**
   - System prevents disbursement processing
   - Displays error message with required amount
   - Suggests deposit workflow
   - Offers option to save disbursement as draft

3. **Resolution**
   - User navigates to wallet section
   - Follows deposit workflow
   - Returns to disbursement to retry

### Scenario: File upload validation errors

1. **Detection**
   - System validates file during upload
   - Identifies formatting or data issues

2. **Error Display**
   - System shows error summary
   - Displays specific errors by row/column
   - Provides guidance on fixing issues

3. **Resolution Options**
   - Download error report
   - Fix and re-upload file
   - Proceed with valid entries only (if allowed)
   - Cancel and start over

### Scenario: Approval request expires

1. **Detection**
   - System identifies expired approval request
   - Marks as expired/cancelled automatically

2. **Notification**
   - Notifies requester of expiration
   - Provides reason and next steps

3. **Resolution**
   - Requester creates new request if still needed
   - System maintains record of expired request
   - New request follows standard approval flow
