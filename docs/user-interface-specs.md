# User Interface Specifications

## Design System

### Core Color Palette
- **Primary**: #3366CC (Blue) - for main actions, headers, key elements
- **Secondary**: #FF9900 (Orange) - for CTAs, highlights, accent elements
- **Success**: #28A745 (Green) - for confirmations, completed statuses
- **Warning**: #FFC107 (Yellow) - for alerts, pending statuses
- **Error**: #DC3545 (Red) - for errors, failed statuses
- **Light**: #F8F9FA (Off-white) - for backgrounds, cards
- **Dark**: #343A40 (Dark gray) - for text, borders

### Typography
- **Primary Font**: Inter (Sans-serif)
- **Heading Sizes**:
  * H1: 32px/2rem
  * H2: 24px/1.5rem
  * H3: 20px/1.25rem
  * H4: 18px/1.125rem
- **Body Text**: 16px/1rem
- **Small Text**: 14px/0.875rem

### Responsive Breakpoints
- **Mobile**: < 576px
- **Tablet**: 576px - 991px
- **Desktop**: ≥ 992px

## Page Specifications

### 1. Landing Page

#### Content Blocks
1. **Hero Section**
   - Headline: "Fund your mobile wallet instantly"
   - Subheading: "Secure, fast transfers from your card to mobile money"
   - CTA Button: "Get Started"
   - Background: Gradient or image showing mobile/payment concept

2. **How It Works**
   - 3-step process with icons:
     1. Enter mobile number and amount
     2. Make secure card payment
     3. Receive funds in your mobile wallet
   - Each step with brief description

3. **Fee Structure**
   - Clear explanation of 4% fee
   - Visual breakdown of 3% bank fee and 1% service fee
   - Example calculation

4. **Benefits Section**
   - 3-4 key benefits with icons:
     * Security (no card storage)
     * Speed (instant transfers)
     * Convenience (anytime, anywhere)
     * Multiple wallet support

5. **FAQ Preview**
   - 3-4 most common questions with expandable answers

6. **Footer**
   - Links to About, Contact, Terms, Privacy Policy
   - Copyright information

### 2. Registration & Login Pages

#### Registration Form Elements
- Email address input
- Phone number input with country code
- First name and last name inputs
- Password input with strength indicator
- Confirm password input
- Terms and conditions checkbox
- "Create Account" button
- "Already have an account? Log in" link

#### Login Form Elements
- Email/phone input
- Password input
- "Remember me" checkbox
- "Log in" button
- "Forgot password?" link
- "Create an account" link

### 3. User Dashboard

#### Components
1. **Header**
   - Logo
   - User dropdown menu
   - Notifications icon

2. **Navigation**
   - Dashboard link
   - Transactions link
   - Beneficiaries link
   - Profile link
   - Support link

3. **Main Dashboard Content**
   - Verification status card
   - Quick transaction widget
   - Recent transactions (last 5)
   - Saved beneficiaries quick access

4. **Call to Action**
   - "Fund Wallet" prominent button

### 4. Transaction Flow Pages

#### Step 1: Transaction Initiation
- Mobile provider dropdown
- Mobile number input field
- Amount input field
- Fee calculation display (updating in real-time)
- Total amount display
- "Proceed" button

#### Step 2: Recipient Confirmation
- Recipient information display:
  * Name (from mobile money provider)
  * Mobile provider
  * Mobile number
- Transaction details:
  * Amount
  * Fee
  * Total payment
- "Save this recipient" checkbox
- "Back" and "Proceed to Payment" buttons

#### Step 3: MPGS Redirect
- Loading animation
- "Connecting to secure payment" message
- Instructions not to refresh or close

#### Step 4: Success/Failure Screen
- Success icon or error icon
- Transaction status heading
- Transaction reference
- Date and time
- Recipient details
- Amount details
- Action buttons based on outcome

### 5. Beneficiaries Management

#### Components
- "Add New" button
- Search/filter field
- Beneficiaries list with:
  * Name
  * Mobile number and provider
  * Last used date
  * Action buttons (Edit, Delete)
- Empty state for new users

#### Add/Edit Modal
- Beneficiary name input
- Mobile provider dropdown
- Mobile number input
- Optional note field
- Save button

### 6. Transaction History

#### Components
- Date filter
- Status filter
- Search field
- Transactions table with:
  * Date/time
  * Recipient
  * Amount
  * Status indicator
  * Action button (View details)
- Pagination controls

#### Transaction Detail Modal
- Transaction reference
- Status indicator
- Complete transaction details
- Receipt download button

### 7. Profile Management

#### Components
- Personal information section
- Email and phone verification status
- KYC document upload section
- Password change section
- Notification preferences

#### KYC Document Upload
- Document type selector
- Document number input
- Expiry date input
- File upload areas
- Submit button

### 8. Support/FAQ Page

#### Components
- Search box
- Category filters
- FAQ accordions
- Contact form
- Problem reporting form

## Mobile Adaptations

### Global Mobile Considerations
- Stack elements vertically
- Increase touch target sizes (minimum 44x44px)
- Full-width buttons
- Simplified navigation via bottom bar
- Reduced padding/margins
- Font size adjustments for readability

### Specific Mobile Adaptations
1. **Dashboard**
   - Collapse navigation into hamburger menu
   - Stack dashboard cards vertically
   - Simplify quick actions into icon buttons

2. **Transaction Flow**
   - Single step per screen
   - Larger input fields
   - Full-width buttons
   - Simplified confirmations

3. **Transaction History**
   - Replace table with card-based list
   - Swipe actions for details
   - Simplified filters via dropdown

## Accessibility Requirements

- Ensure color contrast meets WCAG 2.1 AA standards (4.5:1 for normal text)
- Implement keyboard navigation support
- Add screen reader support via ARIA attributes
- Maintain proper heading hierarchy
- Provide text alternatives for non-text content
- Ensure form inputs have associated labels
- Add visible focus states for all interactive elements
