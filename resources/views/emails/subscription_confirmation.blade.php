<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subscription Confirmed — VidyaMission</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6fb; margin: 0; padding: 0; }
        .wrapper { max-width: 520px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #22c55e, #16a34a); padding: 32px 24px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 26px; }
        .header p { color: rgba(255,255,255,0.85); margin: 6px 0 0; font-size: 14px; }
        .body { padding: 32px 28px; color: #333; }
        .card { background: #f0fdf4; border: 1px solid #86efac; border-radius: 10px; padding: 20px 24px; margin: 20px 0; }
        .card-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #d1fae5; font-size: 14px; }
        .card-row:last-child { border-bottom: none; }
        .label { color: #666; }
        .value { font-weight: 600; color: #333; }
        .cta { text-align: center; margin: 24px 0; }
        .btn { background: #22c55e; color: #fff; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-size: 15px; font-weight: 600; display: inline-block; }
        .footer { background: #f9f9f9; padding: 16px 28px; text-align: center; font-size: 12px; color: #bbb; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>✅ Subscription Confirmed!</h1>
        <p>VidyaMission</p>
    </div>
    <div class="body">
        <p>Hi {{ $user->name ?? $user->email }},</p>
        <p>Your subscription to <strong>{{ $subscription->name }}</strong> has been activated successfully. Happy learning!</p>
        <div class="card">
            <div class="card-row"><span class="label">Plan</span><span class="value">{{ $subscription->name }}</span></div>
            <div class="card-row"><span class="label">Amount Paid</span><span class="value">₹{{ number_format($subscription->price, 2) }}</span></div>
            <div class="card-row"><span class="label">Start Date</span><span class="value">{{ $userSubscription->starts_at->format('d M Y') }}</span></div>
            <div class="card-row"><span class="label">Valid Until</span><span class="value">{{ $userSubscription->ends_at->format('d M Y') }}</span></div>
        </div>
        <div class="cta">
            <a href="{{ config('app.url') }}" class="btn">Start Learning Now →</a>
        </div>
    </div>
    <div class="footer">&copy; {{ date('Y') }} VidyaMission</div>
</div>
</body>
</html>
