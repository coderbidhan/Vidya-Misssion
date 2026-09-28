<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — Vidya Mission</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Bengali:wght@600;700;800&family=Poppins:wght@700;800;900&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Inter', sans-serif;
    background: #0B0F19;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    position: relative;
    overflow-x: hidden;
}

/* Background animated gradient glow */
.bg-glow {
    position: fixed; border-radius: 50%; pointer-events: none;
    filter: blur(120px); opacity: 0.35; z-index: 0;
}
.glow-1 { width: 480px; height: 480px; top: -100px; right: -50px; background: radial-gradient(circle, #5B45FF, transparent 70%); }
.glow-2 { width: 400px; height: 400px; bottom: -100px; left: -50px; background: radial-gradient(circle, #06B6D4, transparent 70%); }

.login-wrap {
    position: relative; z-index: 1;
    width: 100%; max-width: 440px;
}

/* Brand header */
.brand {
    text-align: center; margin-bottom: 2rem;
}
.brand-icon {
    width: 58px; height: 58px; border-radius: 16px;
    background: linear-gradient(135deg, #5B45FF 0%, #06B6D4 100%);
    display: inline-flex; align-items: center; justify-content: center;
    font-family: 'Poppins', sans-serif; font-size: 1.6rem; font-weight: 900; color: #fff;
    box-shadow: 0 12px 32px rgba(91,69,255,0.45);
    margin-bottom: 1rem;
}
.brand-name {
    font-family: 'Poppins', sans-serif;
    font-size: 1.65rem; font-weight: 900; color: #FFFFFF;
    letter-spacing: -0.02em;
}
.brand-name span { color: #7F6BFF; }
.brand-tag { font-size: 0.85rem; color: #94A3B8; margin-top: 0.25rem; font-family: 'Noto Sans Bengali', sans-serif; }

/* Card */
.login-card {
    background: rgba(18, 24, 38, 0.85);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 24px;
    padding: 2.5rem 2.25rem;
    box-shadow: 0 24px 64px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.05);
}

.card-title {
    font-size: 1.25rem; font-weight: 800; color: #FFFFFF;
    margin-bottom: 0.35rem;
}
.card-sub {
    font-size: 0.85rem; color: #94A3B8; margin-bottom: 1.75rem;
}

.form-group { margin-bottom: 1.25rem; }
.form-label {
    display: block; font-size: 0.75rem; font-weight: 700;
    color: #CBD5E1; margin-bottom: 0.5rem;
    text-transform: uppercase; letter-spacing: 0.06em;
}
.form-input {
    width: 100%; padding: 0.78rem 1rem;
    background: rgba(30, 41, 59, 0.7);
    border: 1.5px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px; color: #FFFFFF;
    font-size: 0.92rem; font-family: 'Inter', sans-serif;
    outline: none; transition: all 0.2s;
}
.form-input:focus {
    border-color: #7F6BFF;
    background: rgba(30, 41, 59, 1);
    box-shadow: 0 0 0 3px rgba(91,69,255,0.25);
}
.form-input::placeholder { color: #64748B; }

.btn-login {
    width: 100%; padding: 0.9rem;
    background: linear-gradient(135deg, #5B45FF, #7F6BFF);
    color: #fff; font-size: 0.95rem; font-weight: 700;
    border: none; border-radius: 12px;
    cursor: pointer; transition: all 0.25s;
    font-family: 'Inter', sans-serif;
    margin-top: 0.5rem;
    box-shadow: 0 4px 20px rgba(91,69,255,0.4);
    display: flex; align-items: center; justify-content: center; gap: 0.5rem;
}
.btn-login:hover {
    background: linear-gradient(135deg, #4F38F8, #6D56F5);
    box-shadow: 0 8px 30px rgba(91,69,255,0.6);
    transform: translateY(-1px);
}
.btn-login:active { transform: none; }

/* Alerts */
.alert {
    padding: 0.8rem 1.1rem; border-radius: 12px; font-size: 0.85rem;
    margin-bottom: 1.25rem; display: flex; align-items: flex-start; gap: 0.5rem;
}
.alert-error   { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #FCA5A5; }
.alert-success { background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #6EE7B7; }

.back-link {
    display: block; text-align: center; margin-top: 1.75rem;
    font-size: 0.85rem; color: #94A3B8; text-decoration: none;
    transition: color 0.2s;
}
.back-link:hover { color: #7F6BFF; }
.back-link span { color: #7F6BFF; font-weight: 600; }
</style>
</head>
<body>

<div class="bg-glow glow-1"></div>
<div class="bg-glow glow-2"></div>

<div class="login-wrap">
    <!-- Brand -->
    <div class="brand">
        <div class="brand-icon">V</div>
        <div class="brand-name">Vidya<span>Mission</span></div>
        <div class="brand-tag">অ্যাডমিন কন্ট্রোল প্যানেল • Admin Panel</div>
    </div>

    <!-- Card -->
    <div class="login-card">
        <div class="card-title">Welcome Back 👋</div>
        <div class="card-sub">Sign in to your administrative account to continue.</div>

        @if (session('error'))
            <div class="alert alert-error">⚠️ {{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">✓ {{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Admin Email Address</label>
                <input
                    type="email" id="email" name="email"
                    class="form-input"
                    placeholder="admin@vidyamission.in"
                    value="{{ old('email') }}"
                    required autofocus autocomplete="email"
                >
                @error('email')
                    <div style="font-size:0.75rem;color:#F87171;margin-top:0.35rem">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input
                    type="password" id="password" name="password"
                    class="form-input"
                    placeholder="••••••••••••"
                    required autocomplete="current-password"
                >
                @error('password')
                    <div style="font-size:0.75rem;color:#F87171;margin-top:0.35rem">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-login">
                🔐 Sign In to Dashboard
            </button>
        </form>
    </div>

    <a href="{{ route('home') }}" class="back-link">← Return to <span>VidyaMission Home</span></a>
</div>

</body>
</html>
