<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f7; font-family: Arial, Helvetica, sans-serif; color:#333;">
    <div style="max-width:520px; margin:0 auto; padding:32px 24px;">
        <div style="background:#ffffff; border-radius:8px; padding:32px;">
            <h2 style="margin-top:0; color:#111;">
                {{ $purpose === \App\Models\AuthOtp::PURPOSE_PASSWORD_RESET ? 'Password Reset Code' : 'Verify Your Email' }}
            </h2>

            <p style="font-size:15px; line-height:1.5;">
                Hello{{ $name ? ' ' . $name : '' }},
            </p>

            <p style="font-size:15px; line-height:1.5;">
                Use the verification code below to
                {{ $purpose === \App\Models\AuthOtp::PURPOSE_PASSWORD_RESET ? 'reset your password' : 'complete your registration' }}:
            </p>

            <div style="text-align:center; margin:28px 0;">
                <span style="display:inline-block; font-size:32px; font-weight:bold; letter-spacing:8px; color:#111; background:#f0f1f5; padding:14px 24px; border-radius:6px;">
                    {{ $code }}
                </span>
            </div>

            <p style="font-size:14px; line-height:1.5; color:#555;">
                This code is valid for <strong>{{ $ttlMinutes }} minutes</strong>. For your security, do not share it with anyone.
            </p>

            <p style="font-size:14px; line-height:1.5; color:#555;">
                If you did not request this, you can safely ignore this email.
            </p>

            <hr style="border:none; border-top:1px solid #eee; margin:28px 0;">

            <p style="font-size:12px; color:#999; margin:0;">
                &copy; {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
