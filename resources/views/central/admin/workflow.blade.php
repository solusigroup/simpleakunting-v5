@extends('central.admin.layout')

@section('title', 'Workflow Dokumentasi')

@section('container-class', 'container-fluid')

@section('extra-styles')
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap');

    body {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        color: #334155 !important;
    }

    /* Navbar override for premium light glassmorphism */
    .navbar {
        background: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(20px) !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
    }
    .navbar-brand {
        color: #0284c7 !important;
    }
    .navbar-brand span {
        color: #0f172a !important;
    }
    .navbar-nav a {
        color: #475569 !important;
    }
    .navbar-nav a:hover {
        background: rgba(0, 0, 0, 0.04) !important;
        color: #0f172a !important;
    }
    .navbar-nav a.active {
        background: rgba(2, 132, 199, 0.08) !important;
        color: #0284c7 !important;
        font-weight: 600 !important;
    }
    .btn-logout {
        background: rgba(220, 53, 69, 0.08) !important;
        border: 1px solid rgba(220, 53, 69, 0.2) !important;
        color: #dc3545 !important;
    }

    /* ===== WORKFLOW PAGE STYLES ===== */
    .workflow-wrapper { display: flex; gap: 0; margin: -32px -24px; min-height: calc(100vh - 56px); background: transparent; }

    /* Sidebar */
    .wf-sidebar {
        width: 260px; flex-shrink: 0;
        background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(16px);
        border-right: 1px solid rgba(0, 0, 0, 0.08);
        padding: 20px 0; position: sticky; top: 56px; height: calc(100vh - 56px);
        overflow-y: auto;
    }
    .wf-sidebar::-webkit-scrollbar { width: 3px; }
    .wf-sidebar::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.1); border-radius: 3px; }
    .wf-sidebar-label {
        font-size: 0.65rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
        color: #64748b; padding: 10px 20px 6px; opacity: 0.8;
    }
    .wf-sidebar a {
        display: flex; align-items: center; gap: 8px;
        padding: 7px 20px; font-size: 0.8rem; color: #475569;
        text-decoration: none; transition: all 0.2s;
        border-left: 3px solid transparent;
    }
    .wf-sidebar a:hover { background: rgba(0,0,0,0.02); color: #0f172a; border-left-color: rgba(0,0,0,0.1); }
    .wf-sidebar a.active { background: rgba(2, 132, 199, 0.08); color: #0284c7; border-left-color: #0284c7; font-weight: 600; }
    .wf-sidebar a .wf-icon { font-size: 14px; width: 20px; text-align: center; }
    .wf-sidebar a .wf-num {
        font-size: 0.65rem; font-weight: 700; color: #64748b;
        background: rgba(0,0,0,0.05); padding: 1px 5px; border-radius: 3px;
        margin-left: auto;
    }

    /* Main content */
    .wf-content { flex: 1; padding: 32px; max-width: calc(100% - 260px); overflow-x: hidden; }

    /* Hero */
    .wf-hero { text-align: center; padding: 24px 0 40px; }
    .wf-hero-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(2, 132, 199, 0.08); border: 1px solid rgba(2, 132, 199, 0.15);
        padding: 4px 14px; border-radius: 20px; font-size: 0.7rem;
        color: #0284c7; font-weight: 600; margin-bottom: 14px;
    }
    .wf-hero h1 {
        font-size: 2.2rem; font-weight: 800;
        color: #0f172a;
        margin-bottom: 10px;
    }
    .wf-hero p { color: #475569; font-size: 0.95rem; max-width: 500px; margin: 0 auto 24px; }
    .wf-hero-stats { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
    .wf-hero-stat {
        background: #ffffff; border: 1px solid #e2e8f0;
        padding: 12px 22px; border-radius: 10px; text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    .wf-hero-stat .num { font-size: 1.5rem; font-weight: 800; color: #0284c7; }
    .wf-hero-stat .lbl { font-size: 0.65rem; color: #64748b; font-weight: 500; margin-top: 2px; }

    /* Sections */
    .wf-section { margin-bottom: 40px; scroll-margin-top: 80px; }
    .wf-section-header {
        display: flex; align-items: center; gap: 14px; margin-bottom: 20px;
        padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;
    }
    .wf-section-num {
        width: 36px; height: 36px; border-radius: 10px;
        display: grid; place-items: center; font-size: 0.85rem; font-weight: 800;
        color: #fff; flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .wf-section-header h2 { font-size: 1.25rem; font-weight: 800; color: #0f172a; }
    .wf-section-header .wf-sub { font-size: 0.75rem; color: #64748b; margin-top: 2px; }

    /* Cards */
    .wf-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px; padding: 28px; margin-bottom: 20px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.02), 0 4px 6px -4px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
    }
    .wf-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }
    .wf-card h3 { font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .wf-card h4 { font-size: 0.9rem; font-weight: 600; color: #0f172a; margin: 16px 0 8px; }
    .wf-card p, .wf-card li { color: #334155; font-size: 0.85rem; line-height: 1.7; }

    /* Diagram container */
    .wf-diagram {
        background: #f8fafc; border: 1px solid #e2e8f0;
        border-radius: 12px; padding: 24px 16px; margin: 16px 0;
        overflow-x: auto; text-align: center;
    }
    .wf-diagram svg { max-width: 100%; height: auto; }

    /* Tables */
    .wf-table-wrap {
        overflow-x: auto; margin: 12px 0; border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }
    .wf-table-wrap table { margin: 0; font-size: 0.8rem; }
    .wf-table-wrap thead th {
        background: rgba(2, 132, 199, 0.05); color: #0284c7;
        font-size: 0.7rem; letter-spacing: 0.3px; text-transform: uppercase;
        padding: 10px 14px; white-space: nowrap;
        border-bottom: 1px solid #e2e8f0;
    }
    .wf-table-wrap tbody td { padding: 8px 14px; font-size: 0.82rem; border-bottom: 1px solid #f1f5f9; color: #334155; }
    .wf-table-wrap code {
        background: rgba(2, 132, 199, 0.06); color: #025a87;
        padding: 1px 6px; border-radius: 3px; font-family: 'JetBrains Mono', Consolas, monospace; font-size: 0.78rem;
    }

    /* Alerts */
    .wf-alert {
        padding: 12px 16px; border-radius: 10px; margin: 12px 0;
        font-size: 0.8rem; line-height: 1.7; display: flex; gap: 10px;
        border: 1px solid;
    }
    .wf-alert-icon { font-size: 16px; flex-shrink: 0; }
    .wf-alert-note { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; }
    .wf-alert-tip { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; }
    .wf-alert-important { background: #f5f3ff; border-color: #ddd6fe; color: #5b21b6; }
    .wf-alert-warning { background: #fffbeb; border-color: #fde68a; color: #92400e; }
    .wf-alert-caution { background: #fff5f5; border-color: #fecaca; color: #991b1b; }
    .wf-alert strong { font-weight: 700; }

    /* Number Format Grid */
    .wf-nf-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 10px; margin: 12px 0; }
    .wf-nf-item {
        background: #f8fafc; border: 1px solid #e2e8f0;
        padding: 10px 12px; border-radius: 8px;
    }
    .wf-nf-item .nf-module { font-size: 0.6rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .wf-nf-item .nf-format { font-family: 'JetBrains Mono', Consolas, monospace; font-size: 0.82rem; font-weight: 600; color: #0284c7; margin-top: 2px; }
    .wf-nf-item .nf-example { font-size: 0.72rem; color: #64748b; }

    /* Pattern badges */
    .wf-pattern {
        display: inline-block;
        background: rgba(91, 33, 182, 0.06); border: 1px solid rgba(91, 33, 182, 0.15);
        color: #5b21b6; padding: 2px 8px; border-radius: 4px;
        font-family: 'JetBrains Mono', Consolas, monospace; font-size: 0.72rem; font-weight: 600;
    }

    /* Summary card */
    .wf-summary-card {
        background: linear-gradient(135deg, rgba(2, 132, 199, 0.03), rgba(16, 185, 129, 0.03));
        border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 20px; margin: 14px 0;
    }

    /* Back to top */
    .wf-back-top {
        position: fixed; bottom: 24px; right: 24px; z-index: 100;
        width: 40px; height: 40px; border-radius: 10px;
        background: linear-gradient(135deg, #0284c7, #0369a1); color: #fff; border: none;
        cursor: pointer; font-size: 18px; display: grid; place-items: center;
        opacity: 0; transform: translateY(16px); transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
    }
    .wf-back-top.visible { opacity: 1; transform: translateY(0); }
    .wf-back-top:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(2, 132, 199, 0.3); }

    /* Print */
    @media print {
        .wf-sidebar, .navbar, .wf-back-top { display: none !important; }
        .wf-content { max-width: 100% !important; padding: 0 !important; }
        .workflow-wrapper { display: block !important; margin: 0 !important; }
        .wf-card { break-inside: avoid; }
        body { background: #fff; color: #111; }
    }

    /* Mobile */
    @media (max-width: 768px) {
        .wf-sidebar { display: none; }
        .wf-content { max-width: 100%; padding: 20px; }
        .wf-nf-grid { grid-template-columns: 1fr 1fr; }
        .container { padding: 0; }
    }
@endsection

@section('content')
<div class="workflow-wrapper">

    <!-- ===== SIDEBAR ===== -->
    <nav class="wf-sidebar" id="wfSidebar">
        <div class="wf-sidebar-label">Overview</div>
        <a href="#wf-hero"><span class="wf-icon">🏠</span> Beranda</a>

        <div class="wf-sidebar-label">Arsitektur</div>
        <a href="#wf-s1"><span class="wf-icon">🏗️</span> Arsitektur Sistem <span class="wf-num">1</span></a>
        <a href="#wf-s2"><span class="wf-icon">🔑</span> Role & Permission <span class="wf-num">2</span></a>

        <div class="wf-sidebar-label">Setup & Transaksi</div>
        <a href="#wf-s3"><span class="wf-icon">🚀</span> Setup Awal <span class="wf-num">3</span></a>
        <a href="#wf-s4"><span class="wf-icon">🧾</span> Penjualan <span class="wf-num">4</span></a>
        <a href="#wf-s5"><span class="wf-icon">📦</span> Pembelian <span class="wf-num">5</span></a>
        <a href="#wf-s6"><span class="wf-icon">🔄</span> Retur <span class="wf-num">6</span></a>
        <a href="#wf-s7"><span class="wf-icon">📘</span> Jurnal Umum <span class="wf-num">7</span></a>
        <a href="#wf-s8"><span class="wf-icon">📱</span> Point of Sale <span class="wf-num">8</span></a>

        <div class="wf-sidebar-label">Modul Khusus</div>
        <a href="#wf-s9"><span class="wf-icon">🏦</span> Koperasi <span class="wf-num">9</span></a>
        <a href="#wf-s10"><span class="wf-icon">🏭</span> Manufaktur <span class="wf-num">10</span></a>
        <a href="#wf-s11"><span class="wf-icon">🌾</span> Pertanian <span class="wf-num">11</span></a>
        <a href="#wf-s12"><span class="wf-icon">🏢</span> Aset Tetap <span class="wf-num">12</span></a>

        <div class="wf-sidebar-label">Akuntansi & Laporan</div>
        <a href="#wf-s13"><span class="wf-icon">🔒</span> Tutup Buku <span class="wf-num">13</span></a>
        <a href="#wf-s14"><span class="wf-icon">📊</span> Laporan <span class="wf-num">14</span></a>
        <a href="#wf-s15"><span class="wf-icon">📤</span> Import/Export <span class="wf-num">15</span></a>
        <a href="#wf-s16"><span class="wf-icon">💰</span> Kas & Transfer <span class="wf-num">16</span></a>

        <div class="wf-sidebar-label">Infrastruktur</div>
        <a href="#wf-s17"><span class="wf-icon">🏢</span> Multi-Cabang <span class="wf-num">17</span></a>
        <a href="#wf-s18"><span class="wf-icon">📝</span> Audit Trail <span class="wf-num">18</span></a>
        <a href="#wf-s19"><span class="wf-icon">🗺️</span> Cross-Module <span class="wf-num">19</span></a>
        <a href="#wf-s20"><span class="wf-icon">📋</span> Summary <span class="wf-num">20</span></a>
        <a href="#wf-s21"><span class="wf-icon">🗄️</span> ER Diagram <span class="wf-num">21</span></a>
        <a href="#wf-s22"><span class="wf-icon">⚙️</span> Patterns <span class="wf-num">22</span></a>
    </nav>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="wf-content">

        <!-- HERO -->
        <div class="wf-hero" id="wf-hero">
            <div class="wf-hero-badge">📊 Dokumentasi Alur Kerja Sistem</div>
            <h1>Workflow SimpleAkunting v5</h1>
            <p>Sistem Akuntansi Terpadu — Dagang, Manufaktur, Pertanian (PSAK 69), Koperasi Simpan Pinjam</p>
            <div class="wf-hero-stats">
                <div class="wf-hero-stat"><div class="num">22</div><div class="lbl">Workflow</div></div>
                <div class="wf-hero-stat"><div class="num">16</div><div class="lbl">Modul</div></div>
                <div class="wf-hero-stat"><div class="num">14</div><div class="lbl">Auto-Jurnal</div></div>
                <div class="wf-hero-stat"><div class="num">5</div><div class="lbl">Level Role</div></div>
            </div>
        </div>

        <!-- ===== S1: ARSITEKTUR ===== -->
        <section class="wf-section" id="wf-s1">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#ff8c00,#e67600)">1</div>
                <div><h2>Arsitektur Sistem</h2><div class="wf-sub">Multi-Tenant Architecture dengan Stancl Tenancy</div></div>
            </div>
            <div class="wf-card">
                <h3>🌐 Multi-Tenant Architecture</h3>
                <div class="wf-diagram"><pre class="mermaid">
graph TB
    subgraph Central["🌐 Central Domain — simpleakunting.id"]
        LP["Landing Page"]
        CA["Central Admin Login"]
        TM["Tenant Management"]
    end
    subgraph Tenant1["🏢 Tenant A — demojaya.simpleakunting.id"]
        T1App["Full Accounting App"]
        T1DB["DB: dbv5_demojaya"]
    end
    subgraph Tenant2["🏢 Tenant B — kopcuy.simpleakunting.id"]
        T2App["Full Accounting App"]
        T2DB["DB: dbv5_kopcuy"]
    end
    CA --> TM
    TM --> Tenant1
    TM --> Tenant2
    LP --> CA
                </pre></div>
                <div class="wf-table-wrap"><table>
                    <thead><tr><th>Komponen</th><th>Detail</th></tr></thead>
                    <tbody>
                        <tr><td><strong>Framework</strong></td><td>Laravel 12.x + Stancl Tenancy</td></tr>
                        <tr><td><strong>Central DB</strong></td><td><code>dbv5_central</code></td></tr>
                        <tr><td><strong>Tenant DB</strong></td><td><code>dbv5_{tenant_id}</code></td></tr>
                        <tr><td><strong>Domain</strong></td><td><code>{tenant}.simpleakunting.id</code></td></tr>
                    </tbody>
                </table></div>
            </div>
            <div class="wf-card">
                <h3>🔀 Single-Tenant vs Multi-Tenant</h3>
                <div class="wf-diagram"><pre class="mermaid">
flowchart LR
    ENV["TENANCY_ENABLED"]
    ENV -->|false| ST["Single-Tenant Mode"]
    ENV -->|true| MT["Multi-Tenant Mode"]
    ST --> STR["web.php → auth_shared.php"]
    MT --> MTR1["web.php → Central Routes"]
    MT --> MTR2["tenant.php → auth_shared.php"]
                </pre></div>
            </div>
        </section>

        <!-- ===== S2: ROLE ===== -->
        <section class="wf-section" id="wf-s2">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#ff8c00,#e67600)">2</div>
                <div><h2>Hierarki Role & Permission</h2><div class="wf-sub">5 Level akses bertingkat</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
graph TB
    SU["🔑 Superuser"] --> AD["🛡️ Admin"] --> MN["📋 Manajer"] --> ST["👤 Staff"] --> KS["🧾 Kasir"]
    SU -.->|"Full Access + DB"| ALL["Semua Modul"]
    AD -.->|"Master + Settings"| A["Master, Users, Cabang, Tutup Buku"]
    MN -.->|"Read + Approval"| M["Approval, Mfg, Agri, Import"]
    ST -.->|"Transaksi"| S["Penjualan, Pembelian, Jurnal, Koperasi"]
    KS -.->|"POS"| K["Point of Sale, Laporan Dasar"]
                </pre></div>
                <div class="wf-table-wrap"><table>
                    <thead><tr><th>Role</th><th>Akses Modul</th></tr></thead>
                    <tbody>
                        <tr><td><strong>Superuser</strong></td><td>Semua modul + Database Management + bypass role check</td></tr>
                        <tr><td><strong>Admin</strong></td><td>Master Data CRUD, User & Role Management, Cabang, Tutup Buku, Aset Tetap</td></tr>
                        <tr><td><strong>Manajer</strong></td><td>Read Master, Approval, Manufacturing, Agriculture, Import/Export, Laporan Koperasi</td></tr>
                        <tr><td><strong>Staff</strong></td><td>Penjualan, Pembelian, Jurnal, Penerimaan, Pembayaran, Retur, Koperasi</td></tr>
                        <tr><td><strong>Kasir</strong></td><td>Point of Sale (POS), Laporan Dasar, Buku Besar</td></tr>
                    </tbody>
                </table></div>
            </div>
        </section>

        <!-- ===== S3: SETUP ===== -->
        <section class="wf-section" id="wf-s3">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#28a745,#20c997)">3</div>
                <div><h2>Workflow Setup Awal</h2><div class="wf-sub">Onboarding hingga siap bertransaksi</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
flowchart TD
    START(["🚀 Mulai"]) --> REG["Registrasi Tenant"] --> LOGIN["Login Admin"] --> PROF["Isi Profil"] --> TYPE{"Jenis Usaha"}
    TYPE -->|Dagang| COA["Setup CoA"]
    TYPE -->|Manufaktur| COA
    TYPE -->|Pertanian| COA
    TYPE -->|Jasa| COA
    COA --> SEED["Seed CoA Template"] --> DEFAULTS["Set Akun Default"] --> MASTER["Input Master Data"]
    MASTER --> PLG["Pelanggan"] & PMS["Pemasok"] & PRD["Persediaan"]
    PLG & PMS & PRD --> USR["Buat User & Role"] --> CBG["Setup Cabang"] --> READY(["✅ Siap"])
                </pre></div>
                <div class="wf-alert wf-alert-important">
                    <span class="wf-alert-icon">📌</span>
                    <div><strong>Template CoA:</strong> Dagang (57 akun) &bull; Manufaktur (+25 produksi) &bull; Pertanian (+19 PSAK 69)</div>
                </div>
            </div>
        </section>

        <!-- ===== S4: PENJUALAN ===== -->
        <section class="wf-section" id="wf-s4">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#28a745,#20c997)">4</div>
                <div><h2>Workflow Penjualan</h2><div class="wf-sub">Penawaran → Faktur → Penerimaan</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
flowchart TD
    subgraph Q["📝 Penawaran — Opsional"]
        PW["Buat Penawaran"] --> PC["Cetak"] --> PV["Konversi ke Faktur"]
    end
    subgraph I["🧾 Faktur"]
        FP["Buat Faktur"] --> FM{"Metode?"}
        FM --> FT["Tunai"] & FK["Kredit"]
    end
    subgraph AJ["⚡ Auto-Jurnal"]
        A1["Dr: Piutang/Kas"] --> A2["Cr: Penjualan"] --> A3["Dr: HPP"] --> A4["Cr: Persediaan"]
        A4 --> SK["📦 Update Stok"] --> PU["💰 Update Piutang"]
    end
    subgraph R["💵 Penerimaan"]
        PR["Buat Penerimaan"] --> PS["Pilih Faktur"] --> PA["Input Bayar"] --> PJ["Auto-Jurnal"]
        PJ --> SC{"Lunas?"} -->|Ya| LN["✅ Lunas"]
        SC -->|Tidak| BL["⏳ Belum"]
    end
    PV --> FP
    FT & FK --> A1
    PU -.->|"Kredit"| PR
    BL -.->|"Bayar lagi"| PR
                </pre></div>
                <h4>📘 Auto-Jurnal Detail</h4>
                <div class="wf-table-wrap"><table>
                    <thead><tr><th>Kondisi</th><th>Debit</th><th>Kredit</th></tr></thead>
                    <tbody>
                        <tr><td><strong>Penjualan Kredit</strong></td><td>Piutang Usaha</td><td>Penjualan Barang</td></tr>
                        <tr><td><strong>Penjualan Tunai</strong></td><td>Kas/Bank</td><td>Penjualan Barang</td></tr>
                        <tr><td><strong>HPP</strong></td><td>HPP</td><td>Persediaan Barang</td></tr>
                        <tr><td><strong>Penerimaan</strong></td><td>Kas/Bank</td><td>Piutang Usaha</td></tr>
                    </tbody>
                </table></div>
            </div>
        </section>

        <!-- ===== S5: PEMBELIAN ===== -->
        <section class="wf-section" id="wf-s5">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#f59e0b,#ef4444)">5</div>
                <div><h2>Workflow Pembelian</h2><div class="wf-sub">RFQ → Faktur → Pembayaran</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
flowchart TD
    subgraph RFQ["📋 RFQ"]
        R1["Buat RFQ"] --> R2["Cetak"] --> R3["Konversi ke Faktur"]
    end
    subgraph FB["🧾 Faktur Pembelian"]
        F1["Buat Faktur"] --> FM{"Metode?"} --> FT["Tunai"] & FK["Kredit"]
    end
    subgraph AJ["⚡ Auto-Jurnal"]
        A1["Dr: Persediaan"] --> A2["Cr: Utang/Kas"] --> SK["📦 Stok Masuk"] --> UU["💰 Update Utang"]
    end
    subgraph PB["💸 Pembayaran"]
        P1["Buat Pembayaran"] --> P2["Pilih Faktur"] --> P3["Input Bayar"] --> P4["Auto-Jurnal"]
        P4 --> SC{"Lunas?"} -->|Ya| LN["✅ Lunas"]
        SC -->|Tidak| BL["⏳ Belum"]
    end
    R3 --> F1
    FT & FK --> A1
    UU -.->|"Kredit"| P1
    BL -.->|"Bayar lagi"| P1
                </pre></div>
            </div>
        </section>

        <!-- ===== S6: RETUR ===== -->
        <section class="wf-section" id="wf-s6">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#f59e0b,#ef4444)">6</div>
                <div><h2>Workflow Retur</h2><div class="wf-sub">Retur Penjualan & Pembelian</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
flowchart LR
    subgraph RJ["🔄 Retur Penjualan"]
        J1["Pilih Faktur"] --> J2["Pilih Barang"] --> J3["Auto-Jurnal"] --> J4["📦 Stok Masuk"]
    end
    subgraph RB["🔄 Retur Pembelian"]
        B1["Pilih Faktur"] --> B2["Pilih Barang"] --> B3["Auto-Jurnal"] --> B4["📦 Stok Keluar"]
    end
                </pre></div>
            </div>
        </section>

        <!-- ===== S7: JURNAL ===== -->
        <section class="wf-section" id="wf-s7">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#3b82f6,#8b5cf6)">7</div>
                <div><h2>Workflow Jurnal Umum</h2><div class="wf-sub">14 sumber auto-jurnal</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
flowchart TD
    START{"Sumber Jurnal"} --> AUTO["⚡ Auto-Jurnal"] & MANUAL["✏️ Manual"] & KAS["💰 Jurnal Kas"]
    AUTO --> LOCKED["🔒 is_locked = true"]
    MANUAL --> AP{"Approval?"} -->|Ya| PENDING["⏳ Pending"] --> APPROVED["✅ Approved"]
    AP -->|Tidak| POSTED["✅ Posted"]
    APPROVED --> POSTED
    KAS --> KJ["Kas Masuk/Keluar"]
    POSTED --> BB["📖 Buku Besar"]
    LOCKED --> BB
                </pre></div>
                <div class="wf-alert wf-alert-tip">
                    <span class="wf-alert-icon">💡</span>
                    <div><strong>Validasi Periode:</strong> Semua transaksi memanggil <code>validatePeriodOpen()</code> untuk memastikan periode masih terbuka.</div>
                </div>
                <h4>📋 Sumber Jurnal Otomatis</h4>
                <div class="wf-table-wrap"><table>
                    <thead><tr><th>Sumber</th><th>Trigger</th><th>Locked?</th></tr></thead>
                    <tbody>
                        <tr><td><code>penjualan</code></td><td>Faktur Penjualan</td><td>✅</td></tr>
                        <tr><td><code>pembelian</code></td><td>Faktur Pembelian</td><td>✅</td></tr>
                        <tr><td><code>penerimaan</code></td><td>Penerimaan Pembayaran</td><td>✅</td></tr>
                        <tr><td><code>pembayaran</code></td><td>Pembayaran Utang</td><td>✅</td></tr>
                        <tr><td><code>retur_penjualan</code></td><td>Retur Penjualan</td><td>✅</td></tr>
                        <tr><td><code>retur_pembelian</code></td><td>Retur Pembelian</td><td>✅</td></tr>
                        <tr><td><code>produksi</code></td><td>Produksi selesai</td><td>✅</td></tr>
                        <tr><td><code>revaluasi</code></td><td>Revaluasi Aset Biologis</td><td>✅</td></tr>
                        <tr><td><code>simpanan</code></td><td>Setor/Tarik Simpanan</td><td>✅</td></tr>
                        <tr><td><code>pinjaman</code></td><td>Pencairan/Angsuran</td><td>✅</td></tr>
                        <tr><td><code>kas</code></td><td>Jurnal Kas Manual</td><td>❌</td></tr>
                        <tr><td><code>manual</code></td><td>Jurnal Umum Manual</td><td>❌</td></tr>
                        <tr><td><code>tutup_buku</code></td><td>Penutupan Periode</td><td>✅</td></tr>
                        <tr><td><code>pos</code></td><td>Transaksi POS</td><td>✅</td></tr>
                    </tbody>
                </table></div>
            </div>
        </section>

        <!-- ===== S8: POS ===== -->
        <section class="wf-section" id="wf-s8">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#8b5cf6,#ec4899)">8</div>
                <div><h2>Workflow Point of Sale</h2><div class="wf-sub">Sesi → Transaksi → Shift Report</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
flowchart TD
    OPEN["📱 Buka Sesi"] --> SALDO["Input Saldo Awal"] --> SEARCH["🔍 Cari Produk"]
    SEARCH --> ADD["Keranjang"] --> TOTAL["Hitung Total"] --> PAY["Bayar"] --> SALE["Simpan"]
    SALE --> AJ["Auto-Jurnal: Dr Kas, Cr Penjualan + HPP"] --> STOK["📦 Update Stok"] --> RECEIPT["🧾 Cetak Struk"]
    RECEIPT -.->|"Baru"| SEARCH
    RECEIPT -.->|"Selesai"| CLOSE["📊 Tutup Shift → Laporan"]
                </pre></div>
            </div>
        </section>

        <!-- ===== S9: KOPERASI ===== -->
        <section class="wf-section" id="wf-s9">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#9b59b6,#8b5cf6)">9</div>
                <div><h2>Workflow Koperasi Simpan Pinjam</h2><div class="wf-sub">Simpanan & Pinjaman Lifecycle</div></div>
            </div>
            <div class="wf-card">
                <h3>👤 Registrasi & Simpanan</h3>
                <div class="wf-diagram"><pre class="mermaid">
flowchart TD
    REG["Input Anggota"] --> NIP["No. Anggota, KTP"] --> KARTU["Cetak Kartu"]
    KARTU --> JS["Pilih Jenis"] --> POKOK["Pokok"] & WAJIB["Wajib"] & SUKARELA["Sukarela"] & DEP["Deposito"]
    POKOK & WAJIB & SUKARELA & DEP --> SETOR["Setor → Auto-Jurnal"]
    SUKARELA & DEP --> TARIK["Tarik → Auto-Jurnal"]
                </pre></div>
            </div>
            <div class="wf-card">
                <h3>💳 Pinjaman Lifecycle</h3>
                <div class="wf-diagram"><pre class="mermaid">
flowchart TD
    CREATE["Buat Permohonan"] --> DATA["Input Data"] --> SIM["Simulasi"] --> SUBMIT["Submit"]
    SUBMIT --> INBOX["Approval Inbox"] --> REVIEW["Review"] --> DEC{"Keputusan?"}
    DEC --> APP["✅ Approved"] --> CAIR["Pencairan + Auto-Jurnal"] --> ACTIVE["Status: Active"]
    DEC --> REJ["❌ Rejected"]
    ACTIVE --> ANG["Bayar Angsuran"] --> SISA["Update Sisa"] --> CHK{"Lunas?"} -->|Ya| DONE["✅ Paid Off"]
    CHK -->|Tidak| ANG
                </pre></div>
            </div>
            <div class="wf-card">
                <h3>📊 Status Flow Pinjaman</h3>
                <div class="wf-diagram"><pre class="mermaid">
stateDiagram-v2
    [*] --> draft: Buat
    draft --> submitted: Submit
    submitted --> approved: Approve
    submitted --> rejected: Reject
    approved --> active: Cairkan
    active --> paid_off: Lunas
    active --> settled: Pelunasan Cepat
    rejected --> [*]
    paid_off --> [*]
    settled --> [*]
                </pre></div>
            </div>
        </section>

        <!-- ===== S10: MANUFAKTUR ===== -->
        <section class="wf-section" id="wf-s10">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#e74c3c,#c0392b)">10</div>
                <div><h2>Workflow Manufaktur</h2><div class="wf-sub">BOM → Produksi → Auto-Jurnal</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
flowchart TD
    BOM["📐 Buat BOM"] --> PROD["Pilih Produk Jadi"] --> MAT["Set Bahan Baku"] --> OH["Set Overhead"]
    OH --> OP["🏭 Buat Order Produksi"] --> QTY["Set Qty"] --> CALC["Kalkulasi Material"] --> EXEC["Eksekusi"]
    EXEC --> DONE["Selesai"] --> AJ["Auto-Jurnal: Dr Barang Jadi, Cr Bahan Baku"]
    AJ --> IN["📦 Stok Jadi +"] & OUT["📦 Stok Bahan −"]
                </pre></div>
            </div>
        </section>

        <!-- ===== S11: PERTANIAN ===== -->
        <section class="wf-section" id="wf-s11">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#1abc9c,#16a085)">11</div>
                <div><h2>Workflow Pertanian PSAK 69</h2><div class="wf-sub">Aset Biologis & Revaluasi</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
flowchart TD
    CREATE["Registrasi Aset"] --> TYPE{"Jenis?"} --> AN["Semusim"] & LS["Ternak"] & FR["Kehutanan"] & BR["Bibit"] & BE["Produktif"]
    AN & LS & FR & BR & BE --> INIT["Nilai Perolehan"]
    INIT --> REVAL["Revaluasi"] --> FV["Nilai Wajar Baru"] --> CALC["Hitung Selisih"]
    CALC --> GL{"Selisih?"} -->|Positif| GAIN["🔺 Keuntungan"] --> LOG["📝 Log + Auto-Jurnal"]
    GL -->|Negatif| LOSS["🔻 Kerugian"] --> LOG
                </pre></div>
            </div>
        </section>

        <!-- ===== S12: ASET TETAP ===== -->
        <section class="wf-section" id="wf-s12">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#f59e0b,#ef4444)">12</div>
                <div><h2>Workflow Aset Tetap</h2><div class="wf-sub">Registrasi → Depresiasi → Pelepasan</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
flowchart TD
    GROUP["Buat Grup"] --> CREATE["Daftarkan Aset"] --> ASSIGN["Assign ke Grup"]
    ASSIGN --> RUN["Penyusutan Bulanan"] --> JOURNAL["Auto-Jurnal: Dr Beban, Cr Akumulasi"]
    ASSIGN -.-> DISPOSE["Pelepasan"] --> M{"Metode?"} --> SELL["Dijual"] & DISCARD["Hapusbuku"]
    SELL & DISCARD --> JD["Auto-Jurnal Pelepasan"]
                </pre></div>
            </div>
        </section>

        <!-- ===== S13: TUTUP BUKU ===== -->
        <section class="wf-section" id="wf-s13">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#e74c3c,#c0392b)">13</div>
                <div><h2>Workflow Tutup Buku</h2><div class="wf-sub">Diagnosa → Closing → Lock</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
flowchart TD
    CHECK["🔍 Diagnosa Neraca"] --> REVIEW["Review Jurnal"] --> FIX["Perbaiki"] --> CREATE["Buat Penutupan"]
    CREATE --> PERIOD["Pilih Periode"] --> PROCESS["Proses"]
    PROCESS --> INCOME["Hitung Pendapatan"] --> EXPENSE["Hitung Beban"] --> CLOSING["Auto-Jurnal Penutupan"]
    CLOSING --> CJ1["Pendapatan → Ikhtisar L/R"] --> CJ2["Beban → Ikhtisar L/R"] --> CJ3["Transfer Laba Ditahan"]
    CJ3 --> LOCK["🔒 Periode Terkunci"]
                </pre></div>
                <div class="wf-alert wf-alert-warning">
                    <span class="wf-alert-icon">⚠️</span>
                    <div>Setelah ditutup, <strong>tidak ada transaksi</strong> yang dapat diubah pada periode tersebut!</div>
                </div>
            </div>
        </section>

        <!-- ===== S14: LAPORAN ===== -->
        <section class="wf-section" id="wf-s14">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#28a745,#20c997)">14</div>
                <div><h2>Laporan Keuangan</h2><div class="wf-sub">Dari Jurnal hingga Financial Statements</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
graph TD
    JU["Jurnal"] --> JD["Detail"] --> BB["Buku Besar"] --> NS["Neraca Saldo"] --> NL["Neraca Lajur"]
    NL --> NERACA["Neraca"] & LR["Laba Rugi"] & AK["Arus Kas"] & PE["Perubahan Ekuitas"]
    BB --> PI["Piutang"] & UT["Utang"] & PER["Persediaan"] & AT["Aset Tetap"] & DIAG["Diagnosa"]
    BB --> OUT["Outstanding"] & KOL["Kolektibilitas"] & SHU["SHU"] & AGING["Aging"]
                </pre></div>
            </div>
        </section>

        <!-- ===== S15: IMPORT/EXPORT ===== -->
        <section class="wf-section" id="wf-s15">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#3b82f6,#8b5cf6)">15</div>
                <div><h2>Import/Export Data</h2><div class="wf-sub">Export, Import & Sync Jurnal</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
flowchart LR
    EX["📤 Export: Pilih Modul → Download"] ~~~ IM["📥 Import: Template → Isi → Upload → Validasi → Proses"]
    IM ~~~ SY["🔄 Sync Jurnal: Regenerasi dari transaksi"]
                </pre></div>
            </div>
        </section>

        <!-- ===== S16: KAS ===== -->
        <section class="wf-section" id="wf-s16">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#f59e0b,#ef4444)">16</div>
                <div><h2>Kas & Transfer</h2><div class="wf-sub">Kas masuk, keluar, dan transfer</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
flowchart LR
    INDEX["📊 Saldo Kas/Bank"] --> MASUK["💰 Kas Masuk"] & KELUAR["💸 Kas Keluar"] & TF["🔄 Transfer"]
    TF --> FROM["Dari Akun A"] --> TO["Ke Akun B"] --> AJ["Auto-Jurnal: Dr B, Cr A"]
                </pre></div>
            </div>
        </section>

        <!-- ===== S17: MULTI-CABANG ===== -->
        <section class="wf-section" id="wf-s17">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#ec4899,#8b5cf6)">17</div>
                <div><h2>Multi-Cabang & Unit Usaha</h2><div class="wf-sub">Data isolation otomatis</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
graph TD
    C1["Cabang Jakarta"] --> U1["Toko Retail"] & U2["Grosir"]
    C2["Cabang Surabaya"] --> U3["Workshop"] & U4["Showroom"]
    ISO["🔒 HasCabang Trait"] --> SCOPE["CabangScope: Auto-filter"] & AUTO["Auto-set id_cabang"]
                </pre></div>
                <div class="wf-alert wf-alert-important">
                    <span class="wf-alert-icon">📌</span>
                    <div><strong>HasCabang:</strong> Auto-filter per cabang, auto-set saat create, hanya lihat data cabang sendiri (kecuali Superuser).</div>
                </div>
            </div>
        </section>

        <!-- ===== S18: AUDIT ===== -->
        <section class="wf-section" id="wf-s18">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#3b82f6,#8b5cf6)">18</div>
                <div><h2>Audit Trail</h2><div class="wf-sub">Pelacakan perubahan otomatis</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
flowchart LR
    M["Model + LogsActivity"] -->|Created| LC["📝 dibuat"]
    M -->|Updated| LU["📝 diubah + diff"]
    M -->|Deleted| LD["📝 dihapus"]
    LC & LU & LD --> AT["audit_trails"] --> V["📋 Viewer"]
                </pre></div>
            </div>
        </section>

        <!-- ===== S19: CROSS-MODULE ===== -->
        <section class="wf-section" id="wf-s19">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#28a745,#20c997)">19</div>
                <div><h2>Cross-Module Interaction</h2><div class="wf-sub">Bagaimana modul saling terhubung</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
graph TB
    subgraph Core["Core Accounting"]
        JU["Jurnal Umum"] --> BB["Buku Besar"]
    end
    PJ["Penjualan"] -->|auto-jurnal| JU
    PB["Pembelian"] -->|auto-jurnal| JU
    PN["Penerimaan"] -->|auto-jurnal| JU
    BY["Pembayaran"] -->|auto-jurnal| JU
    PRD["Produksi"] -->|auto-jurnal| JU
    RV["Revaluasi"] -->|auto-jurnal| JU
    SMP["Simpanan"] -->|auto-jurnal| JU
    PNJ["Pinjaman"] -->|auto-jurnal| JU
    POS["POS"] -->|auto-jurnal| JU
    PJ & PB & PRD & POS -->|stok| KS["Kartu Stok"]
                </pre></div>
            </div>
        </section>

        <!-- ===== S20: SUMMARY ===== -->
        <section class="wf-section" id="wf-s20">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#ff8c00,#e67600)">20</div>
                <div><h2>Summary</h2><div class="wf-sub">Seluruh workflow dalam satu pandangan</div></div>
            </div>
            <div class="wf-summary-card">
                <div class="wf-table-wrap"><table>
                    <thead><tr><th>#</th><th>Modul</th><th>Workflow Utama</th><th>Auto-Jurnal</th><th>Role Min.</th></tr></thead>
                    <tbody>
                        <tr><td>1</td><td><strong>Setup</strong></td><td>Profil → CoA → Master Data</td><td>—</td><td>Admin</td></tr>
                        <tr><td>2</td><td><strong>Penjualan</strong></td><td>Penawaran → Faktur → Penerimaan</td><td>✅</td><td>Staff</td></tr>
                        <tr><td>3</td><td><strong>Pembelian</strong></td><td>RFQ → Faktur → Pembayaran</td><td>✅</td><td>Staff</td></tr>
                        <tr><td>4</td><td><strong>Retur</strong></td><td>Faktur → Input Retur</td><td>✅</td><td>Staff</td></tr>
                        <tr><td>5</td><td><strong>POS</strong></td><td>Shift → Transaksi → Tutup</td><td>✅</td><td>Kasir</td></tr>
                        <tr><td>6</td><td><strong>Jurnal</strong></td><td>Manual / Kas / Auto</td><td>✅</td><td>Staff</td></tr>
                        <tr><td>7</td><td><strong>Koperasi</strong></td><td>Registrasi → Simpanan → Pinjaman</td><td>✅</td><td>Staff</td></tr>
                        <tr><td>8</td><td><strong>Approval</strong></td><td>Submit → Review → Approve</td><td>—</td><td>Manajer</td></tr>
                        <tr><td>9</td><td><strong>Manufaktur</strong></td><td>BOM → Produksi → Selesai</td><td>✅</td><td>Manajer</td></tr>
                        <tr><td>10</td><td><strong>Pertanian</strong></td><td>Registrasi → Revaluasi</td><td>✅</td><td>Manajer</td></tr>
                        <tr><td>11</td><td><strong>Aset Tetap</strong></td><td>Registrasi → Penyusutan</td><td>✅</td><td>Admin</td></tr>
                        <tr><td>12</td><td><strong>Tutup Buku</strong></td><td>Diagnosa → Closing → Lock</td><td>✅</td><td>Admin</td></tr>
                        <tr><td>13</td><td><strong>Laporan</strong></td><td>Neraca, L/R, Arus Kas</td><td>—</td><td>Semua</td></tr>
                        <tr><td>14</td><td><strong>Import/Export</strong></td><td>Template → Upload → Proses</td><td>—</td><td>Manajer</td></tr>
                        <tr><td>15</td><td><strong>Audit Trail</strong></td><td>Auto-log perubahan</td><td>—</td><td>Admin</td></tr>
                        <tr><td>16</td><td><strong>Database</strong></td><td>Truncate / Fresh / Seed</td><td>—</td><td>Superuser</td></tr>
                    </tbody>
                </table></div>
            </div>
            <div class="wf-alert wf-alert-caution">
                <span class="wf-alert-icon">🚨</span>
                <div><strong>Auto-Jurnal:</strong> Semua jurnal otomatis ber-flag <code>is_locked = true</code> dan <strong>tidak bisa diedit langsung</strong>.</div>
            </div>
        </section>

        <!-- ===== S21: ER DIAGRAM ===== -->
        <section class="wf-section" id="wf-s21">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#ec4899,#8b5cf6)">21</div>
                <div><h2>Entity Relationship Map</h2><div class="wf-sub">Hubungan antar entitas</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-diagram"><pre class="mermaid">
erDiagram
    TENANT ||--o{ CABANG : has
    CABANG ||--o{ USER : employs
    CABANG ||--o{ UNIT_USAHA : has
    USER }o--|| ROLE : has
    PELANGGAN ||--o{ PENJUALAN : buys
    PEMASOK ||--o{ PEMBELIAN : supplies
    PENJUALAN }o--|| JURNAL : generates
    PEMBELIAN }o--|| JURNAL : generates
    JURNAL ||--o{ JURNAL_DETAIL : contains
    JURNAL_DETAIL }o--|| AKUN : references
    PERSEDIAAN ||--o{ KARTU_STOK : tracks
    BOM ||--o{ PRODUKSI : produces
    ANGGOTA ||--o{ SIMPANAN : deposits
    ANGGOTA ||--o{ PINJAMAN : borrows
    PINJAMAN ||--o{ PINJAMAN_JADWAL : schedule
    PINJAMAN ||--o{ APPROVAL_HISTORY : approvals
    POS_SESSION ||--o{ PENJUALAN : records
                </pre></div>
            </div>
        </section>

        <!-- ===== S22: PATTERNS ===== -->
        <section class="wf-section" id="wf-s22">
            <div class="wf-section-header">
                <div class="wf-section-num" style="background:linear-gradient(135deg,#3b82f6,#8b5cf6)">22</div>
                <div><h2>Architecture Patterns</h2><div class="wf-sub">Pola arsitektur konsisten</div></div>
            </div>
            <div class="wf-card">
                <div class="wf-table-wrap"><table>
                    <thead><tr><th>Pattern</th><th>Deskripsi</th><th>Digunakan Oleh</th></tr></thead>
                    <tbody>
                        <tr><td><span class="wf-pattern">CheckClosedPeriod</span></td><td>Validasi periode tertutup</td><td>Penjualan, Pembelian, Jurnal, Pinjaman, POS, Retur</td></tr>
                        <tr><td><span class="wf-pattern">CheckSaldoTrait</span></td><td>Cek saldo kas cukup</td><td>Pembayaran, Jurnal</td></tr>
                        <tr><td><span class="wf-pattern">HasCabang</span></td><td>Auto-filter & auto-set cabang</td><td>Semua model transaksional</td></tr>
                        <tr><td><span class="wf-pattern">LogsActivity</span></td><td>Auto-log ke audit trail</td><td>Penjualan, Pembelian, Jurnal, Persediaan</td></tr>
                        <tr><td><span class="wf-pattern">ClearsDashboardCache</span></td><td>Invalidasi cache dashboard</td><td>Penjualan, Pembelian, Jurnal, POS</td></tr>
                        <tr><td><span class="wf-pattern">Reverse-then-Apply</span></td><td>Reverse dampak lalu apply ulang</td><td>Penjualan, Pembelian, Manufaktur</td></tr>
                        <tr><td><span class="wf-pattern">Atomic Number</span></td><td>lockForUpdate() anti-duplikat</td><td>Semua controller transaksi</td></tr>
                        <tr><td><span class="wf-pattern">Quote→Transaction</span></td><td>Konversi dokumen ke transaksi</td><td>Penawaran→Penjualan, RFQ→Pembelian</td></tr>
                    </tbody>
                </table></div>

                <h4>📋 Number Format per Modul</h4>
                <div class="wf-nf-grid">
                    <div class="wf-nf-item"><div class="nf-module">Penjualan</div><div class="nf-format">INV-XXXXX</div><div class="nf-example">INV-00042</div></div>
                    <div class="wf-nf-item"><div class="nf-module">Pembelian</div><div class="nf-format">PUR-XXXXX</div><div class="nf-example">PUR-00015</div></div>
                    <div class="wf-nf-item"><div class="nf-module">Jurnal Manual</div><div class="nf-format">JU-XXXXX</div><div class="nf-example">JU-00103</div></div>
                    <div class="wf-nf-item"><div class="nf-module">Kas Masuk</div><div class="nf-format">KM-XXXXX</div><div class="nf-example">KM-00007</div></div>
                    <div class="wf-nf-item"><div class="nf-module">Kas Keluar</div><div class="nf-format">KK-XXXXX</div><div class="nf-example">KK-00012</div></div>
                    <div class="wf-nf-item"><div class="nf-module">Penawaran</div><div class="nf-format">QTN-XXXXX</div><div class="nf-example">QTN-00003</div></div>
                    <div class="wf-nf-item"><div class="nf-module">RFQ</div><div class="nf-format">RFQ-XXXXX</div><div class="nf-example">RFQ-00005</div></div>
                    <div class="wf-nf-item"><div class="nf-module">POS Sale</div><div class="nf-format">POS-YYYYMMDD-XXXXX</div><div class="nf-example">POS-20260605-00001</div></div>
                    <div class="wf-nf-item"><div class="nf-module">POS Purchase</div><div class="nf-format">POB-YYYYMMDD-XXXXX</div><div class="nf-example">POB-20260605-00001</div></div>
                    <div class="wf-nf-item"><div class="nf-module">Pinjaman</div><div class="nf-format">PIN-YYYY-XXXX</div><div class="nf-example">PIN-2026-0001</div></div>
                    <div class="wf-nf-item"><div class="nf-module">Simpanan</div><div class="nf-format">SIM-YYYYMM-XXXX</div><div class="nf-example">SIM-202606-0001</div></div>
                    <div class="wf-nf-item"><div class="nf-module">Retur Jual</div><div class="nf-format">RJ-YYYYMMDD-XXXX</div><div class="nf-example">RJ-20260605-0001</div></div>
                    <div class="wf-nf-item"><div class="nf-module">Retur Beli</div><div class="nf-format">RB-YYYYMMDD-XXXX</div><div class="nf-example">RB-20260605-0001</div></div>
                    <div class="wf-nf-item"><div class="nf-module">Produksi</div><div class="nf-format">PRD-XXXXX</div><div class="nf-example">PRD-00001</div></div>
                    <div class="wf-nf-item"><div class="nf-module">Revaluasi</div><div class="nf-format">REV-XXXXX</div><div class="nf-example">REV-00001</div></div>
                    <div class="wf-nf-item"><div class="nf-module">Tutup Buku</div><div class="nf-format">CLOSING-YYYY-MM</div><div class="nf-example">CLOSING-2026-06</div></div>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <div style="text-align:center; padding:32px 0; border-top:1px solid rgba(255,255,255,0.06); color:#5a7090; font-size:0.8rem;">
            <p>SimpleAkunting v5 — Laravel 12.x &bull; Bootstrap 5.3 &bull; MySQL 8.0+ &bull; PHP 8.3</p>
            <p style="margin-top:4px;">Terakhir diperbarui: 5 Juni 2026</p>
        </div>

    </div>
</div>

<!-- BACK TO TOP -->
<button class="wf-back-top" id="wfBackTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">↑</button>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/mermaid@11/dist/mermaid.min.js"></script>
<script>
    mermaid.initialize({
        startOnLoad: true,
        theme: 'default',
        themeVariables: {
            primaryColor: '#f1f5f9',
            primaryTextColor: '#0f172a',
            primaryBorderColor: '#cbd5e1',
            lineColor: '#64748b',
            secondaryColor: '#f8fafc',
            tertiaryColor: '#ffffff',
            fontFamily: "'Inter', 'Segoe UI', system-ui, sans-serif",
            fontSize: '12px',
            clusterBkg: 'rgba(2,132,199,0.03)',
            clusterBorder: 'rgba(2,132,199,0.15)',
        },
        flowchart: { curve: 'basis', padding: 14, htmlLabels: true },
        er: { fontSize: 11 }
    });

    // Back to top visibility
    const wfBackTop = document.getElementById('wfBackTop');
    window.addEventListener('scroll', () => {
        wfBackTop.classList.toggle('visible', window.scrollY > 300);
    });

    // Active nav tracking
    const wfSections = document.querySelectorAll('.wf-section');
    const wfNavLinks = document.querySelectorAll('.wf-sidebar a');
    const wfObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                wfNavLinks.forEach(l => l.classList.remove('active'));
                const link = document.querySelector(`.wf-sidebar a[href="#${entry.target.id}"]`);
                if (link) link.classList.add('active');
            }
        });
    }, { rootMargin: '-80px 0px -60% 0px', threshold: 0 });
    wfSections.forEach(s => wfObserver.observe(s));
</script>
@endsection
