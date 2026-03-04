<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SimpleAkunting - Platform Akuntansi Multi-Tenant</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; background: linear-gradient(135deg, #0a1628 0%, #1a2a4a 50%, #0d1b2a 100%); color: #e0e0e0; min-height: 100vh; }
        .container { max-width: 1100px; margin: 0 auto; padding: 0 24px; }
        
        /* Header */
        .header { padding: 24px 0; display: flex; align-items: center; justify-content: space-between; }
        .logo { font-size: 1.5rem; font-weight: 700; color: #ff8c00; }
        .logo span { color: #fff; }
        .nav a { color: #a0b4d0; text-decoration: none; margin-left: 28px; font-size: 0.95rem; transition: color 0.2s; }
        .nav a:hover { color: #ff8c00; }
        
        /* Hero */
        .hero { text-align: center; padding: 80px 0 60px; }
        .hero h1 { font-size: 2.8rem; font-weight: 800; color: #fff; margin-bottom: 20px; line-height: 1.2; }
        .hero h1 em { color: #ff8c00; font-style: normal; }
        .hero p { font-size: 1.15rem; color: #8fa8c8; max-width: 600px; margin: 0 auto 40px; line-height: 1.7; }
        .cta-btn { display: inline-block; padding: 14px 36px; background: linear-gradient(135deg, #ff8c00, #e67600); color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 1rem; transition: transform 0.2s, box-shadow 0.2s; }
        .cta-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255, 140, 0, 0.3); }
        
        /* Features */
        .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; padding: 40px 0 80px; }
        .feature-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 28px; transition: transform 0.2s, border-color 0.2s; }
        .feature-card:hover { transform: translateY(-4px); border-color: rgba(255,140,0,0.3); }
        .feature-card h3 { color: #ff8c00; font-size: 1.1rem; margin-bottom: 10px; }
        .feature-card p { color: #8fa8c8; font-size: 0.95rem; line-height: 1.6; }
        
        /* Footer */
        .footer { text-align: center; padding: 24px 0; border-top: 1px solid rgba(255,255,255,0.06); color: #5a7090; font-size: 0.85rem; }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">Simple<span>Akunting</span></div>
            <nav class="nav">
                <a href="{{ route('central.register-tenant') }}">Daftar Perusahaan</a>
                <a href="{{ route('central.tenants.index') }}">Admin Panel</a>
            </nav>
        </header>

        <section class="hero">
            <h1>Platform Akuntansi <em>Multi-Tenant</em></h1>
            <p>Mengelola akuntansi banyak perusahaan dari satu platform. Setiap perusahaan mendapatkan database terpisah, keamanan tinggi, dan fitur lengkap.</p>
            <a href="{{ route('central.register-tenant') }}" class="cta-btn">Daftarkan Perusahaan Anda →</a>
        </section>

        <section class="features">
            <div class="feature-card">
                <h3>📊 Akuntansi Lengkap</h3>
                <p>Point of Sales, Jurnal, Buku Besar, Neraca, Laba Rugi, Arus Kas, dan laporan keuangan lainnya sesuai SAK.</p>
            </div>
            <div class="feature-card">
                <h3>🔒 Isolasi Data</h3>
                <p>Setiap perusahaan memiliki database sendiri. Data dijamin aman dan terpisah sepenuhnya.</p>
            </div>
            <div class="feature-card">
                <h3>🏪 Multi-Modul</h3>
                <p>Penjualan, Pembelian, Persediaan, Koperasi, Manufacturing, dan Agriculture dalam satu platform.</p>
            </div>
        </section>

        <footer class="footer">
            &copy; {{ date('Y') }} SimpleAkunting dibuat oleh Kurniawan dengan ❤️ untuk membantu bisnis lebih berkembang. All rights reserved.
        </footer>
    </div>
</body>
</html>
