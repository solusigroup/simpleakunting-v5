<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Simple Akunting v5</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; min-height: 100vh; display: flex; }

        /* Split Layout */
        .split { display: flex; width: 100%; min-height: 100vh; }

        /* Left Hero */
        .hero { flex: 1; background: linear-gradient(135deg, #1e3a5f 0%, #0a1628 50%, #1a2a4a 100%); display: flex; flex-direction: column; justify-content: center; padding: 60px 48px; position: relative; overflow: hidden; }
        .hero::before { content: ''; position: absolute; top: -50%; right: -30%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(255,140,0,0.08) 0%, transparent 70%); border-radius: 50%; }
        .hero-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; }
        .hero-brand-icon { width: 44px; height: 44px; background: rgba(255,140,0,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #ff8c00; }
        .hero-brand-text { color: #ff8c00; font-size: 1.1rem; font-weight: 700; }
        .hero h1 { color: #fff; font-size: 2.2rem; font-weight: 800; line-height: 1.2; margin-bottom: 16px; }
        .hero h1 span { color: #ff8c00; }
        .hero p { color: #8fa8c8; font-size: 1rem; line-height: 1.7; margin-bottom: 32px; max-width: 400px; }
        .hero-badges { display: flex; flex-wrap: wrap; gap: 10px; }
        .hero-badge { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 24px; color: #c0d0e0; font-size: 0.85rem; }
        .hero-footer { position: absolute; bottom: 24px; left: 48px; color: #4a6080; font-size: 0.8rem; }
        .hero-footer a { color: #5a8ab5; text-decoration: none; }

        /* Right Form */
        .form-side { flex: 0 0 460px; background: #fff; display: flex; align-items: center; justify-content: center; padding: 40px; }
        .form-container { width: 100%; max-width: 360px; }
        .form-header { margin-bottom: 32px; }
        .form-title { color: #1a1a2e; font-size: 1.5rem; font-weight: 700; margin-bottom: 6px; }
        .form-subtitle { color: #6b7280; font-size: 0.9rem; }

        .alert-error { background: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; padding: 12px 16px; border-radius: 10px; margin-bottom: 24px; font-size: 0.85rem; }
        .alert-error ul { margin: 0; padding-left: 18px; }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; color: #374151; font-size: 0.8rem; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .input-wrap { position: relative; }
        .input-wrap svg { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; width: 18px; height: 18px; }
        .form-input { width: 100%; padding: 12px 14px 12px 44px; background: #f9fafb; border: 1.5px solid #e5e7eb; border-radius: 10px; color: #1a1a2e; font-size: 0.95rem; font-family: inherit; outline: none; transition: border-color 0.2s, box-shadow 0.2s; }
        .form-input:focus { border-color: #ff8c00; box-shadow: 0 0 0 3px rgba(255,140,0,0.1); background: #fff; }
        .form-input::placeholder { color: #9ca3af; }

        .btn-login { width: 100%; padding: 14px; background: linear-gradient(135deg, #ff8c00, #e67600); color: #fff; border: none; border-radius: 10px; font-size: 1rem; font-weight: 600; font-family: inherit; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 8px; }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,140,0,0.3); }
        .btn-login:active { transform: translateY(0); }
        .btn-login svg { width: 18px; height: 18px; }

        .form-footer { text-align: center; margin-top: 24px; }
        .form-footer a { color: #ff8c00; text-decoration: none; font-weight: 500; font-size: 0.9rem; }
        .form-footer a:hover { text-decoration: underline; }

        /* Responsive */
        @media (max-width: 900px) {
            .split { flex-direction: column; }
            .hero { flex: none; padding: 32px 24px; min-height: auto; }
            .hero h1 { font-size: 1.6rem; }
            .hero p { margin-bottom: 20px; }
            .hero-footer { position: static; margin-top: 24px; }
            .form-side { flex: 1; padding: 32px 24px; }
        }
    </style>
</head>
<body>
    <div class="split">
        <!-- LEFT - Hero -->
        <div class="hero">
            <div class="hero-brand">
                <div class="hero-brand-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                </div>
                <span class="hero-brand-text">Simple Akunting</span>
            </div>

            <h1>Kelola Keuangan<br><span>Lebih Mudah</span></h1>
            <p>Solusi akuntansi modern untuk bisnis Anda. Pantau keuangan, buat laporan, dan kelola transaksi dengan mudah.</p>

            <div class="hero-badges">
                <div class="hero-badge">📊 Laporan Real-time</div>
                <div class="hero-badge">🔒 Data Aman</div>
                <div class="hero-badge">⚡ Cepat & Mudah</div>
            </div>

            <div class="hero-footer">
                &copy; {{ date('Y') }} Simple Akunting v5. Made by <a href="https://simpleakunting.id" target="_blank">Kurniawan</a>
            </div>
        </div>

        <!-- RIGHT - Login Form -->
        <div class="form-side">
            <div class="form-container">
                <div class="form-header">
                    <h2 class="form-title">Selamat Datang</h2>
                    <p class="form-subtitle">Masuk ke akun Anda untuk melanjutkan</p>
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

                <form method="POST" action="{{ route('login') }}">
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
                        <span>Masuk</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </form>

                <div class="form-footer">
                    <p>
                        <a href="#" onclick="alert('Hubungi Admin atau Superuser untuk mereset password Anda.\n\nAdmin dapat mereset melalui menu:\nAdmin → Manajemen User → 🔑 Reset'); return false;">
                            🔑 Lupa Password?
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>