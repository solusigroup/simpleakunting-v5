<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Simple Akunting v5</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>

<body class="login-body">
    <div class="login-split">
        <!-- LEFT SIDE - Hero Section -->
        <div class="login-hero">
            <div class="login-hero-content">
                <div class="login-brand">
                    <div class="login-brand-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                            <path d="M2 17l10 5 10-5"></path>
                            <path d="M2 12l10 5 10-5"></path>
                        </svg>
                    </div>
                    <span class="login-brand-text">Simple Akunting</span>
                </div>

                <h1 class="login-hero-title">
                    Kelola Keuangan<br>
                    <span class="text-gradient">Lebih Mudah</span>
                </h1>

                <p class="login-hero-subtitle">
                    Solusi akuntansi modern untuk bisnis Anda.
                    Pantau keuangan, buat laporan, dan kelola transaksi dengan mudah.
                </p>

                <div class="login-features">
                    <div class="login-feature-badge">
                        <span class="feature-icon">📊</span>
                        <span>Laporan Real-time</span>
                    </div>
                    <div class="login-feature-badge">
                        <span class="feature-icon">🔒</span>
                        <span>Data Aman</span>
                    </div>
                    <div class="login-feature-badge">
                        <span class="feature-icon">⚡</span>
                        <span>Cepat & Mudah</span>
                    </div>
                </div>
            </div>

            <div class="login-hero-footer">
                <p>© 2026 Simple Akunting v5. Made by <a href="https://simpleakunting.id"
                        target="_blank">Kurniawan</a></p>
            </div>
        </div>

        <!-- RIGHT SIDE - Login Form -->
        <div class="login-form-side">
            <div class="login-form-container">
                <div class="login-form-header">
                    <h2 class="login-form-title">Selamat Datang</h2>
                    <p class="login-form-subtitle">Masuk ke akun Anda untuk melanjutkan</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="login-form">
                    @csrf
                    <div class="form-group">
                        <label for="nama_user" class="form-label">Username</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </span>
                            <input type="text" class="form-control form-control-icon" id="nama_user" name="nama_user"
                                value="{{ old('nama_user') }}" placeholder="Masukkan username" required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </span>
                            <input type="password" class="form-control form-control-icon" id="password" name="password"
                                placeholder="Masukkan password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <span>Masuk</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </form>

                <div class="login-form-footer">
                    <p style="margin-bottom: 8px;">
                        <a href="#" onclick="alert('Hubungi Admin atau Superuser untuk mereset password Anda.\n\nAdmin dapat mereset melalui menu:\nAdmin → Manajemen User → 🔑 Reset'); return false;" style="color: #8b5cf6; font-weight: 500;">
                            🔑 Lupa Password?
                        </a>
                    </p>
                    @if(Route::has('register'))
                    <p>Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>

</html>