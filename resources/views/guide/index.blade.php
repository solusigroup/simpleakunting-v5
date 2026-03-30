@extends('layouts.app')

@section('title', 'Panduan Pengoperasian - Simple Akunting v5')

@section('content')
<div class="row">
    <div class="col-12 col-xl-3 mb-4 sticky-xl-top" style="top: 80px; height: calc(100vh - 100px); overflow-y: auto; padding-bottom: 20px;">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h6 class="text-uppercase fw-bold text-muted small mb-3">Daftar Isi</h6>
                <nav id="guide-nav" class="nav flex-column gap-2 small">
                    <a class="nav-link p-0 text-dark fw-medium" href="#intro">🚀 Pendahuluan</a>
                    <a class="nav-link p-0 text-dark fw-medium" href="#dashboard">🏠 Dashboard</a>
                    <a class="nav-link p-0 text-dark fw-medium" href="#master">📦 Master Data</a>
                    <a class="nav-link p-0 text-dark fw-medium" href="#transaksi">💸 Transaksi Keuangan</a>
                    <a class="nav-link p-0 text-dark fw-medium" href="#kasbank">🏦 Kas & Bank</a>
                    <a class="nav-link p-0 text-dark fw-medium" href="#pos">🛒 Point of Sales</a>
                    <a class="nav-link p-0 text-dark fw-medium" href="#koperasi">🏦 Simpan Pinjam</a>
                    <a class="nav-link p-0 text-dark fw-medium" href="#mfg">🏭 Manufaktur</a>
                    <a class="nav-link p-0 text-dark fw-medium" href="#agri">🌱 Pertanian</a>
                    <a class="nav-link p-0 text-dark fw-medium" href="#report">📊 Laporan & Analisis</a>
                    <a class="nav-link p-0 text-dark fw-medium" href="#admin">⚙️ Administrasi</a>
                </nav>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-9">
        <!-- Pendahuluan -->
        <section id="intro" class="mb-5 pt-2">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    <h2 class="fw-bold mb-4">🚀 Pendahuluan</h2>
                    <p class="lead text-muted">Selamat datang di Panduan Pengoperasian Simple Akunting v5. Aplikasi ini dirancang untuk memudahkan pengelolaan keuangan bisnis Anda dengan standar akuntansi yang benar namun tetap mudah digunakan.</p>
                    <div class="alert alert-primary border-0 rounded-4 p-4 mt-4 bg-opacity-10" style="background-color: rgba(139, 92, 246, 0.1);">
                        <div class="d-flex gap-3">
                            <span class="fs-4">💡</span>
                            <div>
                                <h6 class="fw-bold text-primary mb-1">Tips Navigasi</h6>
                                <p class="mb-0 text-dark opacity-75 small">Gunakan menu di sebelah kiri (atau atas pada perangkat mobile) untuk melompat ke modul yang ingin Anda pelajari.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dashboard -->
        <section id="dashboard" class="mb-5 pt-2">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h2 class="fw-bold mb-4">🏠 Dashboard</h2>
                    <p>Dashboard adalah pusat informasi ringkas tentang kondisi kesehatan keuangan perusahaan Anda secara real-time.</p>
                    <ul class="list-unstyled mt-4 d-flex flex-column gap-3">
                        <li class="d-flex gap-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; color: #8b5cf6;">1</div>
                            <div>
                                <h6 class="fw-bold mb-1">Status Kas/Bank</h6>
                                <p class="text-muted small">Melihat saldo akhir seluruh akun kas dan bank yang aktif.</p>
                            </div>
                        </li>
                        <li class="d-flex gap-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; color: #8b5cf6;">2</div>
                            <div>
                                <h6 class="fw-bold mb-1">Grafik Arus Kas</h6>
                                <p class="text-muted small">Visualisasi uang masuk dan keluar dalam beberapa bulan terakhir.</p>
                            </div>
                        </li>
                        <li class="d-flex gap-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; color: #8b5cf6;">3</div>
                            <div>
                                <h6 class="fw-bold mb-1">Piutang & Hutang Jatuh Tempo</h6>
                                <p class="text-muted small">Pengingat transaksi yang harus segera diselesaikan atau ditagih.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Master Data -->
        <section id="master" class="mb-5 pt-2">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h2 class="fw-bold mb-4">📦 Master Data</h2>
                    <p>Fondasi dari seluruh transaksi. Pastikan data master diisi dengan benar sebelum memulai pencatatan transaksi.</p>
                    
                    <div class="row g-4 mt-2">
                        <div class="col-md-6">
                            <div class="p-4 rounded-4 border border-light h-100 bg-light bg-opacity-25">
                                <h6 class="fw-bold mb-2">👥 Pelanggan & Pemasok</h6>
                                <p class="small text-muted mb-0">Mencatat data pihak luar yang berinteraksi dengan bisnis Anda. Diperlukan untuk modul Penjualan dan Pembelian.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-4 rounded-4 border border-light h-100 bg-light bg-opacity-25">
                                <h6 class="fw-bold mb-2">📦 Persediaan</h6>
                                <p class="small text-muted mb-0">Mengelola daftar produk, stok awal, harga beli, dan harga jual.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-4 rounded-4 border border-light h-100 bg-light bg-opacity-25">
                                <h6 class="fw-bold mb-2">🔢 Akun (COA)</h6>
                                <p class="small text-muted mb-0">Pengaturan kode akun akuntansi. Gunakan struktur yang tersedia atau sesuaikan dengan kebutuhan pelaporan Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Transaksi -->
        <section id="transaksi" class="mb-5 pt-2">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h2 class="fw-bold mb-4">💸 Transaksi Keuangan</h2>
                    
                    <div class="mb-4">
                        <h5 class="fw-bold text-primary">🛒 Penjualan & Pembelian</h5>
                        <p>Digunakan untuk mencatat transaksi barang/jasa. Sistem akan otomatis membentuk jurnal akuntansi dan memutasi stok barang.</p>
                        <div class="bg-light p-3 rounded-3 mt-2 border-start border-4 border-primary">
                            <p class="mb-0 small fw-medium">Alur: Input Data → Simpan → (Otomatis) Jurnal Baru → Update Stok.</p>
                        </div>
                    </div>

                    <div>
                        <h5 class="fw-bold text-primary">📝 Jurnal Umum</h5>
                        <p>Digunakan untuk mencatat transaksi yang tidak tersedia di modul khusus, seperti biaya penyusutan, koreksi, atau setoran modal.</p>
                        <p class="text-danger small fw-bold mt-2">Penting: Total Debit harus selalu sama dengan Total Kredit.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Kas & Bank -->
        <section id="kasbank" class="mb-5 pt-2">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h2 class="fw-bold mb-4">🏦 Kas & Bank</h2>
                    <div class="accordion accordion-flush" id="kasAccordion">
                        <div class="accordion-item bg-transparent">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed px-0 bg-transparent fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#kas-penerimaan">
                                    📥 Penerimaan Kas (Biaya/Non-Penjualan)
                                </button>
                            </h2>
                            <div id="kas-penerimaan" class="accordion-collapse collapse" data-bs-parent="#kasAccordion">
                                <div class="accordion-body px-0 py-3 small text-muted">
                                    Mencatat uang masuk selain dari pelunasan piutang pelanggan. Contoh: Pendapatan Bunga Bank, Modal Pemilik.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item bg-transparent">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed px-0 bg-transparent fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#kas-pembayaran">
                                    📤 Pembayaran Kas (Biaya/Non-Pembelian)
                                </button>
                            </h2>
                            <div id="kas-pembayaran" class="accordion-collapse collapse" data-bs-parent="#kasAccordion">
                                <div class="accordion-body px-0 py-3 small text-muted">
                                    Mencatat pengeluaran uang untuk biaya operasional. Contoh: Bayar Listrik, Gaji Karyawan, Sewa Kantor.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item bg-transparent">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed px-0 bg-transparent fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#kas-transfer">
                                    🔄 Mutasi Kas (Transfer Antar Rekening)
                                </button>
                            </h2>
                            <div id="kas-transfer" class="accordion-collapse collapse" data-bs-parent="#kasAccordion">
                                <div class="accordion-body px-0 py-3 small text-muted">
                                    Mencatat perpindahan uang antar akun kas/bank, misalnya Tarik Tunai dari Bank ke Kas Kecil.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Point of Sales -->
        <section id="pos" class="mb-5 pt-2">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden border-top border-4 border-warning">
                <div class="card-body p-4 p-md-5">
                    <h2 class="fw-bold mb-4">🛒 Point of Sales (POS)</h2>
                    <p>Modul khusus untuk penjualan kasir dengan antarmuka yang cepat dan mendukung layar sentuh/barcode scanner.</p>
                    <div class="row g-4 mt-3">
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <div class="h3 mb-2">🔓</div>
                                <h6 class="fw-bold">1. Buka Shift</h6>
                                <p class="extra-small text-muted">Mulai sesi kasir dengan menginput modal awal.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <div class="h3 mb-2">💳</div>
                                <h6 class="fw-bold">2. Input Transaksi</h6>
                                <p class="extra-small text-muted">Pilih produk, tentukan pembayaran (Tunai/Non-Tunai).</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <div class="h3 mb-2">🔒</div>
                                <h6 class="fw-bold">3. Tutup Shift</h6>
                                <p class="extra-small text-muted">Rekonsiliasi uang fisik dengan laporan sistem.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Simpan Pinjam -->
        <section id="koperasi" class="mb-5 pt-2">
            <div class="card border-0 shadow-sm rounded-4 border-top border-4 border-info">
                <div class="card-body p-4 p-md-5">
                    <h2 class="fw-bold mb-4">🏦 Simpan Pinjam</h2>
                    <p>Modul khusus pengoperasian koperasi atau lembaga keuangan mikro.</p>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item bg-transparent py-3 border-light">
                            <strong>Manajemen Anggota:</strong> Pendaftaran anggota baru dan pencetakan kartu anggota.
                        </li>
                        <li class="list-group-item bg-transparent py-3 border-light">
                            <strong>Tabungan/Simpanan:</strong> Pencatatan setoran, penarikan, dan perhitungan bunga otomatis.
                        </li>
                        <li class="list-group-item bg-transparent py-3 border-light">
                            <strong>Kredit/Pinjaman:</strong> Simulasi angsuran, persetujuan (approval) berjenjang, dan pencairan dana.
                        </li>
                        <li class="list-group-item bg-transparent py-3 border-light">
                            <strong>Kolektibilitas:</strong> Monitoring pinjaman macet dan penuaan hutang (aging).
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Manufaktur -->
        <section id="mfg" class="mb-5 pt-2">
            <div class="card border-0 shadow-sm rounded-4 border-top border-4 border-secondary">
                <div class="card-body p-4 p-md-5">
                    <h2 class="fw-bold mb-4">🏭 Manufaktur</h2>
                    <p>Modul untuk mencatat proses produksi barang (barang mentah menjadi barang jadi).</p>
                    <div class="p-4 bg-muted rounded-4 bg-light border border-light">
                        <h6 class="fw-bold small text-muted text-uppercase mb-3">Feature Utama:</h6>
                        <div class="d-flex flex-column gap-2 small">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary rounded-pill">BoM</span>
                                <span><strong>Bill of Materials:</strong> Formula bahan baku yang dibutuhkan untuk memproduksi 1 unit produk.</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary rounded-pill">Prod</span>
                                <span><strong>Produksi:</strong> Pencatatan harian proses produksi untuk memotong stok bahan baku dan menambah stok barang jadi.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pertanian -->
        <section id="agri" class="mb-5 pt-2">
            <div class="card border-0 shadow-sm rounded-4 border-top border-4 border-success">
                <div class="card-body p-4 p-md-5">
                    <h2 class="fw-bold mb-4">🌱 Pertanian (PSAK 69)</h2>
                    <p>Pencatatan aset biologis sesuai standar akuntansi internasional (Fair Value).</p>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 border rounded-3 bg-success bg-opacity-10 border-success border-opacity-25">
                                <h6 class="fw-bold mb-1 small text-success">Pendaftaran Aset</h6>
                                <p class="extra-small mb-0 opacity-75">Input bibit/hewan baru ke dalam sistem sebagai aset.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 border rounded-3 bg-success bg-opacity-10 border-success border-opacity-25">
                                <h6 class="fw-bold mb-1 small text-success">Revaluasi Wajar</h6>
                                <p class="extra-small mb-0 opacity-75">Update kenaikan nilai aset akibat pertumbuhan tanpa perlu transaksi jual beli.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Laporan -->
        <section id="report" class="mb-5 pt-2">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h2 class="fw-bold mb-4">📊 Laporan & Analisis</h2>
                    <p>Seluruh transaksi yang Anda input akan otomatis membentuk laporan berikut:</p>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-light small">
                            <thead class="bg-light">
                                <tr>
                                    <th>Kategori</th>
                                    <th>Kegunaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold">Laporan Neraca</td>
                                    <td>Melihat posisi Harta (Aset), Hutang, dan Modal saat ini.</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Laba Rugi</td>
                                    <td>Melihat keuntungan atau kerugian dalam periode tertentu.</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Arus Kas</td>
                                    <td>Menganalisis sumber dan penggunaan uang tunai.</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Buku Besar</td>
                                    <td>Rincian pergerakan angka pada setiap akun akuntansi.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- Admin -->
        <section id="admin" class="mb-5 pt-2 pb-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h2 class="fw-bold mb-4">⚙️ Administrasi</h2>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold">👥 Manajemen User</h6>
                            <p class="small text-muted">Menambah karyawan dan membatasi hak akses (Kasir hanya bisa POS, dst).</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">🏢 Profil Perusahaan</h6>
                            <p class="small text-muted">Mengatur Nama, Alamat, Logo, dan pilihan jenis usaha.</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">🛡️ Audit Trail</h6>
                            <p class="small text-muted">Melihat log aktivitas (siapa mengubah apa dan kapan) untuk keamanan data.</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">💾 Database Management</h6>
                            <p class="small text-muted">Fitur lanjutan untuk membersihkan data atau seeding data awal (Hanya Superuser).</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<style>
    #guide-nav .nav-link {
        padding: 8px 12px !important;
        border-radius: 8px;
        transition: all 0.2s;
    }
    #guide-nav .nav-link:hover {
        background: rgba(139, 92, 246, 0.05);
        color: #8b5cf6 !important;
    }
    .sticky-xl-top {
        z-index: 100;
    }
    section {
        scroll-margin-top: 80px;
    }
    .bg-muted {
        background-color: #f8f9fa;
    }
    .extra-small {
        font-size: 0.75rem;
    }
</style>

@endsection

@section('scripts')
<script>
    // Smooth scroll for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Active state highlighting on scroll
    window.addEventListener('scroll', () => {
        let current = '';
        const sections = document.querySelectorAll('section');
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (pageYOffset >= sectionTop - 120) {
                current = section.getAttribute('id');
            }
        });

        document.querySelectorAll('#guide-nav .nav-link').forEach(link => {
            link.classList.remove('bg-primary-subtle', 'text-primary');
            link.style.backgroundColor = '';
            link.style.color = '';
            if (link.getAttribute('href').substring(1) === current) {
                link.style.backgroundColor = 'rgba(139, 92, 246, 0.1)';
                link.style.color = '#8b5cf6';
            }
        });
    });
</script>
@endsection
