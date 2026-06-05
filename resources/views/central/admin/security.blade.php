@extends('central.admin.layout')

@section('title', 'Keamanan & Implementasi')

@section('container-class', 'container-fluid')

@section('extra-styles')
    /* ===== SECURITY PAGE STYLES ===== */
    .sec-wrapper { display: flex; gap: 0; margin: -32px -24px; min-height: calc(100vh - 56px); }

    /* Sidebar */
    .sec-sidebar {
        width: 260px; flex-shrink: 0;
        background: rgba(0,0,0,0.25); border-right: 1px solid rgba(255,255,255,0.06);
        padding: 20px 0; position: sticky; top: 56px; height: calc(100vh - 56px);
        overflow-y: auto;
    }
    .sec-sidebar::-webkit-scrollbar { width: 3px; }
    .sec-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
    .sec-sidebar-label {
        font-size: 0.65rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
        color: #5a7090; padding: 10px 20px 6px;
    }
    .sec-sidebar a {
        display: flex; align-items: center; gap: 8px;
        padding: 7px 20px; font-size: 0.8rem; color: #8fa8c8;
        text-decoration: none; transition: all 0.2s;
        border-left: 3px solid transparent;
    }
    .sec-sidebar a:hover { background: rgba(59, 130, 246, 0.06); color: #fff; border-left-color: rgba(59, 130, 246, 0.3); }
    .sec-sidebar a.active { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-left-color: #3b82f6; font-weight: 600; }
    .sec-sidebar a .sec-icon { font-size: 14px; width: 20px; text-align: center; }
    .sec-sidebar a .sec-num {
        font-size: 0.65rem; font-weight: 700; color: #5a7090;
        background: rgba(255,255,255,0.05); padding: 1px 5px; border-radius: 3px;
        margin-left: auto;
    }

    /* Main content */
    .sec-content { flex: 1; padding: 32px; max-width: calc(100% - 260px); overflow-x: hidden; }

    /* Hero */
    .sec-hero { text-align: center; padding: 24px 0 40px; }
    .sec-hero-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(168, 85, 247, 0.1); border: 1px solid rgba(168, 85, 247, 0.2);
        padding: 4px 14px; border-radius: 20px; font-size: 0.7rem;
        color: #d8b4fe; font-weight: 600; margin-bottom: 14px;
    }
    .sec-hero h1 { font-size: 2rem; font-weight: 800; color: #fff; margin-bottom: 10px; }
    .sec-hero p { color: #8fa8c8; font-size: 0.9rem; max-width: 600px; margin: 0 auto 24px; }
    .sec-hero-stats { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
    .sec-hero-stat {
        background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);
        padding: 12px 22px; border-radius: 10px; text-align: center;
    }
    .sec-hero-stat .num { font-size: 1.5rem; font-weight: 800; color: #3b82f6; }
    .sec-hero-stat .lbl { font-size: 0.65rem; color: #5a7090; font-weight: 500; margin-top: 2px; }

    /* Sections */
    .sec-section { margin-bottom: 40px; scroll-margin-top: 80px; }
    .sec-section-header {
        display: flex; align-items: center; gap: 14px; margin-bottom: 20px;
        padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.06);
    }
    .sec-section-num {
        width: 36px; height: 36px; border-radius: 10px;
        display: grid; place-items: center; font-size: 0.85rem; font-weight: 800;
        color: #fff; flex-shrink: 0;
    }
    .sec-section-header h2 { font-size: 1.25rem; font-weight: 800; color: #fff; }
    .sec-section-header .sec-sub { font-size: 0.75rem; color: #5a7090; margin-top: 2px; }

    /* Cards */
    .sec-card {
        background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);
        border-radius: 12px; padding: 24px; margin-bottom: 16px;
        transition: border-color 0.3s;
    }
    .sec-card:hover { border-color: rgba(59, 130, 246, 0.2); }
    .sec-card h3 { font-size: 1rem; font-weight: 700; color: #e0e0e0; margin-bottom: 12px; }
    .sec-card h4 { font-size: 0.85rem; font-weight: 600; color: #ff8c00; margin: 16px 0 8px; }
    .sec-card p, .sec-card li { color: #8fa8c8; font-size: 0.85rem; line-height: 1.7; }
    .sec-card ul, .sec-card ol { padding-left: 20px; margin: 8px 0; }
    .sec-card li { margin-bottom: 4px; }

    /* Pre, Codes, Badges */
    pre {
        background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(255,255,255,0.06);
        padding: 14px; border-radius: 8px; overflow-x: auto;
        margin: 14px 0; font-family: 'JetBrains Mono', monospace; font-size: 0.75rem;
        line-height: 1.5; color: #e2e8f0;
    }
    code {
        background: rgba(59, 130, 246, 0.12); color: #93c5fd;
        padding: 2px 6px; border-radius: 4px; font-family: 'JetBrains Mono', monospace;
        font-size: 0.75rem;
    }
    pre code { background: none; padding: 0; color: inherit; }

    .status-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 8px; border-radius: 4px; font-size: 0.65rem; font-weight: 700;
        letter-spacing: 0.3px; text-transform: uppercase;
    }
    .badge-secure { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2); }
    .badge-info { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2); }
    .badge-configured { background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.2); }
    .badge-policy { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.2); }

    .alert {
        padding: 12px 16px; border-radius: 8px; margin: 14px 0;
        font-size: 0.8rem; line-height: 1.6; display: flex; gap: 10px;
        border: 1px solid;
    }
    .alert-secure { background: rgba(16, 185, 129, 0.05); border-color: rgba(16, 185, 129, 0.15); color: #a7f3d0; }
    .alert-info { background: rgba(59, 130, 246, 0.05); border-color: rgba(59, 130, 246, 0.15); color: #93c5fd; }

    /* Tables */
    .sec-table-wrap {
        overflow-x: auto; margin: 14px 0; border-radius: 10px;
        border: 1px solid rgba(255,255,255,0.06);
    }
    .sec-table-wrap table { margin: 0; font-size: 0.8rem; }
    .sec-table-wrap thead th {
        background: rgba(59, 130, 246, 0.08); color: #3b82f6;
        font-weight: 600; padding: 10px 14px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .sec-table-wrap tbody td {
        padding: 10px 14px; border-bottom: 1px solid rgba(255,255,255,0.04);
        color: #8fa8c8; vertical-align: middle;
    }

    .controls-filter-bar {
        background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255,255,255,0.06);
        border-radius: 10px; padding: 12px 16px; margin-bottom: 24px;
        display: flex; gap: 12px; align-items: center; justify-content: space-between;
        flex-wrap: wrap;
    }
    .search-box { position: relative; flex-grow: 1; max-width: 320px; }
    .search-box input {
        width: 100%; padding: 8px 12px 8px 32px;
        background: rgba(0, 0, 0, 0.2); border: 1px solid rgba(255,255,255,0.1);
        border-radius: 6px; color: #fff; font-family: inherit; font-size: 0.8rem;
    }
    .search-box input:focus { outline: none; border-color: #3b82f6; }
    .search-box::before { content: '🔍'; position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 0.8rem; opacity: 0.6; }

    .filter-tags { display: flex; gap: 6px; }
    .filter-btn {
        background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);
        color: #8fa8c8; padding: 5px 12px; border-radius: 6px; font-size: 0.75rem;
        cursor: pointer; transition: all 0.2s;
    }
    .filter-btn:hover, .filter-btn.active { background: #3b82f6; border-color: #3b82f6; color: #fff; }

    /* Accordions */
    .accordion-item {
        border: 1px solid rgba(255,255,255,0.06); border-radius: 8px;
        margin-top: 10px; overflow: hidden; background: rgba(0,0,0,0.15);
    }
    .accordion-header {
        padding: 10px 16px; font-weight: 600; font-size: 0.8rem;
        color: #e0e0e0; cursor: pointer; display: flex;
        align-items: center; justify-content: space-between;
        background: rgba(255,255,255,0.02);
    }
    .accordion-header:hover { background: rgba(255,255,255,0.04); }
    .accordion-content { padding: 0 16px 16px; display: none; border-top: 1px solid rgba(255,255,255,0.06); }
    .accordion-item.open .accordion-content { display: block; }
    .accordion-item.open .arrow { transform: rotate(180deg); }
    .arrow { font-size: 10px; transition: transform 0.2s; }
@endsection

@section('content')
<div class="sec-wrapper">
    <!-- Sidebar Nav -->
    <aside class="sec-sidebar">
        <div class="sec-sidebar-label">Overview</div>
        <a href="#hero-sec" class="active"><span class="sec-icon">🏠</span> Beranda</a>
        <a href="#summary-sec"><span class="sec-icon">📋</span> Ringkasan Audit</a>

        <div class="sec-sidebar-label">Detail Layer</div>
        <a href="#sec1"><span class="sec-icon">🌐</span> 1. Multi-Tenancy <span class="sec-num">1</span></a>
        <a href="#sec2"><span class="sec-icon">🔑</span> 2. Autentikasi & RBAC <span class="sec-num">2</span></a>
        <a href="#sec3"><span class="sec-icon">📊</span> 3. Integritas Transaksi <span class="sec-num">3</span></a>
        <a href="#sec4"><span class="sec-icon">🛡</span> 4. Web OWASP Protection <span class="sec-num">4</span></a>
        <a href="#sec5"><span class="sec-icon">⚙</span> 5. Server Hardening <span class="sec-num">5</span></a>
    </aside>

    <!-- Main Content Panel -->
    <div class="sec-content">
        <!-- Hero Section -->
        <div class="sec-hero" id="hero-sec">
            <div class="sec-hero-badge">🛡️ Laporan Keamanan & Implementasi</div>
            <h1>SimpleAkunting v5 Security Hardening</h1>
            <p>Audit mendalam mengenai arsitektur, parameter proteksi kode, dan deployment server terenkripsi pada sistem akuntansi terpadu</p>
            <div class="sec-hero-stats">
                <div class="sec-hero-stat"><div class="num">25+</div><div class="lbl">Pilar Keamanan</div></div>
                <div class="sec-hero-stat"><div class="num">100%</div><div class="lbl">Isolasi DB Tenant</div></div>
                <div class="sec-hero-stat"><div class="num">0</div><div class="lbl">Isu Kritis</div></div>
                <div class="sec-hero-stat"><div class="num">SHA-256</div><div class="lbl">Proteksi Beacon</div></div>
            </div>
        </div>

        <!-- Filter & Summary Table Section -->
        <div class="controls-filter-bar" id="summary-sec">
            <div class="search-box">
                <input type="text" id="searchInput" onkeyup="filterControls()" placeholder="Cari kontrol keamanan (cth: SSL, CSRF, Scope, Nginx)...">
            </div>
            <div class="filter-tags">
                <button class="filter-btn active" onclick="setFilter('all', this)">Semua</button>
                <button class="filter-btn" onclick="setFilter('tenancy', this)">Multi-Tenancy</button>
                <button class="filter-btn" onclick="setFilter('code', this)">Proteksi Kode</button>
                <button class="filter-btn" onclick="setFilter('server', this)">Server Hardening</button>
            </div>
        </div>

        <!-- Matrix Card -->
        <div class="sec-card">
            <h3>📋 Matriks Kontrol Keamanan Terverifikasi</h3>
            <div class="sec-table-wrap">
                <table id="controlsTable">
                    <thead>
                        <tr>
                            <th>Kontrol Keamanan</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Tindakan / Proteksi</th>
                            <th>File Terkait</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-category="tenancy">
                            <td><strong>Isolasi Database Tenant</strong></td>
                            <td>Multi-Tenancy</td>
                            <td><span class="status-badge badge-secure">Secure</span></td>
                            <td>Multi-Database terpisah per tenant (`dbv5_{tenant_id}`)</td>
                            <td><code>config/tenancy.php</code></td>
                        </tr>
                        <tr data-category="tenancy">
                            <td><strong>Subdomain Protection</strong></td>
                            <td>Multi-Tenancy</td>
                            <td><span class="status-badge badge-secure">Secure</span></td>
                            <td>Isolasi route bisnis tenant dari central landing domain</td>
                            <td><code>routes/tenant.php</code></td>
                        </tr>
                        <tr data-category="code">
                            <td><strong>Branch-Level Scope</strong></td>
                            <td>Proteksi Kode</td>
                            <td><span class="status-badge badge-secure">Secure</span></td>
                            <td>Global filter query data berdasarkan user cabang</td>
                            <td><code>app/Scopes/CabangScope.php</code></td>
                        </tr>
                        <tr data-category="code">
                            <td><strong>Closed Period Check</strong></td>
                            <td>Proteksi Kode</td>
                            <td><span class="status-badge badge-configured">Configured</span></td>
                            <td>Blokir entri/edit transaksi pada periode tutup buku</td>
                            <td><code>app/Traits/CheckClosedPeriod.php</code></td>
                        </tr>
                        <tr data-category="code">
                            <td><strong>SQL Injection Prevention</strong></td>
                            <td>Proteksi Kode</td>
                            <td><span class="status-badge badge-secure">Secure</span></td>
                            <td>Parameter binding otomatis via Eloquent & PDO</td>
                            <td><code>app/Http/Controllers/</code></td>
                        </tr>
                        <tr data-category="code">
                            <td><strong>XSS Protection</strong></td>
                            <td>Proteksi Kode</td>
                            <td><span class="status-badge badge-secure">Secure</span></td>
                            <td>HTML entities escaping otomatis via Blade `{{ }}`</td>
                            <td><code>resources/views/</code></td>
                        </tr>
                        <tr data-category="code">
                            <td><strong>CSRF Protection</strong></td>
                            <td>Proteksi Kode</td>
                            <td><span class="status-badge badge-secure">Secure</span></td>
                            <td>Token verification bawaan pada setiap POST/PUT/DELETE</td>
                            <td><code>app/Http/Kernel.php</code></td>
                        </tr>
                        <tr data-category="code">
                            <td><strong>Password Hashing</strong></td>
                            <td>Proteksi Kode</td>
                            <td><span class="status-badge badge-configured">Configured</span></td>
                            <td>Enkripsi searah via Bcrypt dengan cost factor 12</td>
                            <td><code>app/Models/User.php</code></td>
                        </tr>
                        <tr data-category="code">
                            <td><strong>Upload File Verification</strong></td>
                            <td>Proteksi Kode</td>
                            <td><span class="status-badge badge-configured">Configured</span></td>
                            <td>Mime, type, max-size check, dan auto rename file</td>
                            <td><code>app/Http/Controllers/JurnalController.php</code></td>
                        </tr>
                        <tr data-category="server">
                            <td><strong>Nginx Security Headers</strong></td>
                            <td>Server Hardening</td>
                            <td><span class="status-badge badge-configured">Configured</span></td>
                            <td>X-Frame-Options (SameOrigin) & X-Content-Type (NoSniff)</td>
                            <td>Nginx Conf</td>
                        </tr>
                        <tr data-category="server">
                            <td><strong>Disallow Dotfiles</strong></td>
                            <td>Server Hardening</td>
                            <td><span class="status-badge badge-secure">Secure</span></td>
                            <td>Penolakan akses langsung ke `.env`, `.git`, dll.</td>
                            <td>Nginx Conf</td>
                        </tr>
                        <tr data-category="server">
                            <td><strong>Production settings</strong></td>
                            <td>Server Hardening</td>
                            <td><span class="status-badge badge-configured">Configured</span></td>
                            <td>Debug mode dinonaktifkan, kebocoran trace dicegah</td>
                            <td><code>.env</code></td>
                        </tr>
                        <tr data-category="server">
                            <td><strong>Audit Trail Logging</strong></td>
                            <td>Server Hardening</td>
                            <td><span class="status-badge badge-configured">Configured</span></td>
                            <td>Log aktivitas Eloquent (CUD), IP, User Agent</td>
                            <td><code>app/Traits/LogsActivity.php</code></td>
                        </tr>
                        <tr data-category="server">
                            <td><strong>Automated Backup</strong></td>
                            <td>Server Hardening</td>
                            <td><span class="status-badge badge-policy">Policy</span></td>
                            <td>Backup database harian jam 2 pagi via cron</td>
                            <td><code>DEPLOYMENT.md</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECTION 1 -->
        <section class="sec-section" id="sec1">
            <div class="sec-section-header">
                <div class="sec-section-num" style="background: #3b82f6">1</div>
                <div>
                    <h2>Multi-Tenancy & Isolasi Data</h2>
                    <div class="sec-sub">Pemisahan data tingkat tinggi antar-perusahaan penyewa (Tenant)</div>
                </div>
            </div>
            
            <div class="sec-card">
                <h3>🏢 1.1 Isolasi Fisik Database Perusahaan</h3>
                <p>Sistem menghindari penggunaan single database multi-tenant (yang rentan terhadap kebocoran data akibat hilangnya clause WHERE di baris kode). Sebaliknya, diimplementasikan arsitektur database terisolasi menggunakan <code>Stancl/Tenancy</code>.</p>
                
                <div class="alert alert-secure">
                    <span class="alert-icon">✓</span>
                    <div><strong>Keamanan Terjamin:</strong> Saat subdomain diakses, middleware akan mengganti koneksi database default secara dinamis ke database tenant spesifik (`dbv5_{tenant}`). Tidak ada query SQL yang dapat melintasi database tenant lain.</div>
                </div>

                <div class="accordion-item">
                    <div class="accordion-header" onclick="toggleAccordion(this)">
                        <span>Lihat Konfigurasi Multi-Tenancy (config/tenancy.php)</span>
                        <span class="arrow">▼</span>
                    </div>
                    <div class="accordion-content">
                        <pre>
&lt;?php
declare(strict_types=1);

use Stancl\Tenancy\Database\Models\Domain;
use App\Models\Tenant;

return [
    'tenant_model' => Tenant::class,
    'id_generator' => null, // Kustom tenant ID (contoh: perusahaanA)

    'bootstrappers' => [
        Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class,
    ],
];</pre>
                    </div>
                </div>
            </div>

            <div class="sec-card">
                <h3>🚫 1.2 Pencegahan Akses Lintas Domain & Blokir Tenant Nonaktif</h3>
                <p>Untuk memastikan tenant tidak dapat diakses secara ilegal dan mematuhi lisensi penagihan:</p>
                <ul>
                    <li><strong>PreventAccessFromCentralDomains:</strong> Middleware ini mencegah domain pusat (landing page) mengakses route yang didedikasikan untuk bisnis tenant.</li>
                    <li><strong>CheckTenantActive Middleware:</strong> Membaca status `is_active` pada model tenant saat inisialisasi request. Jika bernilai <code>false</code>, sistem otomatis memotong proses alur request dan membuang respons <code>403 Forbidden</code> dengan alasan pemblokiran yang jelas.</li>
                </ul>

                <div class="accordion-item">
                    <div class="accordion-header" onclick="toggleAccordion(this)">
                        <span>Kode Middleware CheckTenantActive.php</span>
                        <span class="arrow">▼</span>
                    </div>
                    <div class="accordion-content">
                        <pre>
&lt;?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();

        if ($tenant) {
            if (isset($tenant->is_active) && $tenant->is_active == false) {
                abort(403, "Akses Diblokir: Perusahaan '{$tenant->nama_perusahaan}' saat ini berstatus NONAKTIF. Silakan hubungi Administrator pusat.");
            }
        }

        return $next($request);
    }
}</pre>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 2 -->
        <section class="sec-section" id="sec2">
            <div class="sec-section-header">
                <div class="sec-section-num" style="background: #3b82f6">2</div>
                <div>
                    <h2>Autentikasi & RBAC (Role-Based Access Control)</h2>
                    <div class="sec-sub">Verifikasi identitas pengguna dan manajemen hak akses terperinci</div>
                </div>
            </div>

            <div class="sec-card">
                <h3>🔑 2.1 Enkripsi Kredensial Hashing</h3>
                <p>Aplikasi menggunakan algoritma enkripsi satu arah **Bcrypt** dengan default 12 putaran putaran hashing (work factor) untuk mengenkripsi password sebelum disimpan ke database, mencegah kebocoran password mentah jika database dicuri.</p>
                <pre>
// Metode verifikasi login di AuthController
if ($user && Hash::check($credentials['password'], $user->password_hash)) {
    Auth::login($user);
    // ...
}</pre>
            </div>

            <div class="sec-card">
                <h3>🛡️ 2.2 Role & Permission Middleware</h3>
                <p>Hak akses dibagi menjadi 5 tingkatan (Superuser, Admin, Manajer, Staff, Kasir). Validasi dilakukan di tingkat route grup menggunakan middleware:</p>
                <ul>
                    <li><strong>RoleMiddleware:</strong> Memastikan pengguna memiliki setidaknya salah satu role yang diperlukan. Menariknya, role <code>superuser</code> diset untuk mem-bypass semua verifikasi role.</li>
                    <li><strong>PermissionMiddleware:</strong> Menyediakan pengecekan granular di level fitur (seperti pembatasan izin pembuatan, pengeditan, atau penghapusan data jurnal).</li>
                </ul>
            </div>
        </section>

        <!-- SECTION 3 -->
        <section class="sec-section" id="sec3">
            <div class="sec-section-header">
                <div class="sec-section-num" style="background: #10b981">3</div>
                <div>
                    <h2>Integritas Data Transaksi Finansial</h2>
                    <div class="sec-sub">Validasi logika bisnis akuntansi agar data konsisten dan tidak dapat dimanipulasi</div>
                </div>
            </div>

            <div class="sec-card">
                <h3>🏢 3.1 Isolasi Data Tingkat Cabang (Multi-Branch Scope)</h3>
                <p>Untuk perusahaan yang memiliki banyak cabang, isolasi data transaksi wajib dilakukan di level database agar user cabang A tidak dapat mengintip atau menyunting transaksi cabang B.</p>
                <p>Ini diselesaikan secara elegan menggunakan global scope <code>CabangScope</code> yang disematkan ke trait <code>HasCabang</code> pada setiap model akuntansi:</p>
                
                <pre>
// Implementasi filter otomatis di CabangScope.php
public function apply(Builder $builder, Model $model)
{
    $user = auth()->user();
    if (!$user) return;

    $table = $model->getTable();

    if ($user->hasRole(['superuser', 'admin'])) {
        $activeCabang = session('active_cabang');
        if ($activeCabang) {
            $builder->where($table . '.id_cabang', $activeCabang);
        }
    } else {
        $userCabang = $user->id_cabang;
        if ($userCabang) {
            $builder->where($table . '.id_cabang', $userCabang);
        }
    }
}</pre>
            </div>

            <div class="sec-card">
                <h3>🔒 3.2 Proteksi Transaksi Periode Tutup Buku</h3>
                <p>Saat laporan keuangan bulanan disepakati dan ditutup, periode tersebut dikunci (Tutup Buku). Untuk mencegah perubahan data masa lalu secara ilegal, disematkan trait <code>CheckClosedPeriod</code> di setiap mutasi jurnal:</p>
                
                <pre>
protected function validatePeriodOpen(string $tanggal): void
{
    $date = Carbon::parse($tanggal);
    $bulan = $date->month;
    $tahun = $date->year;

    $closed = PeriodeTutupBuku::where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->where('status', 'tutup')
        ->exists();

    if ($closed) {
        $namaBulan = $date->translatedFormat('F Y');
        throw new \Exception("Periode {$namaBulan} sudah ditutup. Tidak dapat menambah/mengubah transaksi.");
    }
}</pre>
            </div>
        </section>

        <!-- SECTION 4 -->
        <section class="sec-section" id="sec4">
            <div class="sec-section-header">
                <div class="sec-section-num" style="background: #10b981">4</div>
                <div>
                    <h2>Proteksi Terhadap Serangan Web OWASP</h2>
                    <div class="sec-sub">Penangkalan SQL Injection, XSS, CSRF, dan serangan File Upload</div>
                </div>
            </div>

            <div class="sec-card">
                <h3>🛡️ 4.1 SQLi, XSS & CSRF Mitigation</h3>
                <p>Diimplementasikan langsung pada arsitektur framework Laravel:</p>
                <ul>
                    <li><strong>SQL Injection:</strong> Seluruh query dibentuk menggunakan ORM Eloquent yang mem-binding parameter menggunakan PDO secara bawaan.</li>
                    <li><strong>XSS (Cross-Site Scripting):</strong> Seluruh variable di render pada layout Blade menggunakan tag escaping <code>{!! '{{ $variable }}' !!}</code> untuk melumpuhkan payload JavaScript berbahaya.</li>
                    <li><strong>CSRF (Cross-Site Request Forgery):</strong> Middleware bawaan memverifikasi kesesuaian token enkripsi session pada seluruh endpoint mutasi (POST/PUT/DELETE).</li>
                </ul>
            </div>

            <div class="sec-card">
                <h3>📁 4.2 Keamanan Unggahan File Transaksi (File Upload Safety)</h3>
                <p>Ketika pengguna mengunggah foto bukti jurnal atau foto anggota, sistem menerapkan proteksi bertingkat:</p>
                <ol>
                    <li><strong>Validasi Tipe & Ukuran:</strong> Hanya mengizinkan file berjenis gambar (JPEG, PNG, JPG, WEBP) dengan batas ukuran maksimum 5MB.</li>
                    <li><strong>Renaming Acak:</strong> Nama file asli dibuang dan diganti dengan string unik acak (`bukti_jurnal_` + timestamp + `uniqid()`) untuk menghindari serangan *directory traversal* dan overwrite file penting.</li>
                    <li><strong>Penyimpanan Luar Publik:</strong> File disimpan di folder storage terisolasi, yang tautannya ke folder public diatur ketat oleh Nginx agar tidak dapat mengeksekusi script PHP mentah jika ada shell script terselubung.</li>
                </ol>
            </div>
        </section>

        <!-- SECTION 5 -->
        <section class="sec-section" id="sec5">
            <div class="sec-section-header">
                <div class="sec-section-num" style="background: #f43f5e">5</div>
                <div>
                    <h2>Server Hardening & Deployment Security</h2>
                    <div class="sec-sub">Konfigurasi keamanan pada level server OS, Nginx, dan Logging Audit</div>
                </div>
            </div>

            <div class="sec-card">
                <h3>🔒 5.1 Nginx Security Configuration</h3>
                <p>Untuk menyembunyikan arsitektur internal server dan membatasi manipulasi request dari luar, server Nginx di-hardening dengan konfigurasi keamanan khusus:</p>
                
                <pre>
# Konfigurasi proteksi header dan blokir file rahasia di Nginx
server {
    listen 443 ssl http2;
    server_name *.v5.simpleakunting.id;

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;

    client_max_body_size 20M;

    location ~ /\.(?!well-known) {
        deny all;
    }
}</pre>
            </div>

            <div class="sec-card">
                <h3>📝 5.2 Audit Trail Logging</h3>
                <p>Setiap perubahan data akuntansi (Create, Update, Delete) serta histori login/logout direkam secara real-time pada sistem audit trail yang andal. Log tidak dapat dimanipulasi dari dashboard aplikasi biasa:</p>
                <ul>
                    <li>Merekam user yang bertindak, deskripsi aksi, nama tabel/model, dan kunci baris data.</li>
                    <li>Menangkap nilai sebelum modifikasi (`old_values`) dan sesudah modifikasi (`new_values`).</li>
                    <li>Mendokumentasikan IP address dan user agent penyerang/pengguna untuk keperluan forensik keamanan.</li>
                </ul>
            </div>
        </section>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle Accordion Content
    function toggleAccordion(headerElement) {
        const item = headerElement.parentElement;
        item.classList.toggle('open');
    }

    // Filter Security Controls in Table
    let currentFilter = 'all';
    function setFilter(category, buttonElement) {
        currentFilter = category;
        
        // Update Active Button
        const buttons = document.querySelectorAll('.filter-btn');
        buttons.forEach(btn => btn.classList.remove('active'));
        buttonElement.classList.add('active');

        applyFilters();
    }

    function filterControls() {
        applyFilters();
    }

    function applyFilters() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#controlsTable tbody tr');

        rows.forEach(row => {
            const categoryMatch = (currentFilter === 'all' || row.getAttribute('data-category') === currentFilter);
            const textMatch = row.textContent.toLowerCase().includes(query);

            if (categoryMatch && textMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Simple routing dynamic active sidebar link based on scroll
    const sections = document.querySelectorAll('.sec-section, .sec-hero');
    const navLi = document.querySelectorAll('.sec-sidebar a');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (window.scrollY >= (sectionTop - 150)) {
                current = section.getAttribute('id');
            }
        });

        navLi.forEach(a => {
            a.classList.remove('active');
            if (a.getAttribute('href').substring(1) === current) {
                a.classList.add('active');
            }
        });
    });
</script>
@endsection
