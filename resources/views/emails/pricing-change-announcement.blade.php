<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Update</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
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
        .highlight-box {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: #ffffff;
            padding: 1.5rem;
            border-radius: 0.5rem;
            text-align: center;
            margin: 1.5rem 0;
        }
        .highlight-box .old-price {
            font-size: 1.25rem;
            text-decoration: line-through;
            opacity: 0.7;
        }
        .highlight-box .new-price {
            font-size: 2rem;
            font-weight: bold;
            display: block;
            margin: 0.5rem 0;
        }
        .highlight-box .savings {
            background-color: #22c55e;
            color: #ffffff;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 0.5rem;
        }
        .benefits {
            background-color: #f0fdf4;
            border-left: 4px solid #22c55e;
            padding: 1rem 1.5rem;
            margin: 1.5rem 0;
            border-radius: 0 0.5rem 0.5rem 0;
        }
        .benefits ul {
            margin: 0.5rem 0;
            padding-left: 1.25rem;
        }
        .benefits li {
            margin: 0.5rem 0;
            color: #166534;
        }
        .button {
            display: inline-block;
            background-color: #0ea5e9;
            color: #ffffff !important;
            padding: 0.875rem 2rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-weight: 600;
            margin: 1rem 0;
        }
        .button:hover {
            background-color: #0284c7;
        }
        .footer {
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 2rem;
        }
        .emoji {
            font-size: 1.5rem;
        }
    </style>
</head>
<body style="background-color: #f3f4f6;">
    <div class="container">
        <div class="header">
            <h1>SETLIX</h1>
            <h1 class="title">We've Made Things Simpler (and Cheaper!)</h1>
        </div>

        <div class="content">
            <p>Hey {{ $userName }},</p>

            <p>Setlix, the app that helps your band stay organised, just got a whole lot cheaper.</p>

            <p>We've switched from monthly to <strong>yearly pricing</strong> - and slashed the price while we were at it.</p>

            <div class="highlight-box" style="background-color: #0ea5e9; color: #ffffff; padding: 1.5rem; border-radius: 0.5rem; text-align: center; margin: 1.5rem 0;">
                <span class="old-price" style="font-size: 1.25rem; text-decoration: line-through; opacity: 0.7;">Was: {{ $symbol }}{{ $oldMonthlyPrice }}/month</span>
                <span class="new-price" style="font-size: 2rem; font-weight: bold; display: block; margin: 0.5rem 0;">Now: {{ $symbol }}{{ $price }}/year</span>
                <span class="savings" style="background-color: #22c55e; color: #ffffff; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.875rem; font-weight: 600; display: inline-block; margin-top: 0.5rem;">50% OFF Launch Price!</span>
            </div>

            <div class="benefits">
                <strong>What this means for you:</strong>
                <ul>
                    <li><strong>Unlimited band members</strong> - invite your whole crew at no extra cost</li>
                    <li><strong>One predictable price</strong> - no surprises, no per-seat charges</li>
                    <li><strong>Better value</strong> - the more members, the more you save</li>
                    <li><strong>Simpler billing</strong> - one annual payment and you're set</li>
                </ul>
            </div>

            <p>Whether you're a duo or a full orchestra, one price covers everyone. It's that simple.</p>

            <p>Log in now to check out the new pricing and keep your band organized with unlimited setlists, songs, and members!</p>

            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="button">
                    Check It Out
                </a>
            </div>

            <p style="margin-top: 1.5rem;">Rock on,<br>
            <strong>The Setlix Team</strong></p>
        </div>

        <div class="footer">
            <p>Questions? Just reply to this email - we'd love to hear from you.</p>
            <p>&copy; {{ date('Y') }} Setlix. All rights reserved.</p>
            <p style="margin-top: 1rem;">
                <a href="{{ $unsubscribeUrl }}" style="color: #9ca3af; text-decoration: underline;">Unsubscribe from marketing emails</a>
            </p>
        </div>
    </div>
</body>
</html>