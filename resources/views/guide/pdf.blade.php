<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modul Pelatihan Lengkap Simple Akunting v5</title>
    <style>
        @page {
            margin: 1.5cm;
            counter-increment: page;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10.5pt;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #ff8c00;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #ff8c00;
            font-size: 24pt;
        }
        .footer {
            position: fixed;
            bottom: -1cm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .pagenum:before {
            content: counter(page);
        }
        .cover {
            text-align: center;
            padding-top: 100px;
            page-break-after: always;
        }
        .cover img {
            width: 100%;
            max-width: 600px;
            margin: 30px 0;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .cover h1 {
            font-size: 32pt;
            color: #ff8c00;
            margin-bottom: 10px;
        }
        .toc {
            page-break-after: always;
        }
        .toc h2 {
            border-bottom: 2px solid #ff8c00;
            padding-bottom: 10px;
        }
        .toc ul {
            list-style: none;
            padding-left: 0;
        }
        .toc li {
            margin-bottom: 10px;
            border-bottom: 1px dotted #ccc;
        }
        .toc a {
            text-decoration: none;
            color: #333;
        }
        h2 {
            background-color: #fef1e6;
            color: #d35400;
            padding: 10px 15px;
            border-left: 6px solid #ff8c00;
            font-size: 18pt;
            margin-top: 40px;
            page-break-before: always;
        }
        h3 {
            color: #2c3e50;
            border-bottom: 2px solid #eee;
            padding-bottom: 5px;
            margin-top: 25px;
            font-size: 15pt;
        }
        .section-img {
            width: 100%;
            margin: 20px 0;
            text-align: center;
        }
        .section-img img {
            max-width: 90%;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .step-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
        }
        .step-number {
            display: inline-block;
            width: 25px;
            height: 25px;
            background-color: #ff8c00;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 25px;
            margin-right: 10px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .tip {
            background-color: #e3f2fd;
            border-left: 5px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
        }
        .warning {
            background-color: #fff3e0;
            border-left: 5px solid #ff9800;
            padding: 15px;
            margin: 20px 0;
        }
        .icon {
            font-family: DejaVu Sans, sans-serif;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <!-- COVER PAGE -->
    <div class="cover">
        <h1>OPERATIONS GUIDE</h1>
        <p style="font-size: 16pt; color: #666;">Simple Akunting v5 - Versi Enterprise</p>
        <img src="{{ public_path('assets/img/guide/cover.png') }}" alt="Cover Image">
        <div style="margin-top: 50px;">
            <p><strong>Diterbitkan untuk:</strong></p>
            <p style="font-size: 14pt;">{{ $perusahaan->nama_perusahaan ?? 'Manajemen Internal' }}</p>
            <p>{{ $tanggal }}</p>
        </div>
    </div>

    <!-- TABLE OF CONTENTS -->
    <div class="toc">
        <h2>Daftar Isi</h2>
        <ul>
            <li>Chapter 1: Pendahuluan & Konsep Dasar .................................................... 1</li>
            <li>Chapter 2: Persiapan & Konfigurasi Sistem ................................................. 3</li>
            <li>Chapter 3: Manajemen Master Data ............................................................ 5</li>
            <li>Chapter 4: Modul Transaksi & Operasional .................................................. 7</li>
            <li>Chapter 5: Point of Sales (POS) & Retail ..................................................... 9</li>
            <li>Chapter 6: Akuntansi Aset Tetap ................................................................ 11</li>
            <li>Chapter 7: Modul Produksi & Manufaktur ................................................... 13</li>
            <li>Chapter 8: Modul Pertanian (PSAK 69) ....................................................... 15</li>
            <li>Chapter 9: Koperasi Simpan Pinjam ............................................................ 17</li>
            <li>Chapter 10: Pelaporan & Analisis Keuangan ................................................ 19</li>
            <li>Chapter 11: Administrasi & Tutup Buku ...................................................... 21</li>
        </ul>
    </div>

    <!-- CHAPTER 1 -->
    <div class="section">
        <h2><span class="icon">🚀</span> Chapter 1: Pendahuluan & Konsep Dasar</h2>
        <p>Simple Akunting v5 adalah sistem informasi akuntansi terpadu yang dirancang khusus untuk memenuhi kebutuhan UMKM di Indonesia dengan standar SAK EP dan KepKemendesa 136/2022. Aplikasi ini mengusung konsep <em>Real-time Multi-Unit Accounting</em>, di mana setiap transaksi yang diinput akan langsung membentuk laporan keuangan tanpa perlu proses posting manual yang rumit.</p>
        
        <h3>1.1 Arsitektur Sistem</h3>
        <p>Aplikasi ini dibangun dengan arsitektur berbasis web yang memungkinkan akses dari berbagai perangkat (Desktop, Tablet, Mobile) secara bersamaan. Keamanan data dijaga melalui enkripsi basis data dan sistem <em>Audit Trail</em> yang mencatat setiap aktivitas pengguna.</p>
        
        <div class="tip">
            <strong>Kunci Utama:</strong> Setiap transaksi dalam Simple Akunting v5 mengikuti prinsip <em>Single Entry - Multi Effect</em>. Anda cukup menginput satu bukti transaksi, dan sistem akan mengupdate stok, piutang, hutang, kas, dan jurnal secara otomatis.
        </div>

        <h3>1.2 Navigasi Utama</h3>
        <p>Menu utama dibagi menjadi beberapa kategori besar yang mencerminkan siklus bisnis:</p>
        <ul>
            <li><strong>Core:</strong> Dashboard dan pengaturan data master.</li>
            <li><strong>Transaksi:</strong> Alur kas masuk, kas keluar, dan mutasi internal.</li>
            <li><strong>Operasional:</strong> Modul khusus Manufaktur dan Pertanian.</li>
            <li><strong>Laporan:</strong> Ringkasan eksekutif dan detail finansial.</li>
        </ul>
    </div>

    <!-- CHAPTER 2 -->
    <div class="section">
        <h2><span class="icon">⚙</span> Chapter 2: Persiapan & Konfigurasi Sistem</h2>
        <p>Sebelum memulai operasional harian, sangat penting bagi Admin untuk melakukan konfigurasi awal agar sistem berjalan sesuai dengan profil bisnis Anda.</p>
        
        <h3>2.1 Profil Perusahaan</h3>
        <div class="step-box">
            <span class="step-number">1</span> Buka menu <strong>Admin > Profil Perusahaan</strong>.<br>
            <span class="step-number">2</span> Isi Nama Perusahaan, Alamat Lengkap, dan Nomor Kontak.<br>
            <span class="step-number">3</span> Pilih <strong>Jenis Usaha</strong>. Pilihan ini akan menentukan template akun (COA) yang akan dimuat oleh sistem.
        </div>

        <h3>2.2 Pengaturan Akun (Chart of Accounts)</h3>
        <p>Chart of Accounts (COA) adalah urat nadi akuntansi. Sistem telah menyediakan template standar, namun Anda harus memastikan bahwa setiap akun memiliki <em>Saldo Normal</em> (Debit atau Kredit) yang tepat.</p>
        <table>
            <tr>
                <th>Kelompok Akun</th>
                <th>Kode Awalan</th>
                <th>Saldo Normal</th>
            </tr>
            <tr>
                <td>Harta (Aset)</td>
                <td>1</td>
                <td>Debit</td>
            </tr>
            <tr>
                <td>Kewajiban (Utang)</td>
                <td>2</td>
                <td>Kredit</td>
            </tr>
            <tr>
                <td>Modal (Ekuitas)</td>
                <td>3</td>
                <td>Kredit</td>
            </tr>
            <tr>
                <td>Pendapatan</td>
                <td>4</td>
                <td>Kredit</td>
            </tr>
            <tr>
                <td>Beban</td>
                <td>5 - 6</td>
                <td>Debit</td>
            </tr>
        </table>
    </div>

    <!-- CHAPTER 3 -->
    <div class="section">
        <h2><span class="icon">📦</span> Chapter 3: Manajemen Master Data</h2>
        <p>Master Data adalah kumpulan data statis yang menjadi referensi bagi transaksi. Kualitas laporan keuangan Anda ditentukan oleh kebersihan data master ini.</p>
        
        <h3>3.1 Manajemen Pelanggan & Pemasok</h3>
        <p>Pastikan setiap entitas memiliki kategori yang jelas. Untuk pelanggan, Anda dapat menentukan <em>Plafon Kredit</em> untuk mencegah penjualan berlebih kepada pelanggan yang menunggak.</p>
        
        <h3>3.2 Master Persediaan (Inventory)</h3>
        <p>Modul persediaan mendukung beberapa tipe barang:</p>
        <ul>
            <li><strong>Barang Dagangan:</strong> Barang yang dibeli untuk dijual kembali tanpa modifikasi.</li>
            <li><strong>Bahan Baku:</strong> Komponen untuk proses manufaktur.</li>
            <li><strong>Barang Jadi:</strong> Hasil akhir dari proses produksi.</li>
            <li><strong>Aset Biologis:</strong> Makhluk hidup (hewan/tanaman) dalam modul pertanian.</li>
        </ul>
        <div class="warning">
            Jangan menghapus master barang yang sudah memiliki riwayat transaksi, karena akan menyebabkan kerusakan pada laporan kartu stok. Gunakan fitur "Non-aktifkan" jika barang tidak lagi dijual.
        </div>
    </div>

    <!-- CHAPTER 4 -->
    <div class="section">
        <h2><span class="icon">💸</span> Chapter 4: Modul Transaksi & Operasional</h2>
        <p>Bab ini membahas alur pencatatan harian yang paling sering digunakan dalam aplikasi.</p>
        
        <h3>4.1 Alur Penjualan (Accounts Receivable)</h3>
        <p>Transaksi penjualan dapat dilakukan secara tunai maupun kredit. Saat Anda menyimpan faktur penjualan, sistem melakukan:</p>
        <ol>
            <li>Debit Kas/Piutang.</li>
            <li>Kredit Pendapatan Penjualan.</li>
            <li>Debit Beban Pokok Penjualan (HPP).</li>
            <li>Kredit Persediaan (Stok Berkurang).</li>
        </ol>

        <h3>4.2 Alur Pembelian (Accounts Payable)</h3>
        <p>Pencatatan barang masuk dari supplier. Penting untuk selalu melampirkan nomor faktur fisik dari supplier ke dalam sistem untuk memudahkan rekonsiliasi utang di akhir bulan.</p>

        <h3>4.3 Manajemen Kas & Bank</h3>
        <p>Fitur <strong>Mutasi Kas</strong> sangat vital untuk mencatat perpindahan uang, misalnya dari Bank ke Kas Kecil (Petty Cash). Pastikan saldo di aplikasi selalu sama dengan saldo di buku tabungan bank Anda.</p>
    </div>

    <!-- CHAPTER 5 -->
    <div class="section">
        <h2><span class="icon">🛒</span> Chapter 5: Point of Sales (POS) & Retail</h2>
        <p>Untuk bisnis retail, antarmuka POS menyediakan kecepatan transaksi dengan dukungan barcode scanner.</p>
        
        <h3>5.1 Manajemen Shift</h3>
        <p>Keamanan kas kasir dijaga melalui sistem shift. Kasir tidak dapat memulai transaksi tanpa menginput "Modal Awal". Di akhir shift, sistem akan meminta input "Uang Fisik" untuk mendeteksi selisih (shortage/overage).</p>
        
        <div class="step-box">
            <strong>Prosedur Tutup Kasir:</strong><br>
            1. Hitung seluruh uang di laci kasir.<br>
            2. Masukkan angka tersebut ke form Tutup Shift.<br>
            3. Jika ada selisih, sistem akan mencatatnya ke akun "Selisih Kasir" secara otomatis.
        </div>
    </div>

    <!-- CHAPTER 6 -->
    <div class="section">
        <h2><span class="icon">🏢</span> Chapter 6: Akuntansi Aset Tetap</h2>
        <p>Aset tetap adalah investasi jangka panjang. Simple Akunting v5 menangani penyusutan secara otomatis menggunakan metode Garis Lurus (Straight Line).</p>
        
        <h3>6.1 Pendaftaran Aset</h3>
        <p>Setiap aset harus didaftarkan dengan <em>Harga Perolehan</em> dan <em>Tanggal Perolehan</em>. Sistem akan menghitung akumulasi penyusutan sejak tanggal tersebut hingga hari ini.</p>
        
        <h3>6.2 Proses Depresiasi Bulanan</h3>
        <p>Anda cukup menekan satu tombol di menu Aset Tetap tiap akhir bulan. Sistem akan menjurnal beban penyusutan untuk semua aset aktif sekaligus.</p>
    </div>

    <!-- CHAPTER 7 -->
    <div class="section">
        <h2><span class="icon">🏭</span> Chapter 7: Modul Produksi & Manufaktur</h2>
        <p>Modul ini dirancang untuk menghitung harga pokok produksi secara akurat.</p>
        
        <h3>7.1 Bill of Materials (BOM)</h3>
        <p>BOM mendefinisikan standar penggunaan bahan. Misal: Untuk membuat 1 Meja, dibutuhkan 2 meter kayu, 4 buah baut, dan 1 kaleng cat. BOM ini menjadi acuan otomatis saat Anda melakukan produksi massal.</p>
        
        <h3>7.2 Produksi & WIP</h3>
        <p>Saat produksi dimulai, status barang adalah WIP (Work In Process). Nilai bahan baku dipindahkan dari akun Persediaan Bahan ke akun Persediaan Dalam Proses. Setelah selesai, barulah dipindahkan ke Persediaan Barang Jadi.</p>
    </div>

    <!-- CHAPTER 8 -->
    <div class="section">
        <h2><span class="icon">🌿</span> Chapter 8: Modul Pertanian (PSAK 69)</h2>
        <p>Pertanian memiliki keunikan karena asetnya tumbuh (Biological Transformation). Simple Akunting v5 mendukung PSAK 69 berbasis Nilai Wajar (Fair Value).</p>
        
        <h3>8.1 Revaluasi Aset Biologis</h3>
        <p>Contoh: Anak sapi yang lahir memiliki nilai awal. Setelah 6 bulan, beratnya bertambah sehingga nilainya naik. Kenaikan nilai ini diinput melalui menu Revaluasi, dan sistem akan mengakui laba dari pertumbuhan biologis tersebut.</p>
    </div>

    <!-- CHAPTER 9 -->
    <div class="section">
        <h2><span class="icon">🤝</span> Chapter 9: Koperasi Simpan Pinjam</h2>
        <p>Modul ini menangani sisi perbankan mikro dari koperasi.</p>
        <ul>
            <li><strong>Manajemen Simpanan:</strong> Pencatatan setoran dan penarikan tabungan anggota.</li>
            <li><strong>Kredit/Pinjaman:</strong> Menghitung angsuran pokok dan bunga secara otomatis berdasarkan tenor yang disepakati.</li>
            <li><strong>Kolektibilitas:</strong> Laporan penuaan pinjaman untuk memantau kredit macet (Lancar, Kurang Lancar, Diragukan, Macet).</li>
        </ul>
    </div>

    <!-- CHAPTER 10 -->
    <div class="section">
        <h2><span class="icon">📊</span> Chapter 10: Pelaporan & Analisis Keuangan</h2>
        <p>Tujuan akhir dari setiap sistem akuntansi adalah laporan yang akurat untuk pengambilan keputusan.</p>
        
        <div class="section-img">
            <img src="{{ public_path('assets/img/guide/reports.png') }}" alt="Financial Reports">
        </div>

        <h3>10.1 Laporan Neraca (Balance Sheet)</h3>
        <p>Menampilkan posisi keuangan perusahaan. Aset harus selalu sama dengan Liabilitas + Ekuitas.</p>
        
        <h3>10.2 Laporan Laba Rugi</h3>
        <p>Menampilkan performa operasional. Apakah bisnis Anda menghasilkan laba atau menderita rugi dalam periode tertentu.</p>
    </div>

    <!-- CHAPTER 11 -->
    <div class="section">
        <h2><span class="icon">🔒</span> Chapter 11: Administrasi & Tutup Buku</h2>
        <p>Keamanan data dan integritas periode akuntansi dikelola di sini.</p>
        
        <h3>11.1 User & Role Management</h3>
        <p>Tentukan hak akses secara spesifik. Misalnya: Admin Cabang A tidak boleh melihat data Cabang B.</p>
        
        <h3>11.2 Tutup Buku (Closing)</h3>
        <p>Lakukan tutup buku setiap akhir tahun. Proses ini akan mengenolkan akun-akun pendapatan dan beban, lalu memindahkan selisihnya ke akun Laba Ditahan (Retained Earnings).</p>
    </div>

    <div class="footer">
        Halaman <span class="pagenum"></span> | Panduan Pengoperasian Simple Akunting v5 | &copy; {{ date('Y') }} Solusi Group
    </div>
</body>
</html>