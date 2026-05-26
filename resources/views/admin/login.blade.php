<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — VidyaMission</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@700;800;900&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Inter', sans-serif;
    background: #F4F6FF;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    /* subtle grid pattern */
    background-image:
        linear-gradient(rgba(109,74,255,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(109,74,255,0.04) 1px, transparent 1px);
    background-size: 36px 36px;
}

/* Decorative blobs */
.blob {
    position: fixed; border-radius: 50%; pointer-events: none;
    filter: blur(70px); opacity: 0.45; z-index: 0;
}
.blob-1 { width: 420px; height: 420px; top: -120px; right: -80px; background: radial-gradient(circle, #C4B5FD, transparent 70%); }
.blob-2 { width: 300px; height: 300px; bottom: -80px; left: -60px; background: radial-gradient(circle, #BAE6FD, transparent 70%); }

.login-wrap {
    position: relative; z-index: 1;
    width: 100%; max-width: 420px;
}

/* Brand header */
.brand {
    text-align: center; margin-bottom: 2rem;
}
.brand-icon {
    width: 52px; height: 52px; border-radius: 14px;
    background: linear-gradient(135deg, #6D4AFF, #8B6BFF);
    display: inline-flex; align-items: center; justify-content: center;
    font-family: 'Poppins', sans-serif; font-size: 1.4rem; font-weight: 900; color: #fff;
    box-shadow: 0 8px 24px rgba(109,74,255,0.35);
    margin-bottom: 0.875rem;
}
.brand-name {
    font-family: 'Poppins', sans-serif;
    font-size: 1.5rem; font-weight: 900; color: #111827;
}
.brand-name span { color: #6D4AFF; }
.brand-tag { font-size: 0.8rem; color: #6B7280; margin-top: 0.2rem; }

/* Card */
.login-card {
    background: #FFFFFF;
    border: 1px solid rgba(0,0,0,0.07);
    border-radius: 20px;
    padding: 2.25rem 2rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(109,74,255,0.06);
}

.card-title {
    font-size: 1.1rem; font-weight: 800; color: #111827;
    margin-bottom: 0.35rem;
}
.card-sub {
    font-size: 0.82rem; color: #6B7280; margin-bottom: 1.75rem;
}

.form-group { margin-bottom: 1.125rem; }
.form-label {
    display: block; font-size: 0.73rem; font-weight: 700;
    color: #374151; margin-bottom: 0.4rem;
    text-transform: uppercase; letter-spacing: 0.05em;
}
.form-input {
    width: 100%; padding: 0.7rem 0.875rem;
    background: #F9FAFB;
    border: 1.5px solid rgba(0,0,0,0.1);
    border-radius: 10px; color: #111827;
    font-size: 0.9rem; font-family: 'Inter', sans-serif;
    outline: none; transition: all 0.2s;
}
.form-input:focus {
    border-color: #6D4AFF;
    background: #FFFFFF;
    box-shadow: 0 0 0 3px rgba(109,74,255,0.12);
}
.form-input::placeholder { color: #9CA3AF; }

.btn-login {
    width: 100%; padding: 0.85rem;
    background: #6D4AFF;
    color: #fff; font-size: 0.95rem; font-weight: 700;
    border: none; border-radius: 12px;
    cursor: pointer; transition: all 0.25s;
    font-family: 'Inter', sans-serif;
    margin-top: 0.25rem;
    box-shadow: 0 4px 16px rgba(109,74,255,0.3);
    display: flex; align-items: center; justify-content: center; gap: 0.5rem;
}
.btn-login:hover {
    background: #5536D4;
    box-shadow: 0 8px 28px rgba(109,74,255,0.45);
    transform: translateY(-1px);
}
.btn-login:active { transform: none; }

/* Error / success alerts */
.alert {
    padding: 0.75rem 1rem; border-radius: 10px; font-size: 0.85rem;
    margin-bottom: 1.25rem; display: flex; align-items: flex-start; gap: 0.5rem;
}
.alert-error   { background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; }
.alert-success { background: #F0FDF4; border: 1px solid #A7F3D0; color: #065F46; }

.back-link {
    display: block; text-align: center; margin-top: 1.5rem;
    font-size: 0.82rem; color: #6B7280; text-decoration: none;
    transition: color 0.2s;
}
.back-link:hover { color: #6D4AFF; }
.back-link span { color: #6D4AFF; font-weight: 600; }
</style>
</head>
<body>

<div class="blob blob-1"></div>
<div class="blob blob-2"></div>

<div class="login-wrap">
    <!-- Brand -->
    <div class="brand">
        <div class="brand-icon">V</div>
        <div class="brand-name">Vidya<span>Mission</span></div>
        <div class="brand-tag">Admin Control Panel</div>
    </div>

    <!-- Card -->
    <div class="login-card">
        <div class="card-title">Welcome back 👋</div>
        <div class="card-sub">Sign in to your admin account to continue.</div>

        @if (session('error'))
            <div class="alert alert-error">⚠️ {{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">✓ {{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input
                    type="email" id="email" name="email"
                    class="form-input"
                    placeholder="admin@vidyamission.com"
                    value="{{ old('email') }}"
                    required autofocus autocomplete="email"
                >
                @error('email')
                    <div style="font-size:0.75rem;color:#DC2626;margin-top:0.35rem">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input
                    type="password" id="password" name="password"
                    class="form-input"
                    placeholder="Enter your password"
                    required autocomplete="current-password"
                >
                @error('password')
                    <div style="font-size:0.75rem;color:#DC2626;margin-top:0.35rem">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-login">
                🔐 Sign In to Admin Panel
            </button>
        </form>
    </div>

    <a href="{{ route('home') }}" class="back-link">← Back to <span>VidyaMission</span></a>
</div>

</body>
</html>
