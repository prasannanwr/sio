<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ env('APP_NAME') }} | Account Removal</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="description" content="How to remove (delete) your {{ env('APP_NAME') }} account and what happens to your data.">
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #222; line-height: 1.6; margin: 0; background: #f7f7f9; }
        .wrap { max-width: 760px; margin: 0 auto; padding: 32px 20px; }
        .card { background: #fff; border-radius: 10px; padding: 32px; }
        h1 { font-size: 24px; margin-top: 0; }
        h2 { font-size: 18px; margin-top: 28px; }
        ol, ul { padding-left: 20px; }
        .muted { color: #666; font-size: 14px; }
        code { background: #f0f1f5; padding: 2px 6px; border-radius: 4px; }
        hr { border: 0; border-top: 1px solid #eee; margin: 28px 0; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1>Account Removal</h1>
            <p>
                You can permanently delete your {{ env('APP_NAME') }} account at any time. This page explains how to
                request account removal and what happens to your data.
            </p>

            <h2>Delete your account from the app</h2>
            <ol>
                <li>Open the {{ env('APP_NAME') }} app and sign in.</li>
                <li>Go to <strong>Profile &rarr; Delete Account</strong>.</li>
                <li>Confirm by entering your account password.</li>
                <li>Your account is deleted immediately and you are signed out.</li>
            </ol>

            <h2>Delete your account by request</h2>
            <p>
                If you cannot access the app, email us at
                <a href="mailto:support@sioshipping.eu">support@sioshipping.eu</a> from the email address on your
                account and ask us to delete it. We will verify your identity and remove your account.
            </p>

            <h2>What is deleted</h2>
            <ul>
                <li>Your profile (name, email, phone) and saved shipping addresses.</li>
                <li>Your login credentials and preferences.</li>
            </ul>

            <h2>What may be retained</h2>
            <p class="muted">
                Certain records (for example completed orders, payment and transaction history) may be retained where
                required for legal, tax, accounting, or fraud-prevention purposes, in line with applicable law. Retained
                records are no longer linked to an active account.
            </p>

            <hr>
            <p class="muted">
                Need help? Contact <a href="mailto:support@sioshipping.eu">support@sioshipping.eu</a>.
                &copy; {{ date('Y') }} {{ env('APP_NAME') }}.
            </p>
        </div>
    </div>
</body>
</html>
