<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribed - Setlix</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
        }
        .card {
            background: white;
            border-radius: 0.5rem;
            padding: 2rem;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #1a1a1a;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        p {
            color: #6b7280;
            line-height: 1.6;
        }
        .checkmark {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="checkmark">✓</div>
        <h1>You've been unsubscribed</h1>
        <p>Hey {{ $userName }}, you won't receive any more marketing emails from Setlix.</p>
        <p style="margin-top: 1rem; font-size: 0.875rem;">Changed your mind? Just log in to update your preferences.</p>
    </div>
</body>
</html>
