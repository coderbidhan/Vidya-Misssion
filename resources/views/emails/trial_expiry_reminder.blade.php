<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trial Ending Tomorrow — VidyaMission</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6fb; margin: 0; padding: 0; }
        .wrapper { max-width: 520px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #f59e0b, #d97706); padding: 32px 24px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 24px; }
        .header p { color: rgba(255,255,255,0.85); margin: 6px 0 0; font-size: 14px; }
        .body { padding: 32px 28px; color: #333; line-height: 1.7; }
        .highlight { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px 16px; border-radius: 6px; margin: 16px 0; font-size: 14px; }
        .cta { text-align: center; margin: 28px 0; }
        .btn { background: #f59e0b; color: #fff; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-size: 16px; font-weight: 700; display: inline-block; }
        .footer { background: #f9f9f9; padding: 16px 28px; text-align: center; font-size: 12px; color: #bbb; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>⚡ Your Free Trial Ends Tomorrow!</h1>
        <p>VidyaMission</p>
    </div>
    <div class="body">
        <p>Hi {{ $user->name ?? $user->email }},</p>
        <p>Your 3-day free trial on <strong>VidyaMission</strong> expires <strong>tomorrow</strong>. Don't let your learning journey stop here!</p>
        <div class="highlight">
            🎓 Continue accessing all subjects, live classes, notes, and tests with an affordable subscription plan.
        </div>
        <p>Our plans start at just <strong>₹199/month</strong> — less than the cost of a textbook!</p>
        <div class="cta">
            <a href="{{ config('app.url') }}/subscribe" class="btn">Subscribe Now →</a>
        </div>
        <p style="font-size:13px;color:#888;">Have questions? Reply to this email and we'll help you out.</p>
    </div>
    <div class="footer">&copy; {{ date('Y') }} VidyaMission</div>
</div>
</body>
</html>
