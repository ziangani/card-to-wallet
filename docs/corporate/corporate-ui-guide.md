# Corporate Module UI/UX Guidelines

## Design Principles

The corporate module follows these key design principles:

1. **Professional & Enterprise-Focused**: Clean, minimal design with emphasis on data clarity and efficient workflows
2. **Action-Oriented**: Prominent calls to action that guide corporate users through multi-step processes
3. **Information Density**: Higher information density than consumer interfaces, catering to power users
4. **Hierarchical Navigation**: Clear navigation structure that reflects corporate roles and permissions
5. **Consistency with Brand**: Maintains core brand elements while distinguishing the corporate experience

## Design System Extensions

### Colors

Maintain the core color palette with these additions/modifications:

- **Corporate Primary**: #2C3E50 (Dark Blue) - Slightly more conservative than consumer primary
- **Corporate Secondary**: #34495E (Slate) - Secondary actions and UI elements
- **Corporate Accent**: #3498DB (Bright Blue) - Highlighting important data points
- **Approval Green**: #27AE60 - For approved/completed statuses
- **Pending Amber**: #F39C12 - For pending/in-progress statuses
- **Rejection Red**: #C0392B - For rejected/failed statuses

### Typography

- Continue using Inter as the primary font
- Slightly reduce heading sizes compared to consumer portal:
  * H1: 28px/1.75rem
  * H2: 22px/1.375rem
  * H3: 18px/1.125rem
  * H4: 16px/1rem
- Increase table/data display font weight to 500 for better legibility

### Component Modifications

1. **Data Tables**:
   - Denser row height (48px vs 64px)
   - Column sorting and filtering capabilities
   - Bulk action support
   - Expandable rows for additional details
   - Export functionality (CSV, Excel)

2. **Forms**:
   - Multi-step workflows with progress indicators
   - Validation summaries at top for complex forms
   - Collapsible sections for lengthy forms
   - Field dependencies and conditional logic

3. **Cards**:
   - More compact padding (12px vs 16px)
   - Tabbed content for information grouping
   - Status indicators in headers
   - Action buttons in footers

4. **Navigation**:
   - Left sidebar navigation (vs top navigation in consumer)
   - Role-based menu visibility
   - Context-aware secondary navigation
   - Breadcrumbs for deep navigation structures

## Page Layouts

### 1. Corporate Dashboard

**Layout Structure**:
- Top bar with company name, notifications, profile menu
- Left sidebar navigation
- Main content area with 3-column grid of summary cards
- Two-column layout below with activity feed and quick actions

**Key Components**:
- **Balance Card**: Current wallet balance with deposit button
- **Pending Approvals**: Count of items needing approval with direct link
- **Recent Disbursements**: Summary of latest batch operations
- **Transaction Metrics**: Charts showing volume and success rates
- **Quick Actions Panel**: Frequent tasks like new batch, add user, etc.
- **Activity Timeline**: Recent system activities by all team members

### 2. Corporate Registration

**Layout Structure**:
- Multi-step form with progress indicator
- Side panel explaining benefits and requirements
- Preview/summary before submission

**Key Components**:
- **Step Indicator**: Showing company details, document upload, review
- **Company Information Form**: Core business details
- **Document Upload**: Drag-and-drop interface for business documents
- **Primary Admin Setup**: Details for primary account holder
- **Review Summary**: Final review before submission
- **Submission Confirmation**: Next steps and timeline

### 3. User Management

**Layout Structure**:
- Two-column layout with users list and detail view
- Action bar with add/edit/remove controls
- Role matrix view for advanced permissions

**Key Components**:
- **Users Table**: List of all users with roles and statuses
- **User Detail Panel**: Profile and permission details
- **Role Editor**: Matrix interface for permission assignment
- **Invite Form**: Email-based invitation system
- **Activity Log**: Audit trail of user management actions

### 4. Bulk Disbursement

**Layout Structure**:
- Wizard-style interface with clearly defined steps
- Summary panels showing batch statistics
- Validation results with actionable error handling

**Key Components**:
- **Template Download**: Access to standardized CSV/Excel templates
- **File Upload**: Drag-and-drop with validation feedback
- **Validation Results**: Table showing parsing results with error highlights
- **Disbursement Preview**: Scrollable preview of all transactions
- **Confirmation Form**: Summary statistics and authorization
- **Processing Status**: Real-time progress indicators
- **Results Summary**: Success/failure breakdown with export option

### 5. Corporate Wallet

**Layout Structure**:
- Summary header with balance and quick actions
- Tabbed interface for transactions, deposits, settings
- Sidebar for rate information and account status

**Key Components**:
- **Balance Card**: Current balance with deposit/withdraw options
- **Transaction History**: Advanced filtering and search capabilities
- **Deposit Instructions**: Banking details for manual deposits
- **Rate Information**: Current fee structure with tier progress
- **Settings Panel**: Notification preferences and account limits

### 6. Approval Workflows

**Layout Structure**:
- Queue-based interface showing items needing approval
- Detail panel for reviewing specific items
- Action buttons for approve/reject/defer decisions

**Key Components**:
- **Approval Queue**: Sorted by priority and submission time
- **Detail View**: Complete information about the item needing approval
- **Comment System**: Internal notes for approval process
- **Audit Trail**: History of approval actions and changes
- **Batch Actions**: Support for approving multiple similar items

## Mobile Adaptations

### Responsive Strategy

1. **Priority Content**: Identify critical functions for mobile corporate users
2. **Progressive Disclosure**: Hide secondary information behind expandable sections
3. **Action Consolidation**: Group related actions in dropdown menus
4. **Vertical Reflow**: Reflow multi-column layouts to single column stacks

### Mobile-Specific Components

1. **Hamburger Navigation**: Collapsible navigation for all menu options
2. **Action Sheets**: Bottom sheets for contextual actions instead of dropdown menus
3. **Simplified Tables**: Card-based alternatives to complex data tables
4. **Stepper Inputs**: Touch-friendly numeric inputs for financial amounts
5. **Floating Action Buttons**: For primary actions within each context

## Key User Journeys

### Corporate Onboarding Journey

1. **Registration Entry Point**:
   - Option on login page: "Register as a Corporate User"
   - Clear presentation of benefits and requirements
   - Simple company name and email to start

2. **Information Collection**:
   - Progressive disclosure of required fields
   - Clear indicators for mandatory information
   - Ability to save and resume later

3. **Document Submission**:
   - Simple file uploader with format guidelines
   - Status indicators for uploaded documents
   - Preview capability before submission

4. **Verification Status**:
   - Clear timeline for approval process
   - Status dashboard showing verification progress
   - Proactive email notifications for status changes

### Bulk Disbursement Journey

1. **Preparation**:
   - Clear guideline on format requirements
   - Template download with example data
   - Estimated processing times and fee calculations

2. **Data Submission**:
   - File upload with format validation
   - Progress indicators for large files
   - Immediate feedback on basic file integrity

3. **Validation Review**:
   - Clear summary of validation results
   - Actionable error messages with row/column references
   - Options to fix or proceed with valid entries only

4. **Approval Process**:
   - Clear indicator of required approvals based on amount
   - Notification to approvers
   - Status tracking for initiator

5. **Execution**:
   - Real-time processing updates
   - Success/failure breakdown
   - Detailed error logs for failed transactions
   - Receipt generation for completed batch

## Accessibility Considerations

1. **Role-Based Accessibility**:
   - Ensure screen readers announce user roles and permissions context
   - Implement ARIA landmarks for corporate-specific sections

2. **Complex Data Tables**:
   - Proper table markup with scope attributes
   - Row/column headers for all data tables
   - Alternative text-based formats for data export

3. **Multi-Step Forms**:
   - Error summaries at form beginning
   - Logical tab order through form fields
   - Clear step transitions with announcements

4. **Financial Data**:
   - Ensure currency values are properly announced
   - Avoid relying solely on color for status indicators
   - Provide alternatives to visual charts and graphs

## User Interface Elements

### Corporate Header

```
+-------------------------------------------------------+
| LOGO | Company Name ▼ |                  | 🔔 | 👤 ▼ |
+-------------------------------------------------------+
```

### Corporate Navigation

```
+------------------+--------------------------------+
| Dashboard        |                                |
| Users            |                                |
| Disbursements    |                                |
| Wallet           |                                |
| Reports          |                                |
| Settings         |                                |
+------------------+                                |
|                  |                                |
|                  |                                |
|                  |                                |
|                  |                                |
|                  |                                |
+------------------+--------------------------------+
```

### Balance Card

```
+------------------------------------------+
| Current Balance                          |
| K 150,000.00                             |
|                                          |
| [Deposit Funds]    [View Transactions]   |
+------------------------------------------+
```

### Bulk Disbursement Wizard

```
+--------------------------------------------------+
| New Bulk Disbursement                            |
| [Upload] > [Validate] > [Review] > [Confirm]     |
+--------------------------------------------------+
|                                                  |
| [ Download Template ]                            |
|                                                  |
| Drag files here or click to upload               |
|                                                  |
| Supported formats: CSV, XLSX                     |
|                                                  |
|                                                  |
| [ Next Step ]                [ Save as Draft ]   |
+--------------------------------------------------+
```

### Validation Results

```
+--------------------------------------------------+
| Validation Results                               |
+--------------------------------------------------+
| ✅ 245 Valid Entries    ❌ 3 Errors              |
|                                                  |
| Error Summary:                                   |
| - Invalid phone number format (Row 12, 56, 198)  |
|                                                  |
| [ View All Errors ]    [ Download Error Report ] |
|                                                  |
| [ Fix and Re-upload ]  [ Proceed with Valid ]    |
+--------------------------------------------------+
```

### Approval Queue

```
+--------------------------------------------------+
| Pending Approvals (12)                           |
+--------------------------------------------------+
| Filters: [ All Types ▼ ] [ Date Range ▼ ]        |
|                                                  |
| Type       | Amount    | Submitted | Initiator   |
|------------|-----------|-----------|-------------|
| Bulk Pay   | 25,400.00 | Today     | J. Phiri    |
| New User   | -         | Today     | M. Banda    |
| Rate Change| -         | Yesterday | System      |
|            |           |           |             |
| [ Approve All ]                  [ Reject All ]  |
+--------------------------------------------------+
```

These guidelines provide a comprehensive framework for creating a professional, efficient, and user-friendly corporate module that clearly differentiates from the consumer experience while maintaining overall brand consistency.
