<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Live Class Starting Soon — VidyaMission</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6fb; margin: 0; padding: 0; }
        .wrapper { max-width: 520px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #6C63FF, #3b82f6); padding: 32px 24px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .header p { color: rgba(255,255,255,0.85); margin: 6px 0 0; }
        .body { padding: 32px 28px; color: #333; }
        .card { background: #eff6ff; border: 1px solid #93c5fd; border-radius: 10px; padding: 20px; margin: 20px 0; }
        .card-title { font-size: 18px; font-weight: 700; color: #1d4ed8; margin-bottom: 12px; }
        .card-row { font-size: 14px; color: #555; padding: 4px 0; }
        .card-row strong { color: #333; }
        .cta { text-align: center; margin: 24px 0; }
        .btn { background: #6C63FF; color: #fff; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-size: 15px; font-weight: 600; display: inline-block; }
        .footer { background: #f9f9f9; padding: 16px 28px; text-align: center; font-size: 12px; color: #bbb; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>📡 Live Class in 30 Minutes!</h1>
        <p>VidyaMission</p>
    </div>
    <div class="body">
        <p>Hi {{ $user->name ?? $user->email }},</p>
        <p>Your upcoming live class is starting very soon. Get ready!</p>
        <div class="card">
            <div class="card-title">{{ $liveClass->title }}</div>
            <div class="card-row">📅 <strong>Time:</strong> {{ $liveClass->scheduled_at->format('d M Y, h:i A') }}</div>
            <div class="card-row">⏱ <strong>Duration:</strong> {{ $liveClass->duration_minutes }} minutes</div>
            <div class="card-row">📡 <strong>Platform:</strong> {{ ucfirst(str_replace('_', ' ', $liveClass->platform)) }}</div>
            @if($liveClass->teacher)
            <div class="card-row">👨‍🏫 <strong>Teacher:</strong> {{ $liveClass->teacher->name }}</div>
            @endif
        </div>
        <div class="cta">
            <a href="{{ $liveClass->meeting_url }}" class="btn">Join Class Now →</a>
        </div>
        <p style="font-size:12px;color:#999;">Make sure your internet connection is stable before joining.</p>
    </div>
    <div class="footer">&copy; {{ date('Y') }} VidyaMission</div>
</div>
</body>
</html>
