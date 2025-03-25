<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - TechPay</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem 0;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .logo {
            max-width: 200px;
            margin-bottom: 1rem;
        }

        h1 {
            color: #1a1a1a;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        h2 {
            color: #2c3e50;
            font-size: 1.8rem;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }

        .content {
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .last-updated {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        p {
            margin-bottom: 1.5rem;
        }

        .section {
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            h1 {
                font-size: 2rem;
            }

            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
<div class="header">
    <div class="container">
        <a href="{{url('/')}}">
            <img src="{{url('/assets/img/logo.png')}}" alt="TechPay Logo" class="logo">
        </a>
    </div>
</div>

<div class="container">
    <div class="content">
        <h1>Terms and Conditions</h1>
        <p class="last-updated">Last Updated: 12 December 2023</p>

        <div class="section">
            <p>Welcome to Techpay Limited! By accessing or using Techpay Limited's services ("Services"), you, the
                Merchant, agree to comply with and be bound by the following terms and conditions ("Terms"). These Terms
                govern your access to and use of the Techpay platform ("Platform") for payment processing, including
                card transactions via Cybersource and Mastercard Payment Gateway and mobile money services. Please read
                these Terms carefully before using our services.</p>
        </div>

        <div class="section">
            <h2>1. Acceptance of Terms</h2>
            <p>By registering as a Merchant or using any service provided by Techpay Limited, you agree to abide by
                these Terms. If you do not agree with these Terms, please discontinue the use of the Platform
                immediately.</p>
        </div>

        <div class="section">
            <h2>2. Definitions</h2>
            <p>Techpay Limited: A fintech payment facilitator offering services such as card processing, mobile money
                processing, and other related financial services.</p>
            <p>Platform: The online and mobile application through which Techpay services are offered.</p>
            <p>Merchant: Any business or organization registered on the Techpay platform to process payments for
                products or services offered to their customers ("Clients").</p>
            <p>Clients: End-customers of the Merchant who utilize the Techpay payment services, such as through checkout
                pages provided by the Platform.</p>
            <p>Card Networks: Includes Visa, Mastercard, and any other applicable card schemes.</p>
            <p>Chargebacks: The reversal of a transaction initiated by the Client's issuing bank, typically due to
                disputes regarding the transaction.</p>
        </div>

        <div class="section">
            <h2>3. Eligibility and Onboarding Requirements</h2>
            <p>To use the Techpay platform, Merchants must:</p>
            <ul>
                <li>Be a fully registered organization within Zambia or other approved jurisdictions.</li>
                <li>Provide certified documentation, including but not limited to: Certificate of Incorporation,
                    Taxpayer Identification Number (TPIN), Business License, and proof of business address.
                </li>
                <li>Agree to adhere to all applicable local and international regulations, including anti-money
                    laundering (AML) and anti-fraud regulations.
                </li>
                <li>Comply with Visa, Mastercard, and other card network rules and requirements.</li>
            </ul>
            <p>Merchants must provide accurate, current, and complete information during the onboarding process and
                update such information as needed. Submission of fraudulent or false information will result in the
                refusal of the application or termination of service.</p>
        </div>

        <div class="section">
            <h2>4. Prohibited Business Activities</h2>
            <p>Techpay Limited adheres to strict guidelines issued by the card networks and regulatory bodies. As such,
                the following types of business activities are prohibited:</p>
            <ul>
                <li>Selling illegal or restricted goods or services, including prescription drugs, counterfeit goods,
                    intellectual property-infringing items, illicit adult content, and illegal gambling.
                </li>
                <li>Engaging in fraudulent or misleading activities, including phishing, money laundering, and data
                    harvesting without consent.
                </li>
                <li>Rogue cyberlocker activities, selling or distributing unauthorized or illegal digital content.</li>
            </ul>
            <p>Failure to comply with these restrictions will result in the immediate suspension or termination of
                service and may trigger further action, including reporting to regulatory or law enforcement
                authorities.</p>
        </div>

        <div class="section">
            <h2>5. Card Transaction Processing and Security</h2>
            <p>Techpay enables the processing of card transactions via Cybersource and Mastercard payment gateways.
                Merchants are responsible for ensuring:</p>
            <ul>
                <li>Transactions processed via the Techpay platform adhere to the terms and conditions of Visa,
                    Mastercard, and other relevant card schemes.
                </li>
                <li>Cardholder data is handled in compliance with the Payment Card Industry Data Security Standard (PCI
                    DSS) requirements.
                </li>
                <li>No card data is stored on any merchant website or system in violation of PCI DSS.</li>
            </ul>
            <p>Merchants are required to implement adequate security measures, such as SSL certificates, to protect
                customer data. Merchants failing to comply with security obligations may be held liable for any
                resulting fraud or data breaches.</p>
        </div>

        <div class="section">
            <h2>6. Mobile Money Transactions</h2>
            <p>Techpay also facilitates mobile money transactions. Merchants agree to:</p>
            <ul>
                <li>Comply with all local mobile money regulations and the terms of service of mobile network
                    operators.
                </li>
                <li>Ensure all mobile money transactions are legitimate, accurately reported, and properly documented.
                </li>
            </ul>
        </div>

        <div class="section">
            <h2>7. Merchant Obligations</h2>
            <p>By using the Platform, Merchants agree to:</p>
            <ul>
                <li>Use the Platform solely for lawful purposes, and not to engage in any activity that may violate
                    Zambian law or international payment regulations.
                </li>
                <li>Cooperate with Techpay in any compliance investigations and promptly provide requested documents or
                    information.
                </li>
                <li>Take responsibility for all activities that occur under their account and immediately notify Techpay
                    of any unauthorized use.
                </li>
            </ul>
        </div>

        <div class="section">
            <h2>8. Transaction Monitoring and Compliance</h2>
            <p>Techpay reserves the right to monitor transactions for potential fraud, money laundering, or other
                illegal activities. Merchants acknowledge that:</p>
            <ul>
                <li>Transactions may be subject to review, delay, or cancellation to comply with regulatory
                    requirements.
                </li>
                <li>Techpay may report suspicious activity to the appropriate authorities in line with AML
                    regulations.
                </li>
            </ul>
        </div>

        <div class="section">
            <h2>9. Chargebacks and Merchant Liability</h2>
            <p>Merchant Liability for Chargebacks: Merchants accept full responsibility for any chargebacks initiated by
                Clients. Even if a card transaction is authorized, the Merchant may still be liable for chargebacks,
                particularly if the Client disputes the transaction.</p>
            <p>Techpay's Role in Chargebacks: Techpay will notify the Merchant of any chargebacks and provide the
                Merchant with details of the disputed transaction. Techpay may debit the Merchant's account for the
                amount of any chargeback, as well as any applicable fees.</p>
            <p>Merchant's Responsibility: It is the Merchant's responsibility to handle disputes with Clients and ensure
                that clear policies regarding returns, refunds, cancellations, and chargebacks are in place and
                communicated to Clients.</p>
            <p>Chargeback Reserves: Techpay reserves the right to withhold a portion of payments as a reserve against
                potential chargebacks, as stipulated in the Merchant agreement.</p>
        </div>

        <div class="section">
            <h2>10. Settlement of Funds</h2>
            <p>Payments processed via the Techpay platform will be settled according to the agreed-upon schedule.
                Settlement funds will be transferred to the designated bank or mobile money accounts provided by the
                Merchant, who is responsible for ensuring the accuracy of the provided banking details.</p>
        </div>

        <div class="section">
            <h2>11. Data Privacy and Confidentiality</h2>
            <p>Techpay complies with Zambian data protection laws and is committed to protecting Merchant and Client
                information. By using the Platform, Merchants consent to Techpay collecting, processing, and storing
                personal and transactional data as necessary to provide the Services.</p>
            <p>Merchants are required to:</p>
            <ul>
                <li>Safeguard all customer (Client) data obtained through transactions.</li>
                <li>Implement necessary data protection policies, including customer privacy and cookie policies, in
                    compliance with applicable regulations.
                </li>
            </ul>
        </div>

        <div class="section">
            <h2>12. Liability and Indemnification</h2>
            <p>Merchants agree to indemnify and hold Techpay harmless from any loss, damage, or legal action resulting
                from:</p>
            <ul>
                <li>The misuse of the Techpay platform or violation of these Terms.</li>
                <li>Security breaches due to the Merchant's failure to implement necessary security measures.</li>
                <li>Fraudulent activities or violations of local laws by the Merchant or any of its employees, agents,
                    or representatives.
                </li>
            </ul>
            <p>Techpay is not liable for losses resulting from unauthorized access to Merchant accounts, transaction
                delays, or interruptions in service.</p>
        </div>

        <div class="section">
            <h2>13. Termination of Service</h2>
            <p>Techpay reserves the right to suspend or terminate services if Merchants violate these Terms, engage in
                fraudulent activities, or fail to comply with legal or regulatory requirements.</p>
        </div>

        <div class="section">
            <h2>14. Amendments to Terms</h2>
            <p>Techpay may update these Terms periodically. Merchants will be notified of any changes and continued use
                of the Platform following such updates constitutes acceptance of the revised Terms.</p>
        </div>

        <div class="section">
            <h2>15. Governing Law</h2>
            <p>These Terms shall be governed by and construed in accordance with the laws of Zambia. Any disputes
                arising under these Terms shall be subject to the exclusive jurisdiction of the courts of Zambia.</p>
        </div>

        <div class="section">
            <h2>Contact Information</h2>
            <p>For questions regarding these Terms or the Techpay platform, please contact our support team at:</p>
            <p>Telephone & WhatsApp: +260764188643</p>
        </div>
    </div>
</div>
</body>
</html>
