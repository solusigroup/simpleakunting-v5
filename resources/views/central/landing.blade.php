<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SimpleAkunting - Platform Akuntansi Multi-Tenant</title>

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SimpleAkunting">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">
    <meta name="theme-color" content="#ff8c00">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #0a1628 0%, #1a2a4a 50%, #0d1b2a 100%);
            color: #e0e0e0;
            min-height: 100vh;
            scroll-behavior: smooth;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Header & Nav */
        .header {
            padding: 24px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 100;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ff8c00;
            text-decoration: none;
        }

        .logo span {
            color: #fff;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .nav-link {
            color: #8fa8c8;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: #ff8c00;
        }

        .nav-btn {
            background: #ff8c00;
            color: #fff;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 20px;
            transition: background 0.2s, transform 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .nav-btn:hover {
            background: #e07b00;
            transform: translateY(-1px);
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Hero */
        .hero {
            text-align: center;
            padding: 80px 0 60px;
        }

        .hero h1 {
            font-size: 2.8rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero h1 em {
            color: #ff8c00;
            font-style: normal;
        }

        .hero p {
            font-size: 1.15rem;
            color: #8fa8c8;
            max-width: 600px;
            margin: 0 auto 40px;
            line-height: 1.7;
        }

        /* Features */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            padding: 40px 0 60px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 28px;
            transition: transform 0.2s, border-color 0.2s;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            border-color: rgba(255, 140, 0, 0.3);
        }

        .feature-card h3 {
            color: #ff8c00;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .feature-card p {
            color: #8fa8c8;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Profile Section */
        .profile-section {
            padding: 80px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .section-title {
            text-align: center;
            font-size: 2.2rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 50px;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 3px;
            background: #ff8c00;
            margin: 15px auto 0;
            border-radius: 2px;
        }

        .profile-container {
            display: flex;
            gap: 48px;
            align-items: center;
            margin-bottom: 60px;
        }

        .profile-image-wrapper {
            flex: 1;
            max-width: 380px;
            position: relative;
        }

        .profile-image {
            width: 100%;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            object-fit: cover;
            height: 420px;
            display: block;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 10;
            transition: transform 0.3s;
        }

        .profile-image:hover {
            transform: scale(1.02);
        }

        .profile-image-decoration {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            z-index: 1;
        }

        .decoration-1 {
            top: -20px;
            left: -20px;
            width: 120px;
            height: 120px;
            background: rgba(255, 140, 0, 0.25);
        }

        .decoration-2 {
            bottom: -20px;
            right: -20px;
            width: 150px;
            height: 150px;
            background: rgba(30, 58, 138, 0.4);
        }

        .profile-details {
            flex: 2;
        }

        .profile-subtitle {
            font-size: 0.85rem;
            font-weight: 700;
            color: #ff8c00;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 12px;
            display: block;
        }

        .profile-name {
            font-size: 1.8rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 16px;
            line-height: 1.3;
        }

        .badge-container {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .profile-badge {
            background: rgba(255, 140, 0, 0.1);
            border: 1px solid rgba(255, 140, 0, 0.3);
            color: #ff8c00;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 12px;
        }

        .profile-quote {
            font-size: 1.05rem;
            line-height: 1.6;
            color: #8fa8c8;
            margin-bottom: 30px;
            font-style: italic;
            border-left: 4px solid #ff8c00;
            padding-left: 16px;
        }

        .qualifications-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .qualification-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 10px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: background 0.2s, border-color 0.2s;
        }

        .qualification-card:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 140, 0, 0.2);
        }

        .q-icon {
            font-size: 1.3rem;
            color: #ff8c00;
        }

        .qualification-card h4 {
            font-size: 0.9rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 4px;
        }

        .qualification-card p {
            font-size: 0.8rem;
            color: #8fa8c8;
        }

        /* Stats/Experience Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 12px;
            padding: 32px 24px;
            text-align: center;
            transition: transform 0.2s, border-color 0.2s;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: #ff8c00;
            transform: scaleX(0);
            transition: transform 0.3s;
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(255, 140, 0, 0.25);
        }

        .stat-icon {
            font-size: 2.2rem;
            color: #ff8c00;
            margin-bottom: 20px;
            display: inline-block;
        }

        .stat-card h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 12px;
        }

        .stat-card p {
            font-size: 0.9rem;
            color: #8fa8c8;
            line-height: 1.6;
        }

        /* About Section */
        .about-section {
            padding: 80px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .about-container {
            display: flex;
            gap: 48px;
            align-items: stretch;
        }

        .about-text {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .about-lead {
            font-size: 1.2rem;
            line-height: 1.7;
            color: #fff;
            margin-bottom: 20px;
        }

        .highlight-text {
            color: #ff8c00;
            font-weight: 700;
        }

        .about-description {
            font-size: 1rem;
            line-height: 1.7;
            color: #8fa8c8;
        }

        .about-vision-mission {
            flex: 1;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            padding: 36px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border-left: 4px solid #ff8c00;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .vision-block h3,
        .mission-block h3 {
            font-size: 1.2rem;
            color: #ff8c00;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .vision-block p {
            font-style: italic;
            color: #e0e0e0;
            line-height: 1.6;
        }

        .mission-block ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .mission-block li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            color: #e0e0e0;
            font-size: 0.95rem;
        }

        .mission-block li i {
            color: #ff8c00;
            margin-top: 3px;
        }

        /* Links Hub Section */
        .links-hub-section {
            padding: 40px 0 80px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .hero-box {
            background: linear-gradient(135deg, rgba(255, 140, 0, 0.08) 0%, rgba(30, 58, 138, 0.15) 100%);
            border: 1px solid rgba(255, 140, 0, 0.2);
            border-radius: 20px;
            padding: 48px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
            position: relative;
            overflow: hidden;
        }

        .hero-box::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 180px;
            height: 180px;
            background: rgba(255, 140, 0, 0.15);
            filter: blur(50px);
            border-radius: 50%;
        }

        .hero-box-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .hero-box-subtitle {
            font-size: 0.8rem;
            font-weight: 700;
            color: #ff8c00;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
        }

        .hero-box-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 12px;
        }

        .hero-box-desc {
            font-size: 0.95rem;
            color: #8fa8c8;
            max-width: 650px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .link-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 14px;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            text-decoration: none;
            transition: background 0.2s, border-color 0.2s, transform 0.2s;
            position: relative;
        }

        .link-item:hover {
            background: rgba(255, 140, 0, 0.05);
            border-color: rgba(255, 140, 0, 0.3);
            transform: translateY(-3px);
        }

        .link-icon {
            font-size: 1.4rem;
            color: #ff8c00;
            background: rgba(255, 140, 0, 0.1);
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background 0.2s, color 0.2s;
        }

        .link-item:hover .link-icon {
            background: #ff8c00;
            color: #fff;
        }

        .link-text {
            flex-grow: 1;
        }

        .link-text h4 {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 4px;
        }

        .link-text p {
            font-size: 0.8rem;
            color: #8fa8c8;
            line-height: 1.4;
        }

        .arrow-icon {
            font-size: 0.9rem;
            color: #5a7090;
            transition: transform 0.2s, color 0.2s;
        }

        .link-item:hover .arrow-icon {
            color: #ff8c00;
            transform: translateX(3px);
        }

        /* Contact Section */
        .contact-section {
            padding: 80px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .contact-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .contact-info {
            flex: 1;
            padding: 48px;
        }

        .contact-lead {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #8fa8c8;
            margin-bottom: 36px;
        }

        .contact-details {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 18px;
        }

        .contact-icon-wrapper {
            background: rgba(255, 140, 0, 0.1);
            border: 1px solid rgba(255, 140, 0, 0.3);
            color: #ff8c00;
            font-size: 1.2rem;
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .contact-item h4 {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }

        .contact-item p {
            font-size: 0.9rem;
            color: #8fa8c8;
            line-height: 1.5;
        }

        .contact-item a {
            color: #ff8c00;
            text-decoration: none;
            transition: color 0.2s;
        }

        .contact-item a:hover {
            color: #fff;
        }

        .contact-map {
            flex: 1;
            min-height: 350px;
            position: relative;
        }

        .contact-map iframe {
            width: 100%;
            height: 100%;
            display: block;
            filter: grayscale(100%) invert(90%) contrast(90%);
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 32px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            color: #5a7090;
            font-size: 0.85rem;
        }

        .footer a.admin-link {
            color: #3a4f6a;
            text-decoration: none;
            font-size: 0.75rem;
            transition: color 0.2s;
        }

        .footer a.admin-link:hover {
            color: #8fa8c8;
        }

        @media (max-width: 768px) {
            .nav-menu {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(10, 22, 40, 0.98);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 12px;
                padding: 24px;
                gap: 16px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            }

            .nav-menu.active {
                display: flex;
            }

            .mobile-toggle {
                display: block;
            }

            .nav-btn {
                width: 100%;
                justify-content: center;
            }

            .profile-container {
                flex-direction: column;
                text-align: center;
                gap: 32px;
            }

            .profile-image-wrapper {
                margin: 0 auto;
                max-width: 280px;
            }

            .profile-image {
                height: 320px;
            }

            .profile-quote {
                border-left: none;
                border-top: 2px solid #ff8c00;
                border-bottom: 2px solid #ff8c00;
                padding: 16px 0;
                margin: 24px 0;
            }

            .badge-container {
                justify-content: center;
            }

            .qualification-card {
                text-align: left;
            }

            .contact-card {
                flex-direction: column;
            }

            .contact-info {
                padding: 32px 24px;
            }

            .contact-map {
                min-height: 250px;
            }

            .about-container {
                flex-direction: column;
                gap: 32px;
            }

            .about-vision-mission {
                padding: 24px;
            }

            .hero-box {
                padding: 32px 20px;
            }

            .links-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header class="header">
            <a href="#" class="logo">Simple<span>Akunting</span></a>
            <nav class="nav-menu" id="navMenu">
                <a href="#" class="nav-link">Beranda</a>
                <a href="#about" class="nav-link">Tentang Kami</a>
                <a href="#features" class="nav-link">Fitur</a>
                <a href="#profile" class="nav-link">Profil Pimpinan</a>
                <a href="https://simpleakunting.my.id/panduanhibahSA.html" target="_blank" class="nav-link">Hibah
                    Software</a>
                <a href="#contact" class="nav-link">Kontak</a>
                <a href="{{ route('central.login') }}" class="nav-btn"><i class="fas fa-user-shield"></i> Login
                    Admin</a>
            </nav>
            <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle Menu">
                <i class="fas fa-bars"></i>
            </button>
        </header>

        <section class="hero">
            <h1>Platform Akuntansi <em>Multi-Tenant</em></h1>
            <p>Mengelola akuntansi banyak perusahaan dari satu platform. Setiap perusahaan mendapatkan database
                terpisah, keamanan tinggi, dan fitur lengkap.</p>
        </section>

        <!-- About Section -->
        <section id="about" class="about-section">
            <h2 class="section-title">Tentang simpleakunting.id</h2>

            <div class="about-container">
                <div class="about-text">
                    <p class="about-lead">
                        <span class="highlight-text">simpleakunting.id</span> adalah platform dan layanan akuntansi
                        profesional yang berkedudukan di Mojokerto, Jawa Timur. Kami hadir sebagai mitra strategis bagi
                        pelaku usaha (UMKM), BUMDesa, Koperasi, hingga perusahaan swasta.
                    </p>
                    <p class="about-description">
                        Didirikan dan dipimpin langsung oleh praktisi berpengalaman dengan latar belakang akademis yang
                        kuat, kami memadukan keahlian teknis bersertifikasi internasional dengan pemahaman mendalam
                        mengenai regulasi dan kondisi lapangan di Indonesia.
                    </p>
                </div>

                <div class="about-vision-mission">
                    <div class="vision-block">
                        <h3><i class="fas fa-eye"></i> Visi</h3>
                        <p>"Menjadi mitra konsultan terpercaya yang mampu meningkatkan tata kelola keuangan dan
                            manajemen bisnis yang akuntabel bagi UMKM dan BUMDesa di Jawa Timur."</p>
                    </div>

                    <div class="mission-block">
                        <h3><i class="fas fa-bullseye"></i> Misi</h3>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> Layanan pendampingan akuntansi presisi.</li>
                            <li><i class="fas fa-check-circle"></i> Peningkatan kapasitas SDM melalui pelatihan.</li>
                            <li><i class="fas fa-check-circle"></i> Solusi audit & mitigasi risiko bisnis.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="features">
            <div class="feature-card">
                <h3>📊 Akuntansi Lengkap</h3>
                <p>Point of Sales, Jurnal, Buku Besar, Neraca, Laba Rugi, Arus Kas, dan laporan keuangan lainnya sesuai
                    SAK.</p>
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

        <section id="profile" class="profile-section">
            <h2 class="section-title">Profil Pimpinan</h2>

            <div class="profile-container">
                <div class="profile-image-wrapper">
                    <img src="{{ asset('images/ayahrompi.png') }}" alt="Kurniawan - Managing Director"
                        class="profile-image">
                    <div class="profile-image-decoration decoration-1"></div>
                    <div class="profile-image-decoration decoration-2"></div>
                </div>

                <div class="profile-details">
                    <span class="profile-subtitle">Managing Director</span>
                    <h3 class="profile-name">Kurniawan, S.E., Ak., CA., M.Ak., CMA., CIBA., CIAP.</h3>

                    <div class="badge-container">
                        <span class="profile-badge">CMA</span>
                        <span class="profile-badge">CIBA</span>
                        <span class="profile-badge">CIAP</span>
                        <span class="profile-badge">CA</span>
                        <span class="profile-badge">Ak.</span>
                    </div>

                    <p class="profile-quote">
                        "simpleakunting.id dipimpin oleh seorang profesional yang memiliki kombinasi unik antara
                        pengalaman praktis di industri, penugasan di sektor publik, dan latar belakang akademis yang
                        solid."
                    </p>

                    <div class="qualifications-grid">
                        <div class="qualification-card">
                            <i class="fas fa-award q-icon"></i>
                            <div>
                                <h4>Chartered Accountant (CA)</h4>
                                <p>Ikatan Akuntan Indonesia</p>
                            </div>
                        </div>
                        <div class="qualification-card">
                            <i class="fas fa-university q-icon"></i>
                            <div>
                                <h4>Magister Akuntansi (M.Ak)</h4>
                                <p>Univ. Trunojoyo Madura</p>
                            </div>
                        </div>
                        <div class="qualification-card">
                            <i class="fas fa-chart-pie q-icon"></i>
                            <div>
                                <h4>Certified Mgt. Accountant (CMA)</h4>
                                <p>Universitas Airlangga</p>
                            </div>
                        </div>
                        <div class="qualification-card">
                            <i class="fas fa-globe q-icon"></i>
                            <div>
                                <h4>Intl. Business Analysis (CIBA)</h4>
                                <p>Univ. Kristen Petra</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-landmark stat-icon"></i>
                    <h3>Sektor Publik</h3>
                    <p>Tenaga Ahli Klinik BUMDesa Jatim & Penilai BUMDesa Berhasil (2015-2024)</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-building stat-icon"></i>
                    <h3>Sektor Korporasi</h3>
                    <p>Manajer Keuangan, Audit Internal & CSR (Danone, USAID, Medco)</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-user-graduate stat-icon"></i>
                    <h3>Akademisi & Mentor</h3>
                    <p>Dosen Tetap, Mentor Inkubasi Bisnis & Narasumber Tingkat Provinsi</p>
                </div>
            </div>
        </section>

        <!-- Ecosystem / Other Links Section -->
        <section id="links-hub" class="links-hub-section">
            <div class="hero-box">
                <div class="hero-box-header">
                    <span class="hero-box-subtitle">AKSES CEPAT</span>
                    <h2 class="hero-box-title">Sistem & Layanan Pendukung</h2>
                    <p class="hero-box-desc">Hubungkan dengan platform akuntansi, koperasi, dan tata kelola digital
                        lainnya dalam ekosistem kami.</p>
                </div>

                <div class="links-grid">
                    <a href="https://simpleakunting.my.id/" target="_blank" class="link-item">
                        <i class="fas fa-globe link-icon"></i>
                        <div class="link-text">
                            <h4>Solusi Consult</h4>
                            <p>Profil & Layanan Konsultan</p>
                        </div>
                        <i class="fas fa-arrow-right arrow-icon"></i>
                    </a>

                    <a href="https://simpleakunting.biz.id/login.php" target="_blank" class="link-item">
                        <i class="fas fa-sign-in-alt link-icon"></i>
                        <div class="link-text">
                            <h4>SimpleAkunting v2</h4>
                            <p>Classic Login Platform</p>
                        </div>
                        <i class="fas fa-arrow-right arrow-icon"></i>
                    </a>

                    <a href="https://v3.simpleakunting.biz.id/login" target="_blank" class="link-item">
                        <i class="fas fa-laptop-code link-icon"></i>
                        <div class="link-text">
                            <h4>SimpleAkunting v3</h4>
                            <p>Sistem Akuntansi Koperasi & UMKM v3</p>
                        </div>
                        <i class="fas fa-arrow-right arrow-icon"></i>
                    </a>

                    <a href="https://v4.simpleakunting.biz.id/admin/login" target="_blank" class="link-item">
                        <i class="fas fa-shield-alt link-icon"></i>
                        <div class="link-text">
                            <h4>SimpleAkunting v4</h4>
                            <p>Sistem Akuntansi BUMDesa & Koperasi v4</p>
                        </div>
                        <i class="fas fa-arrow-right arrow-icon"></i>
                    </a>

                    <a href="https://umkm.simkopdes.biz.id/login" target="_blank" class="link-item">
                        <i class="fas fa-user-tie link-icon"></i>
                        <div class="link-text">
                            <h4>Puspa Candra</h4>
                            <p>Login Portal Pelaku UMKM</p>
                        </div>
                        <i class="fas fa-arrow-right arrow-icon"></i>
                    </a>

                    <a href="https://bumdesadigital.my.id/" target="_blank" class="link-item">
                        <i class="fas fa-university link-icon"></i>
                        <div class="link-text">
                            <h4>BUMDesa Digital</h4>
                            <p>Platform Akuntansi Digital BUMDesa Jatim</p>
                        </div>
                        <i class="fas fa-arrow-right arrow-icon"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="contact-section">
            <h2 class="section-title">Hubungi Kami</h2>

            <div class="contact-card">
                <div class="contact-info">
                    <p class="contact-lead">Siap meningkatkan tata kelola keuangan bisnis Anda? Konsultasikan kebutuhan
                        Anda bersama simpleakunting.id.</p>

                    <div class="contact-details">
                        <div class="contact-item">
                            <div class="contact-icon-wrapper">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h4>Alamat Kantor</h4>
                                <p>Jl. Suromulang Barat VI/20, Mojokerto, Jawa Timur.</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon-wrapper">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <h4>Telepon / WhatsApp</h4>
                                <p><a href="https://wa.me/6282141643495" target="_blank">+62 821 4164 3495</a></p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon-wrapper">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h4>Email</h4>
                                <p><a href="mailto:kurniawan@petalmail.com">kurniawan@petalmail.com</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contact-map">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.779427499622!2d112.4340!3d-7.4670!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zN8KwMjgnMDEuMiJTIDExMsKwMjYnMDIuNCJF!5e0!3m2!1sen!2sid!4v1620000000000!5m2!1sen!2sid"
                        style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </section>

        <footer class="footer">
            &copy; {{ date('Y') }} SimpleAkunting dibuat oleh Kurniawan dengan ❤️ untuk membantu bisnis lebih
            berkembang. All rights reserved.
            <br>
            <a href="{{ route('central.login') }}" class="admin-link">Login Administrator</a>
        </footer>
    </div>

    <script>
        // PWA Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registered'))
                    .catch(err => console.error('Service Worker registration failed', err));
            });
        }

        // Mobile Menu Toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const navMenu = document.getElementById('navMenu');
        if (mobileToggle && navMenu) {
            mobileToggle.addEventListener('click', () => {
                navMenu.classList.toggle('active');
                const icon = mobileToggle.querySelector('i');
                if (icon) {
                    if (navMenu.classList.contains('active')) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                    } else {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                }
            });

            // Close menu when clicking nav links on mobile
            const navLinks = navMenu.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    navMenu.classList.remove('active');
                    const icon = mobileToggle.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                });
            });
        }
    </script>
</body>

</html>