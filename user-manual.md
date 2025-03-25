# FNB Mobile Money System User Manual

## Table of Contents
1. [Introduction](#introduction)
2. [System Access](#system-access)
3. [Dashboard Overview](#dashboard-overview)
4. [Company Management](#company-management)
5. [Merchant Management](#merchant-management)
6. [Terminal Management](#terminal-management)
7. [Transaction Processing](#transaction-processing)
8. [System Monitoring](#system-monitoring)
9. [Payment Provider Management](#payment-provider-management)
10. [Cashout Management](#cashout-management)

## 1. Introduction

The FNB mobile money system is a comprehensive solution developed by TechPay for FNB Zambia. It enables the collection of funds from customers using both standalone and integrated point-of-sale (POS) terminals. This manual will guide bank staff through the various features and functionalities of the system.

## 2. System Access

To access the FNB mobile money system:

1. Open your web browser and navigate to the system login page.
2. Enter your email address in the "Email address" field.
3. Enter your password in the "Password" field.
4. If you want the system to remember your login, check the "Remember me" box.
5. Click the "Sign in" button to access the system.

If you've forgotten your password, click on the "Forgot password?" link and follow the instructions to reset it.

![Login Screen](media/image16.png)

## 3. Dashboard Overview

Upon successful login, you will be presented with the system dashboard.

![Dashboard](media/image15.png)

The dashboard provides an at-a-glance view of key statistics:
- Total number of merchants (All Merchants, Active, and Disabled)
- Transaction volume and value (Successful Today, Failed Today, Pending Today)
- To-date transaction statistics (Successful, Failed, Pending)

This information allows you to quickly assess the system's performance and merchant status.

## 4. Company Management

The system allows for the maintenance of company details for KYC (Know Your Customer) purposes.

To manage companies:
1. Navigate to the Company Management section from the main menu.
2. You will see a list of all registered companies.
3. To view details of a specific company, click on the "View" button next to the company name.
4. To edit a company's details, click on the "Edit" button.
5. To add a new company, click on the "New Company Detail" button at the top right of the page.

When viewing a company's details, you'll see information such as:
- Company name and trading name
- Type of ownership
- Registration number (RC number)
- Tax Identification Number (TPIN)
- Nature of business
- Date registered
- Country of incorporation
- Contact information (address, telephone, email, website)

![Company Details](media/image5.png)

## 5. Merchant Management

Merchant management is a crucial part of the system. To manage merchants:

1. Go to the Merchant Management section from the main menu.
2. You will see a list of all merchants in the system.
3. To add a new merchant, click on "New merchants" and fill in the required details.
4. To view or edit an existing merchant, click on the "View" or "Edit" button next to the merchant's name.

When creating or editing a merchant, you'll need to provide:
- Company (select from registered companies)
- Merchant name
- Merchant code
- Description
- Status (Active or Disabled)

![Merchant Listing](media/image14.png)
![Create Merchant](media/image13.png)

Remember, each merchant must be associated with a company for KYC purposes.

## 6. Terminal Management

Terminals are the POS devices used for transactions. To manage terminals:

1. Navigate to the Terminal Management section from the main menu.
2. You will see a list of all terminals in the system.
3. To view details of a specific terminal, click on the "View" button next to the terminal ID.
4. To edit a terminal's details, click on the "Edit" button.
5. To add a new terminal, click on the "New terminals" button at the top right of the page.

Terminal information includes:
- Terminal ID
- Serial number
- Merchant it's assigned to
- Type (POS, SmartPOS, mPOS)
- Model
- Manufacturer
- Status (ACTIVATED, UPLOADED)
- Date activated

![Terminal Listing](media/image9.png)
![Terminal Details](media/image8.png)

## 7. Transaction Processing

The system records all transactions processed through the terminals. To view and manage transactions:

1. Go to the Transactions section from the main menu.
2. You will see a list of all transactions, including their status (FAILED, PENDING, etc.).
3. To view details of a specific transaction, click on the "View" button next to the transaction.

Transaction details include:
- Transaction ID
- Date created and updated
- Amount
- Status
- Merchant reference
- Payment channel
- Provider name and status
- Merchant details

![Transaction List](media/image11.png)
![Transaction Details](media/image10.png)

## 8. System Monitoring

### 8.1 Terminal Heartbeat

Terminals send a heartbeat to the system every 30 minutes, providing:
- Battery health
- Number of transactions
- Terminal location
- Other relevant details

To view terminal heartbeats:
1. Go to the Terminal Heartbeats section from the main menu.
2. You will see a list of recent heartbeats from all terminals.
3. You can view details such as terminal ID, location, battery health, and transaction count.

![Terminal Heartbeats](media/image7.png)

### 8.2 API Logs

Administrators can view API logs for all communications between Mobile Network Operators (MNOs), the bank, and POS terminals.

To view API logs:
1. Go to the API Logs section from the main menu.
2. You will see a list of all API interactions.
3. You can view details such as request type, status, source IP, and timestamps.
4. To view full details of a log entry, click on the "View" button.

![API Logs](media/image4.png)
![API Log Details](media/image3.png)

## 9. Payment Provider Management

Bank administrators can manage payment providers within the system:

1. Navigate to the Payment Providers section from the main menu.
2. You will see a list of current payment providers.
3. To add a new provider, click "New payment providers" and enter the required details, including API keys.
4. To view or edit an existing provider, click the "View" or "Edit" button next to the provider's name.

Payment provider details include:
- Provider name
- Code
- Status (ACTIVE, INACTIVE)
- Environment (sandbox, production)
- API details (key ID, secret, URL, token)

![Payment Provider List](media/image2.png)
![Payment Provider Details](media/image1.png)
