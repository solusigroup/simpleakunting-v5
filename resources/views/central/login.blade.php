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
        .input-wrap > svg { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #5a7090; width: 18px; height: 18px; pointer-events: none; }
        .form-input { width: 100%; padding: 12px 14px 12px 44px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 10px; color: #e0e0e0; font-size: 0.95rem; font-family: inherit; outline: none; transition: border-color 0.2s, box-shadow 0.2s; }
        .form-input:focus { border-color: #ff8c00; box-shadow: 0 0 0 3px rgba(255,140,0,0.15); }
        .form-input::placeholder { color: #5a7090; }

        .btn-toggle-password { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 4px; color: #5a7090; display: flex; align-items: center; justify-content: center; z-index: 5; border-radius: 6px; transition: color 0.2s, background 0.2s; }
        .btn-toggle-password:hover { color: #ff8c00; background: rgba(255,140,0,0.08); }
        .btn-toggle-password svg { width: 18px; height: 18px; position: static; transform: none; }

        .login-actions { display: flex; justify-content: flex-end; align-items: center; margin-top: -10px; margin-bottom: 20px; }
        .forgot-link { color: #8fa8c8; font-size: 0.85rem; text-decoration: none; transition: color 0.2s; display: inline-flex; align-items: center; gap: 6px; }
        .forgot-link:hover { color: #ff8c00; text-decoration: underline; }

        .btn-login { width: 100%; padding: 14px; background: linear-gradient(135deg, #ff8c00, #e67600); color: #fff; border: none; border-radius: 10px; font-size: 1rem; font-weight: 600; font-family: inherit; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 8px; }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,140,0,0.3); }
        .btn-login:active { transform: translateY(0); }
        .btn-login svg { width: 18px; height: 18px; }

        .login-footer { text-align: center; margin-top: 28px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.06); color: #5a7090; font-size: 0.8rem; }

        /* Modal styling */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index: 1000; align-items: center; justify-content: center; padding: 20px; animation: fadeIn 0.2s ease; }
        .modal-overlay.active { display: flex; }
        .modal-card { background: #0f1c2e; border: 1px solid rgba(255,255,255,0.12); border-radius: 16px; width: 100%; max-width: 440px; padding: 28px; box-shadow: 0 20px 50px rgba(0,0,0,0.6); position: relative; }
        .modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .modal-title { color: #fff; font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .btn-modal-close { background: none; border: none; color: #8fa8c8; cursor: pointer; font-size: 1.5rem; padding: 4px 8px; border-radius: 6px; line-height: 1; transition: color 0.2s, background 0.2s; }
        .btn-modal-close:hover { color: #fff; background: rgba(255,255,255,0.08); }
        .modal-body { color: #c0d0e0; font-size: 0.9rem; line-height: 1.6; margin-bottom: 24px; }
        .modal-info-box { background: rgba(255,140,0,0.08); border-left: 3px solid #ff8c00; padding: 12px 14px; border-radius: 6px; margin-top: 14px; color: #e0e8f0; font-size: 0.85rem; }
        .modal-actions { display: flex; flex-direction: column; gap: 10px; }
        .btn-whatsapp { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 16px; background: #25D366; color: #fff; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 0.95rem; transition: transform 0.2s, box-shadow 0.2s; }
        .btn-whatsapp:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37,211,102,0.35); color: #fff; }
        .btn-dismiss { padding: 10px 16px; background: rgba(255,255,255,0.08); color: #8fa8c8; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; font-weight: 500; font-size: 0.9rem; cursor: pointer; text-align: center; transition: background 0.2s, color 0.2s; }
        .btn-dismiss:hover { background: rgba(255,255,255,0.12); color: #fff; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
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
                        placeholder="Masukkan password" required style="padding-right: 44px;">
                    <button type="button" class="btn-toggle-password" onclick="togglePassword('password', this)" title="Tampilkan/Sembunyikan Password">
                        <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    </button>
                </div>
            </div>

            <div class="login-actions">
                <a href="javascript:void(0)" onclick="openForgotModal()" class="forgot-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="8" cy="15" r="4"></circle>
                        <path d="M10.85 12.15L19 4"></path>
                        <path d="M18 5l2 2"></path>
                        <path d="M15 8l2 2"></path>
                    </svg>
                    <span>Lupa Password?</span>
                </a>
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

    <!-- Modal Lupa Password -->
    <div id="forgotPasswordModal" class="modal-overlay" onclick="handleBackdropClick(event)">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ff8c00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="8" cy="15" r="4"></circle>
                        <path d="M10.85 12.15L19 4"></path>
                        <path d="M18 5l2 2"></path>
                        <path d="M15 8l2 2"></path>
                    </svg>
                    Bantuan Reset Password
                </h3>
                <button type="button" class="btn-modal-close" onclick="closeForgotModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Untuk mereset password akun <strong>Central Admin / Superuser</strong>, silakan hubungi tim Administrator melalui WhatsApp dengan menyertakan username Anda.</p>
                <div class="modal-info-box">
                    💡 <strong>Pengguna Tenant/Bisnis:</strong> Silakan login melalui subdomain bisnis Anda (misal: <em>namabisnis.simpleakunting.id</em>) dan hubungi Admin bisnis Anda untuk reset password.
                </div>
            </div>
            <div class="modal-actions">
                <a href="https://wa.me/6282141643495?text=Halo%20Admin%20Simple%20Akunting,%20saya%20membutuhkan%20bantuan%20reset%20password%20akun%20Central%20Admin." target="_blank" class="btn-whatsapp">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    Hubungi WhatsApp Support
                </a>
                <button type="button" class="btn-dismiss" onclick="closeForgotModal()">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const eyeIcon = btn.querySelector('.icon-eye');
            const eyeOffIcon = btn.querySelector('.icon-eye-off');

            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = '';
                btn.style.color = '#ff8c00';
            } else {
                input.type = 'password';
                eyeIcon.style.display = '';
                eyeOffIcon.style.display = 'none';
                btn.style.color = '';
            }
        }

        function openForgotModal() {
            document.getElementById('forgotPasswordModal').classList.add('active');
        }

        function closeForgotModal() {
            document.getElementById('forgotPasswordModal').classList.remove('active');
        }

        function handleBackdropClick(e) {
            if (e.target.id === 'forgotPasswordModal') {
                closeForgotModal();
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeForgotModal();
            }
        });

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
