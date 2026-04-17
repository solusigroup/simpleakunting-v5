<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Admin Login - Simple Akunting v5</title>

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SimpleAkunting">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">
    <meta name="theme-color" content="#ff8c00">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; background: linear-gradient(135deg, #0a1628 0%, #1a2a4a 50%, #0d1b2a 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }

        .login-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; width: 100%; max-width: 420px; padding: 40px 36px; backdrop-filter: blur(10px); }

        .login-header { text-align: center; margin-bottom: 32px; }
        .login-icon { width: 56px; height: 56px; background: linear-gradient(135deg, #ff8c00, #e67600); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
        .login-icon svg { color: #fff; }
        .login-title { color: #fff; font-size: 1.4rem; font-weight: 700; margin-bottom: 6px; }
        .login-subtitle { color: #8fa8c8; font-size: 0.85rem; }

        .alert-error { background: rgba(220,53,69,0.15); border: 1px solid rgba(220,53,69,0.3); color: #ff6b6b; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-size: 0.85rem; }
        .alert-error ul { margin: 0; padding-left: 18px; }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; color: #a0b4d0; font-size: 0.8rem; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .input-wrap { position: relative; }
        .input-wrap svg { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #5a7090; width: 18px; height: 18px; }
        .form-input { width: 100%; padding: 12px 14px 12px 44px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 10px; color: #e0e0e0; font-size: 0.95rem; font-family: inherit; outline: none; transition: border-color 0.2s, box-shadow 0.2s; }
        .form-input:focus { border-color: #ff8c00; box-shadow: 0 0 0 3px rgba(255,140,0,0.15); }
        .form-input::placeholder { color: #5a7090; }

        .btn-login { width: 100%; padding: 14px; background: linear-gradient(135deg, #ff8c00, #e67600); color: #fff; border: none; border-radius: 10px; font-size: 1rem; font-weight: 600; font-family: inherit; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 8px; }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,140,0,0.3); }
        .btn-login:active { transform: translateY(0); }
        .btn-login svg { width: 18px; height: 18px; }

        .login-footer { text-align: center; margin-top: 28px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.06); color: #5a7090; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="login-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                    <path d="M2 17l10 5 10-5"></path>
                    <path d="M2 12l10 5 10-5"></path>
                </svg>
            </div>
            <h1 class="login-title">Central Admin Login</h1>
            <p class="login-subtitle">Autentikasi khusus Super Administrator</p>
        </div>

        @if ($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('central.login') }}">
            @csrf
            <div class="form-group">
                <label for="nama_user" class="form-label">Username</label>
                <div class="input-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <input type="text" class="form-input" id="nama_user" name="nama_user"
                        value="{{ old('nama_user') }}" placeholder="Masukkan username" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <input type="password" class="form-input" id="password" name="password"
                        placeholder="Masukkan password" required>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <span>Masuk ke Central</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </button>
        </form>

        <div class="login-footer">
            🔒 Area ini hanya untuk Administrator sistem<br>
            &copy; {{ date('Y') }} Simple Akunting v5
        </div>
    </div>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registered'))
                    .catch(err => console.error('Service Worker registration failed', err));
            });
        }
    </script>
</body>
</html>
