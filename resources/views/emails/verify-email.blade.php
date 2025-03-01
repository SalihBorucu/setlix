<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
    <style>
        /* Base styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.5;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 1rem;
        }
        .title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }
        .content {
            background-color: #ffffff;
            border-radius: 0.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .button {
            display: inline-block;
            background-color: #0ea5e9;
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-weight: 500;
            margin: 1rem 0;
        }
        .button:hover {
            background-color: #0284c7;
            color: #ffffff;
        }
        .footer {
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 2rem;
        }
        .expires {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 1rem;
        }
    </style>
</head>
<body style="background-color: #f3f4f6;">
    <div class="container">
        <div class="header">
            <h1>SETLIX</h1>
            <h1 class="title">Verify Your Email Address</h1>
        </div>

        <div class="content">
            <p>Hello!</p>

            <p>Thanks for signing up for Setlix! Please verify your email address to get started.</p>

            <p>With Setlix, you'll be able to:</p>

            <ul style="margin: 1rem 0; padding-left: 1.5rem;">
                <li>Create and manage bands</li>
                <li>Build and organize setlists</li>
                <li>Collaborate with band members</li>
                <li>Access all features during your trial period</li>
            </ul>

            <p>Click the button below to verify your email address:</p>

            <div style="text-align: center;">
                <a href="{{ $url }}" class="button">
                    <span :style="{ color: '#ffffff !important' }">Verify Email Address</span>
                </a>
            </div>

            <p class="expires">This verification link will expire in {{ config('auth.verification.expire', 60) }} minutes.</p>
        </div>

        <div class="footer">
            <p>If you did not create an account, no further action is required.</p>
            <p>&copy; {{ date('Y') }} Setlix. All rights reserved.</p>
        </div>
    </div>
</body>
</html> 