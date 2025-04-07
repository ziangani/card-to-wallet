<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - TechPay</title>
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

        p {
            margin-bottom: 1.5rem;
        }

        .section {
            margin-bottom: 2rem;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .contact-info {
            padding-right: 2rem;
        }

        .contact-form {
            padding-left: 2rem;
            border-left: 1px solid #eee;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        input, textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 1rem;
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        button {
            background-color: #007751;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #005a3d;
        }

        .contact-card {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .contact-card h3 {
            margin-top: 0;
            color: #007751;
        }

        .contact-icon {
            display: inline-block;
            width: 40px;
            height: 40px;
            background-color: #007751;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            margin-right: 1rem;
            font-size: 1.2rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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

            .contact-grid {
                grid-template-columns: 1fr;
            }

            .contact-form {
                padding-left: 0;
                border-left: none;
                border-top: 1px solid #eee;
                padding-top: 2rem;
                margin-top: 2rem;
            }

            .contact-info {
                padding-right: 0;
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
        <h1>Contact Us</h1>

        <div class="section">
            <p>Have questions about our card-to-wallet transfer service? Need assistance with a transaction? We're here to help! Choose the most convenient way to reach us below.</p>
        </div>

        <div class="contact-grid">
            <div class="contact-info">
                <h2>Get in Touch</h2>
                
                <div class="contact-card">
                    <h3><i class="contact-icon">📞</i> Phone Support</h3>
                    <p>Our customer service team is available Monday to Friday, 8:00 AM to 6:00 PM.</p>
                    <p><strong>Phone:</strong> +260 76 418 8643</p>
                    <p><strong>WhatsApp:</strong> +260 76 418 8643</p>
                </div>
                
                <div class="contact-card">
                    <h3><i class="contact-icon">✉️</i> Email</h3>
                    <p>For general inquiries and support:</p>
                    <p><strong>Email:</strong> support@techpay.co.zm</p>
                    <p>For business partnerships:</p>
                    <p><strong>Email:</strong> partnerships@techpay.co.zm</p>
                </div>
                
                <div class="contact-card">
                    <h3><i class="contact-icon">🏢</i> Office Location</h3>
                    <p><strong>Address:</strong> Plot No. 123, Great East Road, Lusaka, Zambia</p>
                    <p><strong>Business Hours:</strong> Monday to Friday, 8:00 AM to 5:00 PM</p>
                </div>
                
                <div class="contact-card">
                    <h3><i class="contact-icon">🌐</i> Social Media</h3>
                    <p>Connect with us on social media for updates, tips, and promotions:</p>
                    <p>
                        <strong>Facebook:</strong> <a href="https://facebook.com/techpayzambia" target="_blank">@techpayzambia</a><br>
                        <strong>Twitter:</strong> <a href="https://twitter.com/techpayzambia" target="_blank">@techpayzambia</a><br>
                        <strong>LinkedIn:</strong> <a href="https://linkedin.com/company/techpay-limited" target="_blank">Techpay Limited</a>
                    </p>
                </div>
            </div>
            
            <div class="contact-form">
                <h2>Send Us a Message</h2>
                
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin: 0; padding-left: 1rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="name">Your Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" required>{{ old('message') }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit">Send Message</button>
                    </div>
                    
                    <p style="font-size: 0.9rem; color: #666;">* Required fields</p>
                </form>
            </div>
        </div>

        <div class="section">
            <h2>Frequently Asked Questions</h2>
            <p>Before contacting us, you might find answers to your questions in our <a href="{{ url('/faq') }}">FAQ section</a>.</p>
        </div>
    </div>
</div>
</body>
</html>
