<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\PersediaanController;
use App\Http\Controllers\JenisPinjamanController;
use App\Http\Controllers\JenisSimpananController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController; 
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\ManufacturingController;
use App\Http\Controllers\AgricultureController;
use App\Http\Controllers\AccountingPeriodeController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\UnitUsahaController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\CabangSessionController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\GuideController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| These routes are loaded only when TENANCY_ENABLED=true.
| They are wrapped with tenant identification middleware.
|
*/

Route::middleware([
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
    'web',
    \App\Http\Middleware\CheckTenantActive::class,
])->group(function () {

    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        
        // =====================================================
        // DASHBOARD - All authenticated users
        // =====================================================
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // =====================================================
        // MASTER DATA - Read access for manajer+, Write access for admin+
        // =====================================================
        
        Route::middleware('role:superuser,admin')->group(function () {
            Route::get('pelanggan/create', [PelangganController::class, 'create'])->name('pelanggan.create');
            Route::post('pelanggan', [PelangganController::class, 'store'])->name('pelanggan.store');
            Route::get('pelanggan/{pelanggan}/edit', [PelangganController::class, 'edit'])->name('pelanggan.edit');
            Route::put('pelanggan/{pelanggan}', [PelangganController::class, 'update'])->name('pelanggan.update');
            Route::delete('pelanggan/{pelanggan}', [PelangganController::class, 'destroy'])->name('pelanggan.destroy');
            
            Route::get('pemasok/create', [PemasokController::class, 'create'])->name('pemasok.create');
            Route::post('pemasok', [PemasokController::class, 'store'])->name('pemasok.store');
            Route::get('pemasok/{pemasok}/edit', [PemasokController::class, 'edit'])->name('pemasok.edit');
            Route::put('pemasok/{pemasok}', [PemasokController::class, 'update'])->name('pemasok.update');
            Route::delete('pemasok/{pemasok}', [PemasokController::class, 'destroy'])->name('pemasok.destroy');
            
            Route::get('persediaan/create', [PersediaanController::class, 'create'])->name('persediaan.create');
            Route::post('persediaan', [PersediaanController::class, 'store'])->name('persediaan.store');
            Route::get('persediaan/{persediaan}/edit', [PersediaanController::class, 'edit'])->name('persediaan.edit');
            Route::put('persediaan/{persediaan}', [PersediaanController::class, 'update'])->name('persediaan.update');
            Route::delete('persediaan/{persediaan}', [PersediaanController::class, 'destroy'])->name('persediaan.destroy');
            
            Route::get('akun/create', [AkunController::class, 'create'])->name('akun.create');
            Route::post('akun', [AkunController::class, 'store'])->name('akun.store');
            Route::get('akun/{akun}/edit', [AkunController::class, 'edit'])->name('akun.edit');
            Route::put('akun/{akun}', [AkunController::class, 'update'])->name('akun.update');
            Route::delete('akun/{akun}', [AkunController::class, 'destroy'])->name('akun.destroy');
            
            Route::resource('jenis-pinjaman', JenisPinjamanController::class);
            Route::resource('jenis-simpanan', JenisSimpananController::class);
        });

        Route::middleware('role:superuser,admin,manajer')->group(function () {
            Route::get('pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
            Route::get('pelanggan/{pelanggan}', [PelangganController::class, 'show'])->name('pelanggan.show');
            Route::get('pemasok', [PemasokController::class, 'index'])->name('pemasok.index');
            Route::get('pemasok/{pemasok}', [PemasokController::class, 'show'])->name('pemasok.show');
            Route::get('persediaan', [PersediaanController::class, 'index'])->name('persediaan.index');
            Route::get('akun', [AkunController::class, 'index'])->name('akun.index');
        });

        // =====================================================
        // POINT OF SALES - Kasir, Staff, Manajer, Admin, Superuser
        // =====================================================
        Route::middleware('role:superuser,admin,manajer,staff,kasir')->group(function () {
            Route::get('pos', [PosController::class, 'index'])->name('pos.index');
            Route::get('pos/search', [PosController::class, 'searchProduct'])->name('pos.search');
            Route::post('pos/sale', [PosController::class, 'storeSale'])->name('pos.store.sale');
            Route::post('pos/purchase', [PosController::class, 'storePurchase'])->name('pos.store.purchase');
            Route::get('pos/session', [PosController::class, 'sessionCreate'])->name('pos.session.create');
            Route::post('pos/session/open', [PosController::class, 'sessionOpen'])->name('pos.session.open');
            Route::post('pos/session/close', [PosController::class, 'sessionClose'])->name('pos.session.close');
            Route::get('pos/receipt/{id}', [PosController::class, 'receipt'])->name('pos.receipt');
            Route::get('pos/purchase-receipt/{id}', [PosController::class, 'purchaseReceipt'])->name('pos.purchase.receipt');
            Route::get('pos/shift-report/{id}', [PosController::class, 'shiftReport'])->name('pos.shift.report');
        });

        // =====================================================
        // TRANSAKSI - Staff level and above
        // =====================================================
        Route::middleware('role:superuser,admin,manajer,staff')->group(function () {
            Route::resource('penjualan', PenjualanController::class);
            Route::resource('pembelian', PembelianController::class);
            Route::resource('jurnal', JurnalController::class);
            Route::resource('penerimaan', PenerimaanController::class);
            Route::resource('pembayaran', PembayaranController::class);
            Route::get('kas', [KasController::class, 'index'])->name('kas.index');
            Route::get('kas/transfer', [KasController::class, 'transfer'])->name('kas.transfer');
            Route::post('kas/transfer', [KasController::class, 'storeTransfer'])->name('kas.storeTransfer');
        });

        // =====================================================
        // LAPORAN - Semua role (termasuk staff)
        // =====================================================
        Route::middleware('role:superuser,admin,manajer,staff,kasir')->group(function () {
            Route::get('bukubesar', [BukuBesarController::class, 'index'])->name('bukubesar.index');
            Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
            Route::get('/laporan/neraca', [LaporanController::class, 'neraca'])->name('laporan.neraca');
            Route::get('/laporan/neraca/pdf', [LaporanController::class, 'neracaPdf'])->name('laporan.neraca.pdf');
            Route::get('/laporan/labarugi', [LaporanController::class, 'labaRugi'])->name('laporan.labarugi');
            Route::get('/laporan/labarugi/pdf', [LaporanController::class, 'labaRugiPdf'])->name('laporan.labarugi.pdf');
            Route::get('/laporan/aruskas-langsung', [LaporanController::class, 'arusKasLangsung'])->name('laporan.aruskas_langsung');
            Route::get('/laporan/aruskas-tidak-langsung', [LaporanController::class, 'arusKasTidakLangsung'])->name('laporan.aruskas_tidak_langsung');
            Route::get('/laporan/perubahan-ekuitas', [LaporanController::class, 'perubahanEkuitas'])->name('laporan.perubahan_ekuitas');
            Route::get('/laporan/persediaan', [LaporanController::class, 'persediaan'])->name('laporan.persediaan');
            Route::get('/laporan/mutasi-persediaan', [LaporanController::class, 'mutasiPersediaan'])->name('laporan.mutasi_persediaan');
        });

        // =====================================================
        // PENGATURAN - Admin, Superuser
        // =====================================================
        Route::middleware('role:superuser,admin')->group(function () {
            Route::get('perusahaan', [PerusahaanController::class, 'edit'])->name('perusahaan.edit');
            Route::put('perusahaan', [PerusahaanController::class, 'update'])->name('perusahaan.update');
            Route::resource('users', UserController::class);
            Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
            Route::resource('roles', RoleController::class);
            Route::resource('cabang', CabangController::class);
            Route::resource('unit-usaha', UnitUsahaController::class);
            Route::post('cabang/switch', [CabangSessionController::class, 'switch'])->name('cabang.switch');
            Route::get('audit-trail', [\App\Http\Controllers\AuditTrailController::class, 'index'])->name('audit-trail.index');
        });

        // API: Cascade dropdown unit usaha by cabang (all authenticated)
        Route::get('api/unit-usaha/cabang/{cabangId}', [UnitUsahaController::class, 'getByCabang'])->name('api.unit-usaha.by-cabang');

        // =====================================================
        // KOPERASI SIMPAN PINJAM - Staff level and above
        // =====================================================
        Route::middleware('role:superuser,admin,manajer,staff')->group(function () {
            Route::resource('anggota', \App\Http\Controllers\AnggotaController::class);
            Route::get('anggota/{id}/kartu', [\App\Http\Controllers\AnggotaController::class, 'kartu'])->name('anggota.kartu');

            Route::resource('simpanan', \App\Http\Controllers\SimpananController::class);
            Route::get('simpanan-setor', [\App\Http\Controllers\SimpananController::class, 'setor'])->name('simpanan.setor');
            Route::get('simpanan-tarik', [\App\Http\Controllers\SimpananController::class, 'tarik'])->name('simpanan.tarik');
            Route::get('simpanan-kartu/{id_anggota}', [\App\Http\Controllers\SimpananController::class, 'kartu'])->name('simpanan.kartu');

            Route::post('pinjaman/simulasi', [\App\Http\Controllers\PinjamanController::class, 'simulasi'])->name('pinjaman.simulasi');
            Route::resource('pinjaman', \App\Http\Controllers\PinjamanController::class);
            Route::post('pinjaman/{id}/submit', [\App\Http\Controllers\PinjamanController::class, 'submit'])->name('pinjaman.submit');
            Route::get('pinjaman/{id}/pencairan', [\App\Http\Controllers\PinjamanController::class, 'pencairanForm'])->name('pinjaman.pencairan');
            Route::post('pinjaman/{id}/cairkan', [\App\Http\Controllers\PinjamanController::class, 'cairkan'])->name('pinjaman.cairkan');
            Route::get('pinjaman/{id}/angsuran', [\App\Http\Controllers\PinjamanController::class, 'angsuranForm'])->name('pinjaman.angsuran');
            Route::post('pinjaman/{id}/bayar', [\App\Http\Controllers\PinjamanController::class, 'bayarAngsuran'])->name('pinjaman.bayar');
            Route::get('pinjaman/{id}/pelunasan', [\App\Http\Controllers\PinjamanController::class, 'pelunasanForm'])->name('pinjaman.pelunasan');
            Route::post('pinjaman/{id}/lunasi', [\App\Http\Controllers\PinjamanController::class, 'lunasi'])->name('pinjaman.lunasi');
        });

        Route::middleware('role:superuser,admin,manajer')->group(function () {
            Route::get('approval', [\App\Http\Controllers\ApprovalController::class, 'inbox'])->name('approval.inbox');
            Route::post('approval/{module}/{id}/approve', [\App\Http\Controllers\ApprovalController::class, 'approve'])->name('approval.approve');
            Route::post('approval/{module}/{id}/reject', [\App\Http\Controllers\ApprovalController::class, 'reject'])->name('approval.reject');
        });

        Route::middleware('role:superuser,admin,manajer')->group(function () {
            Route::get('laporan/simpanan', [LaporanController::class, 'laporanSimpanan'])->name('laporan.simpanan');
            Route::get('laporan/pinjaman-aktif', [LaporanController::class, 'laporanPinjamanAktif'])->name('laporan.pinjaman_aktif');
            Route::get('laporan/kolektibilitas', [LaporanController::class, 'laporanKolektibilitas'])->name('laporan.kolektibilitas');
            Route::get('laporan/aging', [LaporanController::class, 'laporanAgingPinjaman'])->name('laporan.aging');
            Route::get('laporan/outstanding-simpan-pinjam', [LaporanController::class, 'outstandingSimpanPinjam'])->name('laporan.outstanding_simpan_pinjam');
            Route::get('laporan/kolektibilitas-pinjaman', [LaporanController::class, 'kolektibilitasPinjaman'])->name('laporan.kolektibilitas_pinjaman');
            Route::get('laporan/perhitungan-shu', [LaporanController::class, 'perhitunganShu'])->name('laporan.perhitungan_shu');
        });

        // =====================================================
        // MANUFACTURING - Manajer, Admin, Superuser
        // =====================================================
        Route::middleware('role:superuser,admin,manajer')->group(function () {
            Route::get('manufacturing/bom', [ManufacturingController::class, 'bomIndex'])->name('manufacturing.bom.index');
            Route::get('manufacturing/bom/create', [ManufacturingController::class, 'bomCreate'])->name('manufacturing.bom.create');
            Route::post('manufacturing/bom', [ManufacturingController::class, 'bomStore'])->name('manufacturing.bom.store');
            
            Route::get('manufacturing/production', [ManufacturingController::class, 'productionIndex'])->name('manufacturing.production.index');
            Route::get('manufacturing/production/create', [ManufacturingController::class, 'productionCreate'])->name('manufacturing.production.create');
            Route::post('manufacturing/production', [ManufacturingController::class, 'productionStore'])->name('manufacturing.production.store');

            // Laporan Manufaktur
            Route::get('manufacturing/laporan/biaya-produksi', [ManufacturingController::class, 'laporanBiayaProduksi'])->name('manufacturing.laporan.biaya_produksi');
            Route::get('manufacturing/laporan/penggunaan-material', [ManufacturingController::class, 'laporanPenggunaanMaterial'])->name('manufacturing.laporan.penggunaan_material');
            Route::get('manufacturing/laporan/wip-valuation', [ManufacturingController::class, 'laporanWipValuation'])->name('manufacturing.laporan.wip_valuation');
        });

        // =====================================================
        // AGRICULTURE (PSAK 69) - Manajer, Admin, Superuser
        // =====================================================
        Route::middleware('role:superuser,admin,manajer')->group(function () {
            Route::get('agriculture', [AgricultureController::class, 'index'])->name('agriculture.index');
            Route::get('agriculture/create', [AgricultureController::class, 'create'])->name('agriculture.create');
            Route::post('agriculture', [AgricultureController::class, 'store'])->name('agriculture.store');
            Route::get('agriculture/{id}/edit', [AgricultureController::class, 'edit'])->name('agriculture.edit');
            Route::put('agriculture/{id}', [AgricultureController::class, 'update'])->name('agriculture.update');
            Route::delete('agriculture/{id}', [AgricultureController::class, 'destroy'])->name('agriculture.destroy');
            Route::post('agriculture/{id}/revaluation', [AgricultureController::class, 'revaluation'])->name('agriculture.revaluation');

            // Laporan PSAK 69
            Route::get('agriculture/laporan/rekonsiliasi', [AgricultureController::class, 'rekonsiliasi'])->name('agriculture.laporan.rekonsiliasi');
            Route::get('agriculture/laporan/perubahan-nilai-wajar', [AgricultureController::class, 'perubahanNilaiWajar'])->name('agriculture.laporan.perubahan_nilai_wajar');
            Route::get('agriculture/laporan/produksi-panen', [AgricultureController::class, 'produksiPanen'])->name('agriculture.laporan.produksi_panen');
            Route::get('agriculture/laporan/pengungkapan', [AgricultureController::class, 'pengungkapan'])->name('agriculture.laporan.pengungkapan');
        });

        // =====================================================
        // TEKNIS AKUNTANSI / CLOSING - Admin, Superuser
        // =====================================================
        Route::middleware('role:superuser,admin')->group(function () {
             Route::get('accounting/closing', [AccountingPeriodeController::class, 'index'])->name('accounting.closing.index');
             Route::get('accounting/closing/create', [AccountingPeriodeController::class, 'create'])->name('accounting.closing.create');
             Route::post('accounting/closing', [AccountingPeriodeController::class, 'closeBook'])->name('accounting.closing.store');
        });

        // =====================================================
        // DATABASE MANAGEMENT - Superuser Only
        // =====================================================
        Route::middleware('role:superuser')->group(function () {
            Route::get('database', [\App\Http\Controllers\DatabaseController::class, 'index'])->name('database.index');
            Route::post('database/truncate', [\App\Http\Controllers\DatabaseController::class, 'truncate'])->name('database.truncate');
            Route::post('database/fresh', [\App\Http\Controllers\DatabaseController::class, 'fresh'])->name('database.fresh');
            Route::post('database/drop', [\App\Http\Controllers\DatabaseController::class, 'drop'])->name('database.drop');
            Route::post('database/seed', [\App\Http\Controllers\DatabaseController::class, 'seed'])->name('database.seed');
        });

        // =====================================================
        // IMPORT & EXPORT DATA - Manajer, Admin, Superuser
        // =====================================================
        Route::middleware('role:superuser,admin,manajer')->group(function () {
            Route::get('import-export', [\App\Http\Controllers\ImportExportController::class, 'index'])->name('import-export.index');
            Route::get('import-export/export/{module}', [\App\Http\Controllers\ImportExportController::class, 'export'])->name('import-export.export');
            Route::get('import-export/template/{module}', [\App\Http\Controllers\ImportExportController::class, 'template'])->name('import-export.template');
            Route::post('import-export/import/{module}', [\App\Http\Controllers\ImportExportController::class, 'import'])->name('import-export.import');
            Route::get('import-export/export-all', [\App\Http\Controllers\ImportExportController::class, 'exportAll'])->name('import-export.export-all');
        });

        // =====================================================
        // PANDUAN PENGOPERASIAN - All authenticated users
        // =====================================================
        Route::get('guide', [GuideController::class, 'index'])->name('guide.index');
    });
});
