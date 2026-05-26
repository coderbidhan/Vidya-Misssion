<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP — VidyaMission</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6fb; margin: 0; padding: 0; }
        .wrapper { max-width: 520px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #6C63FF, #4A90D9); padding: 32px 24px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 26px; letter-spacing: 0.5px; }
        .header p { color: rgba(255,255,255,0.85); margin: 6px 0 0; font-size: 14px; }
        .body { padding: 32px 28px; }
        .greeting { font-size: 16px; color: #333; margin-bottom: 16px; }
        .otp-box { background: #f0f0ff; border: 2px dashed #6C63FF; border-radius: 10px; text-align: center; padding: 24px; margin: 24px 0; }
        .otp-code { font-size: 42px; font-weight: 700; letter-spacing: 12px; color: #6C63FF; font-family: monospace; }
        .expiry { font-size: 13px; color: #888; margin-top: 8px; }
        .note { font-size: 13px; color: #999; border-top: 1px solid #eee; padding-top: 16px; margin-top: 24px; }
        .footer { background: #f9f9f9; padding: 16px 28px; text-align: center; font-size: 12px; color: #bbb; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>🎓 VidyaMission</h1>
        <p>West Bengal's Student Learning Platform</p>
    </div>
    <div class="body">
        <p class="greeting">Hi {{ $user->name ?? $user->email }},</p>
        <p>Use the OTP below to log in to your VidyaMission account:</p>
        <div class="otp-box">
            <div class="otp-code">{{ $otp }}</div>
            <p class="expiry">⏱ Valid for <strong>{{ $expiryMinutes }} minutes</strong> only</p>
        </div>
        <p>If you did not request this OTP, please ignore this email. Your account remains secure.</p>
        <p class="note">⚠️ Never share this OTP with anyone — VidyaMission staff will never ask for it.</p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} VidyaMission · Helping West Bengal students learn better
    </div>
</div>
</body>
</html>
