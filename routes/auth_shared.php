<?php

use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\ReturController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\SimpananController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\FixedAssetGroupController;
use App\Http\Controllers\FixedAssetController;
use App\Http\Controllers\FixedAssetDisposalController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\PenjualanPenawaranController;
use App\Http\Controllers\PembelianRfqController;

Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Audit Diagnostics (Debug Position)
Route::get('test-diagnosa', function() { return "Test OK"; });
Route::get('laporan/diagnosa-neraca', [LaporanController::class, 'checkNeraca'])->name('audit.neraca');
Route::get('laporan/buku-pembantu-piutang', [LaporanController::class, 'bukuPembantuPiutang'])->name('laporan.piutang');
Route::get('laporan/buku-pembantu-utang', [LaporanController::class, 'bukuPembantuUtang'])->name('laporan.utang');

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
    
    Route::resource('aset-tetap-group', FixedAssetGroupController::class)->except(['show']);
    Route::post('aset-tetap/depreciate', [FixedAssetController::class, 'runDepreciation'])->name('aset-tetap.depreciate');
    Route::get('aset-tetap/{asset}/dispose', [FixedAssetDisposalController::class, 'create'])->name('aset-tetap.dispose.create');
    Route::post('aset-tetap/{asset}/dispose', [FixedAssetDisposalController::class, 'store'])->name('aset-tetap.dispose.store');
    Route::resource('aset-tetap', FixedAssetController::class);
});

Route::middleware('role:superuser,admin,manajer')->group(function () {
    Route::get('pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::get('pelanggan/recalculate', [PelangganController::class, 'recalculate'])->name('pelanggan.recalculate');
    Route::get('pelanggan/{pelanggan}', [PelangganController::class, 'show'])->name('pelanggan.show');
    Route::get('pemasok', [PemasokController::class, 'index'])->name('pemasok.index');
    Route::get('pemasok/recalculate', [PemasokController::class, 'recalculate'])->name('pemasok.recalculate');
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
    Route::post('penawaran/{id}/convert', [PenjualanPenawaranController::class, 'convertToInvoice'])->name('penawaran.convert');
    Route::resource('penawaran', PenjualanPenawaranController::class);
    Route::resource('penjualan', PenjualanController::class);
    
    Route::post('rfq/{id}/convert', [PembelianRfqController::class, 'convertToPurchase'])->name('rfq.convert');
    Route::resource('rfq', PembelianRfqController::class);
    Route::resource('pembelian', PembelianController::class);
    Route::get('jurnal/kas/create', [JurnalController::class, 'createKas'])->name('jurnal.createKas');
    Route::post('jurnal/kas', [JurnalController::class, 'storeKas'])->name('jurnal.storeKas');
    Route::resource('jurnal', JurnalController::class);
    Route::resource('penerimaan', PenerimaanController::class);
    Route::resource('pembayaran', PembayaranController::class);

    // RETUR
    Route::get('retur/penjualan', [ReturController::class, 'indexPenjualan'])->name('retur.penjualan.index');
    Route::get('retur/penjualan/create', [ReturController::class, 'createPenjualan'])->name('retur.penjualan.create');
    Route::post('retur/penjualan', [ReturController::class, 'storePenjualan'])->name('retur.penjualan.store');
    Route::get('retur/penjualan/{id}', [ReturController::class, 'showPenjualan'])->name('retur.penjualan.show');

    Route::get('retur/pembelian', [ReturController::class, 'indexPembelian'])->name('retur.pembelian.index');
    Route::get('retur/pembelian/create', [ReturController::class, 'createPembelian'])->name('retur.pembelian.create');
    Route::post('retur/pembelian', [ReturController::class, 'storePembelian'])->name('retur.pembelian.store');
    Route::get('retur/pembelian/{id}', [ReturController::class, 'showPembelian'])->name('retur.pembelian.show');

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
    Route::get('/laporan/neraca-lajur', [LaporanController::class, 'neracaLajur'])->name('laporan.neraca_lajur');
    Route::get('/laporan/mutasi-persediaan', [LaporanController::class, 'mutasiPersediaan'])->name('laporan.mutasi_persediaan');
    Route::get('/laporan/aset-tetap', [LaporanController::class, 'daftarAsetTetap'])->name('laporan.aset_tetap');
    Route::get('/laporan/downloads', [LaporanController::class, 'downloads'])->name('laporan.downloads');
    Route::get('/laporan/downloads/{id}', [LaporanController::class, 'downloadFile'])->name('laporan.download_file');
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
    Route::get('audit-trail', [AuditTrailController::class, 'index'])->name('audit-trail.index');
});

// API: Cascade dropdown unit usaha by cabang (all authenticated)
Route::get('api/unit-usaha/cabang/{cabangId}', [UnitUsahaController::class, 'getByCabang'])->name('api.unit-usaha.by-cabang');

// =====================================================
// KOPERASI SIMPAN PINJAM - Staff level and above
// =====================================================
Route::middleware('role:superuser,admin,manajer,staff')->group(function () {
    Route::resource('anggota', AnggotaController::class);
    Route::get('anggota/{id}/kartu', [AnggotaController::class, 'kartu'])->name('anggota.kartu');

    Route::resource('simpanan', SimpananController::class);
    Route::get('simpanan-setor', [SimpananController::class, 'setor'])->name('simpanan.setor');
    Route::get('simpanan-tarik', [SimpananController::class, 'tarik'])->name('simpanan.tarik');
    Route::get('simpanan-kartu/{id_anggota}', [SimpananController::class, 'kartu'])->name('simpanan.kartu');

    Route::post('pinjaman/simulasi', [PinjamanController::class, 'simulasi'])->name('pinjaman.simulasi');
    Route::resource('pinjaman', PinjamanController::class);
    Route::post('pinjaman/{id}/submit', [PinjamanController::class, 'submit'])->name('pinjaman.submit');
    Route::get('pinjaman/{id}/pencairan', [PinjamanController::class, 'pencairanForm'])->name('pinjaman.pencairan');
    Route::post('pinjaman/{id}/cairkan', [PinjamanController::class, 'cairkan'])->name('pinjaman.cairkan');
    Route::get('pinjaman/{id}/angsuran', [PinjamanController::class, 'angsuranForm'])->name('pinjaman.angsuran');
    Route::post('pinjaman/{id}/bayar', [PinjamanController::class, 'bayarAngsuran'])->name('pinjaman.bayar');
    Route::get('pinjaman/{id}/pelunasan', [PinjamanController::class, 'pelunasanForm'])->name('pinjaman.pelunasan');
    Route::post('pinjaman/{id}/lunasi', [PinjamanController::class, 'lunasi'])->name('pinjaman.lunasi');
});

Route::middleware('role:superuser,admin,manajer')->group(function () {
    Route::get('approval', [ApprovalController::class, 'inbox'])->name('approval.inbox');
    Route::post('approval/{module}/{id}/approve', [ApprovalController::class, 'approve'])->name('approval.approve');
    Route::post('approval/{module}/{id}/reject', [ApprovalController::class, 'reject'])->name('approval.reject');
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
    Route::get('manufacturing/bom/{id}/edit', [ManufacturingController::class, 'bomEdit'])->name('manufacturing.bom.edit');
    Route::put('manufacturing/bom/{id}', [ManufacturingController::class, 'bomUpdate'])->name('manufacturing.bom.update');
    Route::delete('manufacturing/bom/{id}', [ManufacturingController::class, 'bomDestroy'])->name('manufacturing.bom.destroy');
    
    Route::get('manufacturing/production', [ManufacturingController::class, 'productionIndex'])->name('manufacturing.production.index');
    Route::get('manufacturing/production/create', [ManufacturingController::class, 'productionCreate'])->name('manufacturing.production.create');
    Route::post('manufacturing/production', [ManufacturingController::class, 'productionStore'])->name('manufacturing.production.store');
    Route::get('manufacturing/production/{id}/edit', [ManufacturingController::class, 'productionEdit'])->name('manufacturing.production.edit');
    Route::put('manufacturing/production/{id}', [ManufacturingController::class, 'productionUpdate'])->name('manufacturing.production.update');
    Route::delete('manufacturing/production/{id}', [ManufacturingController::class, 'productionDestroy'])->name('manufacturing.production.destroy');

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
    Route::get('database', [DatabaseController::class, 'index'])->name('database.index');
    Route::post('database/truncate', [DatabaseController::class, 'truncate'])->name('database.truncate');
    Route::post('database/fresh', [DatabaseController::class, 'fresh'])->name('database.fresh');
    Route::post('database/drop', [DatabaseController::class, 'drop'])->name('database.drop');
    Route::post('database/seed', [DatabaseController::class, 'seed'])->name('database.seed');
});

// =====================================================
// IMPORT & EXPORT DATA - Manajer, Admin, Superuser
// =====================================================
Route::middleware('role:superuser,admin,manajer')->group(function () {
    Route::get('import-export', [ImportExportController::class, 'index'])->name('import-export.index');
    Route::get('import-export/export/{module}', [ImportExportController::class, 'export'])->name('import-export.export');
    Route::get('import-export/template/{module}', [ImportExportController::class, 'template'])->name('import-export.template');
    Route::post('import-export/import/{module}', [ImportExportController::class, 'import'])->name('import-export.import');
    Route::get('import-export/export-all', [ImportExportController::class, 'exportAll'])->name('import-export.export-all');
});

// =====================================================
// PANDUAN PENGOPERASIAN - All authenticated users
// =====================================================
Route::get('guide', [GuideController::class, 'index'])->name('guide.index');
Route::get('guide/pdf', [GuideController::class, 'downloadPdf'])->name('guide.pdf');
