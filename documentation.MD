# TechPay Core API Documentation

This document provides information about the available APIs in the TechPay Core project.

## Terminal API

### Initialize Parameters

- **Endpoint:** `POST /api/terminal/initialize`
- **Description:** Initializes the terminal parameters with merchant and API information.
- **Required Parameters:**
  - `sn` (string): The serial number of the terminal.
- **Response:**
  - Success: Returns initialized terminal parameters including API key, API secret, merchant name, merchant code, and terminal ID.
  - Error: Returns an error message if the terminal is not found or validation fails.

### Heartbeat

- **Endpoint:** `POST /api/terminal/heartbeat`
- **Description:** Sends heartbeat data from the terminal to the server.
- **Required Parameters:**
  - `sn` (string): The serial number of the terminal.
  - `location` (string): The current location of the terminal.
  - `battery_health` (integer): The battery health status of the terminal.
  - `transactions_count` (integer): The number of transactions processed by the terminal.
- **Optional Parameters:**
  - `misc` (JSON): Additional miscellaneous information.
- **Response:**
  - Success: Returns a confirmation that the heartbeat data was received successfully.
  - Error: Returns an error message if the terminal is not found or validation fails.

## Hosted Checkout API

The following endpoints require merchant authentication:

### Get Token

- **Endpoint:** `POST /api/hc/gettoken`
- **Description:** Generates a token for a new payment request.
- **Required Parameters:**
  - `orderNumber` (string): Unique identifier for the order.
  - `description` (string): Description of the payment (max 255 characters).
  - `amount` (numeric): Amount to be paid (minimum 1).
  - `returnURL` (url): URL to redirect after payment completion.
- **Optional Parameters:**
  - `callbackURL` (url): URL for receiving payment status updates.
  - `channel` (string): Payment channel (POS, BOT, WEB, APP, ATM, USSD, WHATSAPP).
- **Response:**
  - Success: Returns a token and payment link for the checkout process.
  - Error: Returns an error message if validation fails or there's a duplicate token.

### Status Check

- **Endpoint:** `POST /api/hc/statuscheck`
- **Description:** Checks the status of a payment request.
- **Required Parameters:**
  - `token` (string): The token received from the getToken endpoint.
- **Response:**
  - Success: Returns the current status of the payment request, including order details and payment information.
  - Error: Returns an error message if the token is not found or invalid.

### Pay with Mobile Money

- **Endpoint:** `POST /api/ic/pay/mobilemoney`
- **Description:** Initiates a mobile money payment for a given token.
- **Required Parameters:**
  - `mobileNumber` (numeric): The mobile number to charge (10-12 digits).
  - `token` (string): The token received from the getToken endpoint.
- **Optional Parameters:**
  - `reference` (string): An optional reference for the transaction.
- **Response:**
  - Success: Returns a confirmation that the mobile money request was initiated successfully.
  - Error: Returns an error message if validation fails, token is invalid, or the payment provider encounters an error.

### Generate Token and Send Mobile Money Request

- **Endpoint:** `POST /api/ic/pay/mobilemoney/instant`
- **Description:** Generates a token and initiates a mobile money payment in one step.
- **Required Parameters:**
  - `orderNumber` (string): Unique identifier for the order.
  - `description` (string): Description of the payment (max 255 characters).
  - `amount` (numeric): Amount to be paid (minimum 1).
  - `returnURL` (url): URL to redirect after payment completion.
  - `mobileNumber` (numeric): The mobile number to charge (10-12 digits).
- **Optional Parameters:**
  - `callbackURL` (url): URL for receiving payment status updates.
  - `channel` (string): Payment channel (POS, BOT, WEB, APP, ATM, USSD, WHATSAPP).
- **Response:**
  - Success: Returns a token for the initiated payment request.
  - Error: Returns an error message if validation fails, payment provider is not found, or the mobile money request fails.

Note: All endpoints return a unique `requestReference` in the response for tracking purposes. Error responses include an error code and a descriptive message.