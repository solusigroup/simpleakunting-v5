<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Simple Akunting')</title>

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SimpleAkunting">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">
    <meta name="theme-color" content="#ff8c00">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    
    <style>
        /* ========================================
           App Layout Styles
        ======================================== */
        body {
            font-family: var(--font-family);
            font-size: var(--font-size-sm);
            background-color: var(--color-bg);
            overflow-x: hidden;
        }

        /* Top Header */
        .app-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 var(--space-md);
            z-index: 1000;
            box-shadow: var(--shadow-md);
        }

        .app-brand {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            color: var(--color-white);
            text-decoration: none;
            font-weight: 600;
            font-size: var(--font-size-lg);
        }

        .app-brand-icon {
            font-size: 24px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: var(--space-md);
        }

        .btn-sidebar-toggle {
            background: transparent;
            border: none;
            color: var(--color-white);
            padding: var(--space-sm);
            cursor: pointer;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-sidebar-toggle:hover {
            background: rgba(255,255,255,0.1);
        }

        .btn-logout {
            background: rgba(255,255,255,0.1);
            border: none;
            color: var(--color-white);
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-sm);
            font-size: var(--font-size-sm);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            transition: all var(--transition-fast);
        }

        .btn-logout:hover {
            background: rgba(255,255,255,0.2);
        }

        /* Sidebar */
        .app-sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            width: 260px;
            height: calc(100vh - 60px);
            background: var(--color-white);
            border-right: 1px solid var(--color-border-light);
            overflow-y: auto;
            transition: transform var(--transition-base);
            z-index: 900;
        }

        .app-sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar-content {
            padding: var(--space-md) 0;
        }

        /* Sidebar Navigation */
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-nav-item {
            margin-bottom: 2px;
        }

        .sidebar-nav-link {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: 10px var(--space-lg);
            color: var(--color-text);
            text-decoration: none;
            font-weight: 500;
            font-size: var(--font-size-sm);
            transition: all var(--transition-fast);
            border-left: 3px solid transparent;
        }

        .sidebar-nav-link:hover {
            background: var(--color-bg);
            color: var(--color-primary);
        }

        .sidebar-nav-link.active {
            background: rgba(139, 92, 246, 0.05);
            color: var(--color-primary);
            border-left-color: var(--color-primary);
        }

        .sidebar-nav-link .feather {
            width: 18px;
            height: 18px;
            color: var(--color-text-muted);
        }

        .sidebar-nav-link:hover .feather,
        .sidebar-nav-link.active .feather {
            color: var(--color-primary);
        }

        /* Sidebar Section Header */
        .sidebar-section {
            margin-top: var(--space-lg);
        }

        .sidebar-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--space-sm) var(--space-lg);
            color: var(--color-text-muted);
            font-size: var(--font-size-xs);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .sidebar-section-header:hover {
            background: var(--color-bg);
        }

        .sidebar-section-header .chevron {
            transition: transform var(--transition-fast);
        }

        .sidebar-section-header[aria-expanded="true"] .chevron {
            transform: rotate(180deg);
        }

        .sidebar-submenu {
            padding-left: var(--space-sm);
        }

        /* Main Content */
        .app-main {
            margin-left: 260px;
            margin-top: 60px;
            min-height: calc(100vh - 60px);
            padding: var(--space-lg);
            transition: margin-left var(--transition-base);
        }

        .app-main.expanded {
            margin-left: 0;
        }

        /* Page Header */
        .page-header {
            margin-bottom: var(--space-lg);
        }

        .page-title {
            font-size: var(--font-size-2xl);
            font-weight: 600;
            color: var(--color-text);
            margin: 0 0 var(--space-xs) 0;
        }

        .page-subtitle {
            color: var(--color-text-muted);
            font-size: var(--font-size-sm);
            margin: 0;
        }

        /* Mobile Bottom Navigation */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: var(--color-white);
            border-top: 1px solid var(--color-border-light);
            z-index: 1000;
            padding: var(--space-sm) 0;
        }

        .bottom-nav-list {
            display: flex;
            justify-content: space-around;
            align-items: center;
            list-style: none;
            padding: 0;
            margin: 0;
            height: 100%;
        }

        .bottom-nav-item {
            flex: 1;
        }

        .bottom-nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            color: var(--color-text-muted);
            text-decoration: none;
            font-size: 10px;
            font-weight: 500;
            padding: var(--space-xs);
        }

        .bottom-nav-link .feather {
            width: 22px;
            height: 22px;
        }

        .bottom-nav-link.active,
        .bottom-nav-link:hover {
            color: var(--color-primary);
        }

        /* Alerts */
        .app-alert {
            padding: var(--space-md);
            border-radius: var(--radius-md);
            margin-bottom: var(--space-md);
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .app-alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--color-success);
        }

        .app-alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--color-danger);
        }

        .app-alert-close {
            margin-left: auto;
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            padding: 0;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .app-sidebar {
                transform: translateX(-100%);
            }

            .app-sidebar.show {
                transform: translateX(0);
            }

            .app-main {
                margin-left: 0;
            }

            .bottom-nav {
                display: block;
            }

            .app-main {
                padding-bottom: 80px;
            }
        }

        /* Override Bootstrap feather icons size */
        .feather {
            width: 18px;
            height: 18px;
            vertical-align: text-bottom;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="app-header">
        <div class="d-flex align-items-center gap-3">
            <button class="btn-sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
            <a href="{{ route('dashboard') }}" class="app-brand">
                <span class="app-brand-icon">📊</span>
                <span class="d-none d-sm-inline">Simple Akunting</span>
            </a>
        </div>
        
        <!-- Cabang/Unit Selector (Admin/Superuser) -->
        @if(auth()->user()->hasRole(['superuser', 'admin']))
        <div class="d-none d-lg-flex align-items-center gap-2 mx-auto">
            <form action="{{ route('cabang.switch') }}" method="POST" id="formSwitchCabang" class="d-flex align-items-center gap-2">
                @csrf
                <select name="active_cabang" class="form-select form-select-sm" style="width: auto; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2);" onchange="this.form.submit()">
                    <option value="" style="color: black;">-- Semua Cabang --</option>
                    @foreach(DB::table('cabang')->orderBy('nama_cabang')->get() as $c)
                        <option value="{{ $c->id }}" {{ session('active_cabang') == $c->id ? 'selected' : '' }} style="color: black;">{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>

                @if(session('active_cabang'))
                <select name="active_unit" class="form-select form-select-sm" style="width: auto; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2);" onchange="this.form.submit()">
                    <option value="" style="color: black;">-- Semua Unit --</option>
                    @foreach(DB::table('unit_usaha')->where('id_cabang', session('active_cabang'))->orderBy('nama_unit')->get() as $u)
                        <option value="{{ $u->id }}" {{ session('active_unit') == $u->id ? 'selected' : '' }} style="color: black;">{{ $u->nama_unit }}</option>
                    @endforeach
                </select>
                @endif
            </form>
        </div>
        @endif

        <div class="header-actions">
            <!-- Theme Toggle -->
            <button class="btn-sidebar-toggle me-2" id="themeToggle" title="Ganti Tema">
                <span id="themeToggleLightIcon" data-feather="sun" style="display: none;"></span>
                <span id="themeToggleDarkIcon" data-feather="moon"></span>
            </button>

            <span class="text-white d-none d-md-inline" style="opacity: 0.8; font-size: 0.875rem;">
                {{ Auth::user()->nama_user }}
            </span>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span class="d-none d-sm-inline">Keluar</span>
                </button>
            </form>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="app-sidebar" id="sidebarMenu">
        <div class="sidebar-content">
            <ul class="sidebar-nav">
                <li class="sidebar-nav-item">
                    <a class="sidebar-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <span data-feather="home"></span>
                        Dashboard
                    </a>
                </li>
            </ul>

            <!-- Master Data -->
            @if(auth()->user()->canViewMasterData())
            @php
                $isMasterActive = request()->routeIs('pelanggan.*') || request()->routeIs('pemasok.*') || request()->routeIs('persediaan.*') || request()->routeIs('akun.*');
            @endphp
            <div class="sidebar-section">
                <div class="sidebar-section-header" data-bs-toggle="collapse" data-bs-target="#masterDataMenu" aria-expanded="{{ $isMasterActive ? 'true' : 'false' }}">
                    <span>Master Data</span>
                    <span data-feather="chevron-down" class="chevron"></span>
                </div>
                <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="masterDataMenu">
                    <ul class="sidebar-nav sidebar-submenu">
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('pelanggan.*') ? 'active' : '' }}" href="{{ route('pelanggan.index') }}">
                                <span data-feather="users"></span>
                                Pelanggan
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('pemasok.*') ? 'active' : '' }}" href="{{ route('pemasok.index') }}">
                                <span data-feather="truck"></span>
                                Pemasok
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('persediaan.*') ? 'active' : '' }}" href="{{ route('persediaan.index') }}">
                                <span data-feather="box"></span>
                                Persediaan
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('akun.*') ? 'active' : '' }}" href="{{ route('akun.index') }}">
                                <span data-feather="list"></span>
                                Akun (COA)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif

            @php
                $jenisUsaha = DB::table('perusahaan')->value('jenis_usaha') ?? 'dagang';
                $showManufacturing = in_array($jenisUsaha, ['manufaktur', 'multi']);
                $showAgriculture = in_array($jenisUsaha, ['pertanian', 'multi']);
            @endphp

            <!-- Manufacturing -->
            @if($showManufacturing && auth()->user()->hasRole(['superuser','admin','manajer']))
            @php
                $isMfgActive = request()->routeIs('manufacturing.*');
            @endphp
            <div class="sidebar-section">
                <div class="sidebar-section-header" data-bs-toggle="collapse" data-bs-target="#mfgMenu" aria-expanded="{{ $isMfgActive ? 'true' : 'false' }}">
                    <span>🏭 Manufaktur</span>
                    <span data-feather="chevron-down" class="chevron"></span>
                </div>
                <div class="collapse {{ $isMfgActive ? 'show' : '' }}" id="mfgMenu">
                    <ul class="sidebar-nav sidebar-submenu">
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('manufacturing.bom.*') ? 'active' : '' }}" href="{{ route('manufacturing.bom.index') }}">
                                <span data-feather="layers"></span>
                                Bill of Materials
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('manufacturing.production.*') ? 'active' : '' }}" href="{{ route('manufacturing.production.index') }}">
                                <span data-feather="settings"></span>
                                Produksi
                            </a>
                        </li>

                        <!-- Laporan Manufaktur -->
                        <li class="sidebar-nav-item mt-2">
                            <span class="sidebar-nav-link text-secondary small">📊 Laporan Manufaktur</span>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('manufacturing.laporan.biaya_produksi') ? 'active' : '' }}" href="{{ route('manufacturing.laporan.biaya_produksi') }}">
                                <span data-feather="dollar-sign"></span>
                                Biaya Produksi
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('manufacturing.laporan.penggunaan_material') ? 'active' : '' }}" href="{{ route('manufacturing.laporan.penggunaan_material') }}">
                                <span data-feather="package"></span>
                                Penggunaan Material
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('manufacturing.laporan.wip_valuation') ? 'active' : '' }}" href="{{ route('manufacturing.laporan.wip_valuation') }}">
                                <span data-feather="clock"></span>
                                WIP Valuation
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif

            <!-- Agriculture (PSAK 69) -->
            @if($showAgriculture && auth()->user()->hasRole(['superuser','admin','manajer']))
            @php
                $isAgriActive = request()->routeIs('agriculture.*');
            @endphp
            <div class="sidebar-section">
                <div class="sidebar-section-header" data-bs-toggle="collapse" data-bs-target="#agriMenu" aria-expanded="{{ $isAgriActive ? 'true' : 'false' }}">
                    <span>🌱 Pertanian</span>
                    <span data-feather="chevron-down" class="chevron"></span>
                </div>
                <div class="collapse {{ $isAgriActive ? 'show' : '' }}" id="agriMenu">
                    <ul class="sidebar-nav sidebar-submenu">
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('agriculture.index') || request()->routeIs('agriculture.create') || request()->routeIs('agriculture.edit') ? 'active' : '' }}" href="{{ route('agriculture.index') }}">
                                <span data-feather="feather"></span>
                                Aset Biologis
                            </a>
                        </li>

                        <!-- Laporan PSAK 69 -->
                        <li class="sidebar-nav-item mt-2">
                            <span class="sidebar-nav-link text-secondary small">📊 Laporan PSAK 69</span>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('agriculture.laporan.rekonsiliasi') ? 'active' : '' }}" href="{{ route('agriculture.laporan.rekonsiliasi') }}">
                                <span data-feather="git-merge"></span>
                                Rekonsiliasi
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('agriculture.laporan.perubahan_nilai_wajar') ? 'active' : '' }}" href="{{ route('agriculture.laporan.perubahan_nilai_wajar') }}">
                                <span data-feather="trending-up"></span>
                                Perubahan Nilai Wajar
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('agriculture.laporan.produksi_panen') ? 'active' : '' }}" href="{{ route('agriculture.laporan.produksi_panen') }}">
                                <span data-feather="sunrise"></span>
                                Produksi & Panen
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('agriculture.laporan.pengungkapan') ? 'active' : '' }}" href="{{ route('agriculture.laporan.pengungkapan') }}">
                                <span data-feather="file-text"></span>
                                Pengungkapan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif

            <!-- Point of Sales -->
            @if(auth()->user()->canAccessPos())
            @php
                $isPosActive = request()->routeIs('pos.*');
            @endphp
            <div class="sidebar-section">
                <div class="sidebar-section-header" data-bs-toggle="collapse" data-bs-target="#posMenu" aria-expanded="{{ $isPosActive ? 'true' : 'false' }}">
                    <span>🛒 Point of Sales</span>
                    <span data-feather="chevron-down" class="chevron"></span>
                </div>
                <div class="collapse {{ $isPosActive ? 'show' : '' }}" id="posMenu">
                    <ul class="sidebar-nav sidebar-submenu">
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('pos.index') ? 'active' : '' }}" href="{{ route('pos.index') }}">
                                <span data-feather="monitor"></span>
                                Kasir
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('pos.session.*') ? 'active' : '' }}" href="{{ route('pos.session.create') }}">
                                <span data-feather="clock"></span>
                                Buka/Tutup Shift
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('pos.shift.*') ? 'active' : '' }}" href="{{ route('pos.session.create') }}">
                                <span data-feather="file-text"></span>
                                Laporan Shift
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif

            <!-- Transaksi -->
            @php
                $isTransaksiActive = request()->routeIs('penjualan.*') || request()->routeIs('pembelian.*') || request()->routeIs('jurnal.*');
            @endphp
            <div class="sidebar-section">
                <div class="sidebar-section-header" data-bs-toggle="collapse" data-bs-target="#transaksiMenu" aria-expanded="{{ $isTransaksiActive ? 'true' : 'false' }}">
                    <span>Transaksi</span>
                    <span data-feather="chevron-down" class="chevron"></span>
                </div>
                <div class="collapse {{ $isTransaksiActive ? 'show' : '' }}" id="transaksiMenu">
                    <ul class="sidebar-nav sidebar-submenu">
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('penjualan.*') ? 'active' : '' }}" href="{{ route('penjualan.index') }}">
                                <span data-feather="shopping-cart"></span>
                                Penjualan
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('pembelian.*') ? 'active' : '' }}" href="{{ route('pembelian.index') }}">
                                <span data-feather="shopping-bag"></span>
                                Pembelian
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('jurnal.*') && !request()->routeIs('jurnal.createKas') ? 'active' : '' }}" href="{{ route('jurnal.index') }}">
                                <span data-feather="file-text"></span>
                                Jurnal Umum
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('jurnal.createKas') ? 'active' : '' }}" href="{{ route('jurnal.createKas') }}">
                                <span data-feather="plus-square"></span>
                                Jurnal Kas (KM/KK)
                            </a>
                        </li>
                        <li class="sidebar-nav-item mt-2">
                            <span class="sidebar-nav-link text-secondary small">🔄 Retur</span>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('retur.penjualan.*') ? 'active' : '' }}" href="{{ route('retur.penjualan.index') }}">
                                <span data-feather="corner-up-left"></span>
                                Retur Penjualan
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('retur.pembelian.*') ? 'active' : '' }}" href="{{ route('retur.pembelian.index') }}">
                                <span data-feather="corner-up-right"></span>
                                Retur Pembelian
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Kas & Bank -->
            @php
                $isKasActive = request()->routeIs('penerimaan.*') || request()->routeIs('pembayaran.*') || request()->routeIs('kas.*');
            @endphp
            <div class="sidebar-section">
                <div class="sidebar-section-header" data-bs-toggle="collapse" data-bs-target="#kasMenu" aria-expanded="{{ $isKasActive ? 'true' : 'false' }}">
                    <span>Kas & Bank</span>
                    <span data-feather="chevron-down" class="chevron"></span>
                </div>
                <div class="collapse {{ $isKasActive ? 'show' : '' }}" id="kasMenu">
                    <ul class="sidebar-nav sidebar-submenu">
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('penerimaan.*') ? 'active' : '' }}" href="{{ route('penerimaan.index') }}">
                                <span data-feather="arrow-down-circle"></span>
                                Penerimaan
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('pembayaran.*') ? 'active' : '' }}" href="{{ route('pembayaran.index') }}">
                                <span data-feather="arrow-up-circle"></span>
                                Pembayaran
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('kas.*') ? 'active' : '' }}" href="{{ route('kas.index') }}">
                                <span data-feather="dollar-sign"></span>
                                Transaksi Kas
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Akuntansi / Tutup Buku -->
            @if(in_array(auth()->user()->role ?? '', ['admin', 'manajer', 'owner']))
            @php
                $isAkuntansiActive = request()->routeIs('accounting.closing.*') || request()->routeIs('aset-tetap-group.*') || request()->routeIs('aset-tetap.*');
            @endphp
            <div class="sidebar-section">
                <div class="sidebar-section-header" data-bs-toggle="collapse" data-bs-target="#akuntansiMenu" aria-expanded="{{ $isAkuntansiActive ? 'true' : 'false' }}">
                    <span>📊 Akuntansi</span>
                    <span data-feather="chevron-down" class="chevron"></span>
                </div>
                <div class="collapse {{ $isAkuntansiActive ? 'show' : '' }}" id="akuntansiMenu">
                    <ul class="sidebar-nav sidebar-submenu">
                        <li class="sidebar-nav-item">
                            <span class="sidebar-nav-link text-secondary small">🏢 Manajemen Aset</span>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('aset-tetap-group.*') ? 'active' : '' }}" href="{{ route('aset-tetap-group.index') }}">
                                <span data-feather="grid"></span>
                                Kelompok Aset
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('aset-tetap.*') ? 'active' : '' }}" href="{{ route('aset-tetap.index') }}">
                                <span data-feather="briefcase"></span>
                                Aset Tetap
                            </a>
                        </li>
                        <li class="sidebar-nav-item mt-2">
                            <span class="sidebar-nav-link text-secondary small">🔒 Proses Akhir Bulan</span>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('accounting.closing.*') ? 'active' : '' }}" href="{{ route('accounting.closing.index') }}">
                                <span data-feather="lock"></span>
                                Tutup Buku (Closing)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif

            <!-- Laporan -->
            @if(auth()->user()->canViewReports())
            @php
                $isLaporanActive = request()->routeIs('bukubesar.*') || request()->routeIs('laporan.*');
            @endphp
            <div class="sidebar-section">
                <div class="sidebar-section-header" data-bs-toggle="collapse" data-bs-target="#laporanMenu" aria-expanded="{{ $isLaporanActive ? 'true' : 'false' }}">
                    <span>Laporan</span>
                    <span data-feather="chevron-down" class="chevron"></span>
                </div>
                <div class="collapse {{ $isLaporanActive ? 'show' : '' }}" id="laporanMenu">
                    <ul class="sidebar-nav sidebar-submenu">
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('bukubesar.*') ? 'active' : '' }}" href="{{ route('bukubesar.index') }}">
                                <span data-feather="book"></span>
                                Buku Besar
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('laporan.neraca') ? 'active' : '' }}" href="{{ route('laporan.neraca') }}">
                                <span data-feather="bar-chart-2"></span>
                                Neraca
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('laporan.neraca_lajur') ? 'active' : '' }}" href="{{ route('laporan.neraca_lajur') }}">
                                <span data-feather="grid"></span>
                                Neraca Lajur (10 Kolom)
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('laporan.labarugi') ? 'active' : '' }}" href="{{ route('laporan.labarugi') }}">
                                <span data-feather="trending-up"></span>
                                Laba Rugi
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('laporan.aruskas_langsung') ? 'active' : '' }}" href="{{ route('laporan.aruskas_langsung') }}">
                                <span data-feather="activity"></span>
                                Arus Kas (Langsung)
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('laporan.aruskas_tidak_langsung') ? 'active' : '' }}" href="{{ route('laporan.aruskas_tidak_langsung') }}">
                                <span data-feather="activity"></span>
                                Arus Kas (Tidak Langsung)
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('laporan.perubahan_ekuitas') ? 'active' : '' }}" href="{{ route('laporan.perubahan_ekuitas') }}">
                                <span data-feather="pie-chart"></span>
                                Perubahan Ekuitas
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('laporan.persediaan') ? 'active' : '' }}" href="{{ route('laporan.persediaan') }}">
                                <span data-feather="package"></span>
                                Persediaan Barang
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('laporan.mutasi_persediaan') ? 'active' : '' }}" href="{{ route('laporan.mutasi_persediaan') }}">
                                <span data-feather="repeat"></span>
                                Mutasi Persediaan
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('laporan.aset_tetap') ? 'active' : '' }}" href="{{ route('laporan.aset_tetap') }}">
                                <span data-feather="briefcase"></span>
                                Daftar Aset Tetap
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif

            <!-- Simpan Pinjam -->
            @php
                $isKoperasiActive = request()->routeIs('anggota.*') || request()->routeIs('simpanan.*') || request()->routeIs('pinjaman.*') || request()->routeIs('approval.*');
            @endphp
            <div class="sidebar-section">
                <div class="sidebar-section-header" data-bs-toggle="collapse" data-bs-target="#koperasiMenu" aria-expanded="{{ $isKoperasiActive ? 'true' : 'false' }}">
                    <span>🏦 Simpan Pinjam</span>
                    <span data-feather="chevron-down" class="chevron"></span>
                </div>
                <div class="collapse {{ $isKoperasiActive ? 'show' : '' }}" id="koperasiMenu">
                    <ul class="sidebar-nav sidebar-submenu">
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('anggota.*') ? 'active' : '' }}" href="{{ route('anggota.index') }}">
                                <span data-feather="users"></span>
                                Anggota
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('simpanan.*') ? 'active' : '' }}" href="{{ route('simpanan.index') }}">
                                <span data-feather="save"></span>
                                Simpanan
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('pinjaman.*') ? 'active' : '' }}" href="{{ route('pinjaman.index') }}">
                                <span data-feather="credit-card"></span>
                                Pinjaman
                            </a>
                        </li>
                        @if(auth()->user()->canApprove())
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('approval.*') ? 'active' : '' }}" href="{{ route('approval.inbox') }}">
                                <span data-feather="check-square"></span>
                                Approval
                            </a>
                        </li>
                        @endif
                        
                        <!-- Laporan Simpan Pinjam -->
                        <li class="sidebar-nav-item mt-2">
                            <span class="sidebar-nav-link text-secondary small">📊 Laporan</span>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('laporan.outstanding_simpan_pinjam') ? 'active' : '' }}" href="{{ route('laporan.outstanding_simpan_pinjam') }}">
                                <span data-feather="bar-chart-2"></span>
                                Outstanding
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('laporan.kolektibilitas_pinjaman') ? 'active' : '' }}" href="{{ route('laporan.kolektibilitas_pinjaman') }}">
                                <span data-feather="pie-chart"></span>
                                Kolektibilitas
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('laporan.perhitungan_shu') ? 'active' : '' }}" href="{{ route('laporan.perhitungan_shu') }}">
                                <span data-feather="dollar-sign"></span>
                                Perhitungan SHU
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Import/Export -->
            @if(auth()->user()->canImportExport())
            <ul class="sidebar-nav mt-3">
                <li class="sidebar-nav-item">
                    <a class="sidebar-nav-link {{ request()->routeIs('import-export.*') ? 'active' : '' }}" href="{{ route('import-export.index') }}">
                        <span data-feather="upload-cloud"></span>
                        Import/Export
                    </a>
                </li>
            </ul>
            @endif

            <!-- Admin -->
            @if(auth()->user()->canManageUsers())
            @php
                $isAdminActive = request()->routeIs('perusahaan.*') || request()->routeIs('users.*') || request()->routeIs('database.*') || request()->routeIs('jenis-pinjaman.*') || request()->routeIs('jenis-simpanan.*') || request()->routeIs('audit-trail.*');
            @endphp
            <div class="sidebar-section">
                <div class="sidebar-section-header" data-bs-toggle="collapse" data-bs-target="#adminMenu" aria-expanded="{{ $isAdminActive ? 'true' : 'false' }}">
                    <span>⚙️ Admin</span>
                    <span data-feather="chevron-down" class="chevron"></span>
                </div>
                <div class="collapse {{ $isAdminActive ? 'show' : '' }}" id="adminMenu">
                    <ul class="sidebar-nav sidebar-submenu">
                        @if(auth()->user()->canManageCompany())
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('perusahaan.*') ? 'active' : '' }}" href="{{ route('perusahaan.edit') }}">
                                <span data-feather="settings"></span>
                                Profil Perusahaan
                            </a>
                        </li>
                        @endif
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <span data-feather="users"></span>
                                Manajemen User
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                                <span data-feather="key"></span>
                                Manajemen Role
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('cabang.*') ? 'active' : '' }}" href="{{ route('cabang.index') }}">
                                <span data-feather="map-pin"></span>
                                Kantor Cabang
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('unit-usaha.*') ? 'active' : '' }}" href="{{ route('unit-usaha.index') }}">
                                <span data-feather="briefcase"></span>
                                Unit Usaha
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('jenis-pinjaman.*') ? 'active' : '' }}" href="{{ route('jenis-pinjaman.index') }}">
                                <span data-feather="layers"></span>
                                Jenis Pinjaman
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('jenis-simpanan.*') ? 'active' : '' }}" href="{{ route('jenis-simpanan.index') }}">
                                <span data-feather="layers"></span>
                                Jenis Simpanan
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('audit-trail.*') ? 'active' : '' }}" href="{{ route('audit-trail.index') }}">
                                <span data-feather="shield"></span>
                                Audit Trail
                            </a>
                        </li>
                        @if(auth()->user()->canAccessDatabase())
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('database.*') ? 'active' : '' }}" href="{{ route('database.index') }}">
                                <span data-feather="database"></span>
                                Manajemen Database
                            </a>
                        </li>
                        @endif
                        
                        <!-- Closing (Tutup Buku) -->
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link {{ request()->routeIs('accounting.closing.*') ? 'active' : '' }}" href="{{ route('accounting.closing.index') }}">
                                <span data-feather="lock"></span>
                                Tutup Buku
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif

            <!-- Help & Documentation -->
            <div class="sidebar-section mt-4">
                <ul class="sidebar-nav">
                    <li class="sidebar-nav-item">
                        <a class="sidebar-nav-link {{ request()->routeIs('guide.*') ? 'active' : '' }}" href="{{ route('guide.index') }}">
                            <span data-feather="help-circle"></span>
                            Panduan Pengoperasian
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="app-main" id="mainContent">
        @if(session('success'))
            <div class="app-alert app-alert-success">
                <span data-feather="check-circle"></span>
                {{ session('success') }}
                <button type="button" class="app-alert-close" onclick="this.parentElement.remove()">
                    <span data-feather="x"></span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="app-alert app-alert-danger">
                <span data-feather="alert-circle"></span>
                {{ session('error') }}
                <button type="button" class="app-alert-close" onclick="this.parentElement.remove()">
                    <span data-feather="x"></span>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation -->
    <nav class="bottom-nav">
        <ul class="bottom-nav-list">
            <li class="bottom-nav-item">
                <a href="{{ route('dashboard') }}" class="bottom-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span data-feather="home"></span>
                    Beranda
                </a>
            </li>
            <li class="bottom-nav-item">
                <a href="{{ route('jurnal.index') }}" class="bottom-nav-link {{ request()->routeIs('jurnal.*') || request()->routeIs('penjualan.*') || request()->routeIs('pembelian.*') ? 'active' : '' }}">
                    <span data-feather="list"></span>
                    Transaksi
                </a>
            </li>
            <li class="bottom-nav-item">
                <a href="{{ route('laporan.index') }}" class="bottom-nav-link {{ request()->routeIs('laporan.*') || request()->routeIs('bukubesar.*') ? 'active' : '' }}">
                    <span data-feather="bar-chart-2"></span>
                    Laporan
                </a>
            </li>
            <li class="bottom-nav-item">
                <a href="{{ route('users.index') }}" class="bottom-nav-link {{ request()->routeIs('users.*') || request()->routeIs('perusahaan.*') ? 'active' : '' }}">
                    <span data-feather="user"></span>
                    Akun
                </a>
            </li>
        </ul>
    </nav>

    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" style="display: none; position: fixed; top: 60px; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 800;"></div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"></script>
    <script>
        // Theme Management (Inline to prevent flash)
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();

        (function () {
            'use strict'
            
            // Initialize Feather Icons
            feather.replace({ 'aria-hidden': 'true' });

            // Theme Toggle Logic
            const themeToggle = document.getElementById('themeToggle');
            const themeToggleLightIcon = document.getElementById('themeToggleLightIcon');
            const themeToggleDarkIcon = document.getElementById('themeToggleDarkIcon');

            function updateThemeIcons() {
                if (document.documentElement.classList.contains('dark-mode')) {
                    themeToggleLightIcon.style.display = 'inline-block';
                    themeToggleDarkIcon.style.display = 'none';
                } else {
                    themeToggleLightIcon.style.display = 'none';
                    themeToggleDarkIcon.style.display = 'inline-block';
                }
            }
            updateThemeIcons();

            themeToggle.addEventListener('click', function() {
                document.documentElement.classList.toggle('dark-mode');
                const isDark = document.documentElement.classList.contains('dark-mode');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                updateThemeIcons();
                // Re-replace icons to ensure they render correctly
                feather.replace({ 'aria-hidden': 'true' });
            });

            // Sidebar Toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebarMenu');
            const main = document.getElementById('mainContent');
            const overlay = document.getElementById('sidebarOverlay');

            function isMobile() {
                return window.innerWidth < 992;
            }

            sidebarToggle.addEventListener('click', function () {
                if (isMobile()) {
                    sidebar.classList.toggle('show');
                    overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
                } else {
                    sidebar.classList.toggle('collapsed');
                    main.classList.toggle('expanded');
                }
            });

            overlay.addEventListener('click', function () {
                sidebar.classList.remove('show');
                overlay.style.display = 'none';
            });

            // Handle resize
            window.addEventListener('resize', function () {
                if (!isMobile()) {
                    sidebar.classList.remove('show');
                    overlay.style.display = 'none';
                }
            });

            // Slide Panel Functions (Global)
            window.openSlidePanel = function(panelId) {
                document.getElementById(panelId).classList.add('show');
                document.getElementById(panelId + 'Overlay').classList.add('show');
                document.body.style.overflow = 'hidden';
            };

            window.closeSlidePanel = function(panelId) {
                document.getElementById(panelId).classList.remove('show');
                document.getElementById(panelId + 'Overlay').classList.remove('show');
                document.body.style.overflow = '';
            };

            // Close slide panel on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.slide-panel.show').forEach(function(panel) {
                        closeSlidePanel(panel.id);
                    });
                }
            });
        })()
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registered'))
                    .catch(err => console.error('Service Worker registration failed', err));
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
