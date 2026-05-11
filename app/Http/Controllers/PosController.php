<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Pelanggan;
use App\Models\Pemasok;
use App\Models\Persediaan;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Akun;
use App\Models\Cabang;
use App\Models\UnitUsaha;
use App\Models\PosSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosController extends Controller
{
    use \App\Traits\CheckClosedPeriod;

    /**
     * POS main page.
     */
    public function index()
    {
        $user = auth()->user();
        // Check for an open session
        $session = PosSession::where('id_user', $user->id_user)
            ->whereNull('closed_at')
            ->first();

        if (!$session) {
            return redirect()->route('pos.session.create');
        }

        $akunKas = Akun::where('tipe_akun', 'Kas & Bank')->get();
        $pemasok = Pemasok::orderBy('nama_pemasok')->get();
        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $perusahaan = DB::table('perusahaan')->first();

        return view('pos.index', compact('session', 'akunKas', 'pemasok', 'cabang', 'unitUsaha', 'perusahaan'));
    }

    /**
     * Show session management page.
     */
    public function sessionCreate()
    {
        return view('pos.session');
    }

    /**
     * Open a new POS session (shift).
     */
    public function sessionOpen(Request $request)
    {
        $request->validate([
            'saldo_awal' => 'required|numeric|min:0',
        ]);

        $user = auth()->user();

        // Prevent double open
        $existing = PosSession::where('id_user', $user->id_user)
            ->whereNull('closed_at')
            ->first();

        if ($existing) {
            return redirect()->route('pos.index')->with('info', 'Sesi kasir sudah aktif.');
        }

        PosSession::create([
            'id_user' => $user->id_user,
            'id_cabang' => $user->id_cabang,
            'saldo_awal' => $request->saldo_awal,
            'opened_at' => now(),
        ]);

        return redirect()->route('pos.index')->with('success', 'Sesi kasir berhasil dibuka.');
    }

    /**
     * Close the current POS session.
     */
    public function sessionClose(Request $request)
    {
        $request->validate([
            'saldo_akhir' => 'required|numeric|min:0',
        ]);

        $user = auth()->user();
        $session = PosSession::where('id_user', $user->id_user)
            ->whereNull('closed_at')
            ->firstOrFail();

        // Calculate totals from transactions in this session
        $totalPenjualan = Penjualan::where('id_pos_session', $session->id)->sum('total');
        $totalPembelian = Pembelian::where('id_pos_session', $session->id)->sum('total');
        // Only cash (Tunai) purchases affect physical cash in the drawer
        $pembelianTunai = Pembelian::where('id_pos_session', $session->id)
            ->where('metode_pembayaran', 'Tunai')->sum('total');

        $expectedCash = $session->saldo_awal + $totalPenjualan - $pembelianTunai;
        $selisih = $request->saldo_akhir - $expectedCash;

        $session->update([
            'saldo_akhir' => $request->saldo_akhir,
            'total_penjualan' => $totalPenjualan,
            'total_pembelian' => $totalPembelian,
            'selisih' => $selisih,
            'closed_at' => now(),
        ]);

        return redirect()->route('pos.session.create')->with('success', 
            "Sesi kasir ditutup. Penjualan: Rp " . number_format($totalPenjualan, 0, ',', '.') . 
            " | Pembelian Tunai: Rp " . number_format($pembelianTunai, 0, ',', '.') . 
            " | Selisih: Rp " . number_format($selisih, 0, ',', '.'));
    }

    /**
     * AJAX: Search products by barcode or name.
     */
    public function searchProduct(Request $request)
    {
        $q = $request->input('q', '');

        if (empty($q)) {
            return response()->json([]);
        }

        $products = Persediaan::where('barcode', $q)
            ->orWhere('kode_barang', 'LIKE', "%{$q}%")
            ->orWhere('nama_barang', 'LIKE', "%{$q}%")
            ->limit(20)
            ->get(['id_barang', 'kode_barang', 'barcode', 'nama_barang', 'satuan', 'harga_jual', 'harga_beli', 'stok_saat_ini']);

        return response()->json($products);
    }

    /**
     * Store a POS sale transaction.
     */
    public function storeSale(Request $request)
    {
        $request->validate([
            'akun_kas_bank' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:master_persediaan,id_barang',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.harga' => 'required|numeric|min:0',
            'diskon_total' => 'nullable|numeric|min:0',
            'id_cabang' => 'nullable|exists:cabang,id',
            'id_unit_usaha' => 'nullable|exists:unit_usaha,id',
        ]);

        $user = auth()->user();
        $session = PosSession::where('id_user', $user->id_user)
            ->whereNull('closed_at')
            ->firstOrFail();

        try {
            DB::beginTransaction();

            $tanggal = now()->format('Y-m-d');
            $this->validatePeriodOpen($tanggal);

            // Auto-number: POS-YYYYMMDD-XXXXX
            $prefix = 'POS-' . now()->format('Ymd') . '-';
            $lastPOS = Penjualan::where('no_faktur', 'LIKE', $prefix . '%')
                ->orderBy('id_penjualan', 'desc')
                ->lockForUpdate()
                ->first();
            $nextNo = $lastPOS ? (int)substr($lastPOS->no_faktur, -5) + 1 : 1;
            $noFaktur = $prefix . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

            // Get POS settings
            $perusahaan = DB::table('perusahaan')->first();
            $akunPendapatanDefault = $perusahaan->pos_akun_pendapatan_default ?? $perusahaan->akun_pendapatan ?? '4-10000';
            $diskonTotal = $request->diskon_total ?? 0;

            // Calculate totals & validate stock
            $totalPenjualan = 0;
            $detailsData = [];

            foreach ($request->items as $item) {
                $barang = Persediaan::findOrFail($item['id_barang']);

                if ($barang->stok_saat_ini < $item['qty']) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak cukup. Sisa: {$barang->stok_saat_ini}");
                }

                $subtotal = $item['harga'] * $item['qty'];
                $totalPenjualan += $subtotal;

                $detailsData[] = [
                    'barang' => $barang,
                    'kuantitas' => $item['qty'],
                    'harga' => $item['harga'],
                    'subtotal' => $subtotal,
                ];
            }

            $totalPenjualan -= $diskonTotal;

            // Create journal
            $idCabang = $request->id_cabang ?? $session->id_cabang ?? $user->id_cabang;
            $idUnit = $request->id_unit_usaha;

            $jurnal = Jurnal::create([
                'no_transaksi' => $noFaktur,
                'tanggal' => $tanggal,
                'id_cabang' => $idCabang,
                'id_unit_usaha' => $idUnit,
                'deskripsi' => "POS Penjualan #{$noFaktur}",
                'sumber_jurnal' => 'POS',
                'is_locked' => 1,
            ]);

            // Debit: Kas
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $request->akun_kas_bank,
                'debit' => $totalPenjualan,
                'kredit' => 0,
            ]);

            // Per-item: Kredit Pendapatan + HPP
            foreach ($detailsData as $data) {
                $akunKredit = $data['barang']->akun_penjualan ?? $akunPendapatanDefault;
                JurnalDetail::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'kode_akun' => $akunKredit,
                    'debit' => 0,
                    'kredit' => $data['subtotal'],
                ]);

                // HPP (skip for jasa)
                $jenisUsaha = $perusahaan->jenis_usaha ?? 'dagang';
                $akunHpp = $data['barang']->akun_hpp ?? ($perusahaan->pos_akun_hpp_default ?? '5-10000');
                $akunPersediaan = $data['barang']->akun_persediaan ?? ($perusahaan->pos_akun_persediaan_default ?? '1-10200');

                if ($jenisUsaha !== 'jasa' && $akunHpp && $akunPersediaan) {
                    $totalHPP = $data['barang']->harga_beli * $data['kuantitas'];

                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id_jurnal,
                        'kode_akun' => $akunHpp,
                        'debit' => $totalHPP,
                        'kredit' => 0,
                    ]);

                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id_jurnal,
                        'kode_akun' => $akunPersediaan,
                        'debit' => 0,
                        'kredit' => $totalHPP,
                    ]);
                }
            }

            // Create Penjualan
            // Use "Pelanggan Umum" — get or create
            $pelangganUmum = Pelanggan::firstOrCreate(
                ['nama_pelanggan' => 'Pelanggan Umum (POS)'],
                ['alamat' => '-', 'telepon' => '-']
            );

            $penjualan = Penjualan::create([
                'id_pelanggan' => $pelangganUmum->id_pelanggan,
                'id_jurnal' => $jurnal->id_jurnal,
                'id_cabang' => $idCabang,
                'id_unit_usaha' => $idUnit,
                'no_faktur' => $noFaktur,
                'tanggal_faktur' => $tanggal,
                'total' => $totalPenjualan,
                'keterangan' => 'Transaksi POS',
                'metode_pembayaran' => 'Tunai',
                'akun_kas_bank' => $request->akun_kas_bank,
                'sisa_tagihan' => 0,
                'status_pembayaran' => 'Lunas',
                'sumber' => 'POS',
                'id_pos_session' => $session->id,
                'diskon_total' => $diskonTotal,
            ]);

            // Save details & update stock
            foreach ($detailsData as $data) {
                PenjualanDetail::create([
                    'id_penjualan' => $penjualan->id_penjualan,
                    'id_barang' => $data['barang']->id_barang,
                    'kuantitas' => $data['kuantitas'],
                    'harga' => $data['harga'],
                    'subtotal' => $data['subtotal'],
                    'akun_pendapatan' => $data['barang']->akun_penjualan,
                ]);

                $data['barang']->decrement('stok_saat_ini', $data['kuantitas']);

                DB::table('kartu_stok')->insert([
                    'id_barang' => $data['barang']->id_barang,
                    'tipe_transaksi' => 'OUT',
                    'kuantitas' => $data['kuantitas'],
                    'keterangan' => "POS Penjualan #{$noFaktur}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'no_faktur' => $noFaktur,
                'total' => $totalPenjualan,
                'id_penjualan' => $penjualan->id_penjualan,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('POS Sale Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Store a POS purchase (buying) transaction.
     * Only accessible by manajer and above.
     */
    public function storePurchase(Request $request)
    {
        // Authorization check
        if (!auth()->user()->canAccessPosBuying()) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk input pembelian.'], 403);
        }

        $request->validate([
            'id_pemasok' => 'required|exists:pemasok,id_pemasok',
            'metode_pembayaran' => 'required|in:Tunai,Kredit',
            'akun_kas_bank' => 'required_if:metode_pembayaran,Tunai',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:master_persediaan,id_barang',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'id_cabang' => 'nullable|exists:cabang,id',
            'id_unit_usaha' => 'nullable|exists:unit_usaha,id',
        ]);

        $user = auth()->user();
        $session = PosSession::where('id_user', $user->id_user)
            ->whereNull('closed_at')
            ->firstOrFail();

        try {
            DB::beginTransaction();

            $tanggal = now()->format('Y-m-d');
            $this->validatePeriodOpen($tanggal);

            // Auto-number: POB-YYYYMMDD-XXXXX
            $prefix = 'POB-' . now()->format('Ymd') . '-';
            $lastPOB = Pembelian::where('no_faktur_pembelian', 'LIKE', $prefix . '%')
                ->orderBy('id_pembelian', 'desc')
                ->lockForUpdate()
                ->first();
            $nextNo = $lastPOB ? (int)substr($lastPOB->no_faktur_pembelian, -5) + 1 : 1;
            $noFaktur = $prefix . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

            $perusahaan = DB::table('perusahaan')->first();
            $akunUtang = $perusahaan->pos_akun_utang_default ?? $perusahaan->akun_utang ?? '2-10100';
            $akunPersediaanDefault = $perusahaan->pos_akun_persediaan_default ?? $perusahaan->akun_persediaan ?? '1-10200';

            $totalPembelian = 0;
            $detailsData = [];

            foreach ($request->items as $item) {
                $barang = Persediaan::findOrFail($item['id_barang']);
                $subtotal = $item['harga_beli'] * $item['qty'];
                $totalPembelian += $subtotal;

                $detailsData[] = [
                    'barang' => $barang,
                    'kuantitas' => $item['qty'],
                    'harga' => $item['harga_beli'],
                    'subtotal' => $subtotal,
                ];
            }

            $idCabang = $request->id_cabang ?? $session->id_cabang ?? $user->id_cabang;
            $idUnit = $request->id_unit_usaha;

            // Create journal
            $jurnal = Jurnal::create([
                'no_transaksi' => $noFaktur,
                'tanggal' => $tanggal,
                'id_cabang' => $idCabang,
                'id_unit_usaha' => $idUnit,
                'id_pemasok' => $request->id_pemasok,
                'deskripsi' => "POS Pembelian #{$noFaktur}",
                'sumber_jurnal' => 'POS',
                'is_locked' => 1,
            ]);

            // Debit: Persediaan per item
            foreach ($detailsData as $data) {
                $akunDebit = $data['barang']->akun_persediaan ?? $akunPersediaanDefault;
                JurnalDetail::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'kode_akun' => $akunDebit,
                    'debit' => $data['subtotal'],
                    'kredit' => 0,
                ]);
            }

            // Kredit: Kas (Tunai) atau Utang (Kredit supplier)
            $akunKredit = ($request->metode_pembayaran == 'Tunai') ? $request->akun_kas_bank : $akunUtang;
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $akunKredit,
                'debit' => 0,
                'kredit' => $totalPembelian,
            ]);

            // Save Pembelian
            $pembelian = Pembelian::create([
                'id_pemasok' => $request->id_pemasok,
                'id_jurnal' => $jurnal->id_jurnal,
                'id_cabang' => $idCabang,
                'id_unit_usaha' => $idUnit,
                'no_faktur_pembelian' => $noFaktur,
                'tanggal_faktur' => $tanggal,
                'total' => $totalPembelian,
                'keterangan' => 'Pembelian POS',
                'metode_pembayaran' => $request->metode_pembayaran,
                'akun_kas_bank' => ($request->metode_pembayaran == 'Tunai') ? $request->akun_kas_bank : null,
                'sisa_tagihan' => ($request->metode_pembayaran == 'Kredit') ? $totalPembelian : 0,
                'status_pembayaran' => ($request->metode_pembayaran == 'Kredit') ? 'Belum Lunas' : 'Lunas',
                'sumber' => 'POS',
                'id_pos_session' => $session->id,
            ]);

            // Save details & update stock
            foreach ($detailsData as $data) {
                PembelianDetail::create([
                    'id_pembelian' => $pembelian->id_pembelian,
                    'id_barang' => $data['barang']->id_barang,
                    'kuantitas' => $data['kuantitas'],
                    'harga' => $data['harga'],
                    'subtotal' => $data['subtotal'],
                ]);

                $data['barang']->increment('stok_saat_ini', $data['kuantitas']);
                $data['barang']->update(['harga_beli' => $data['harga']]);

                DB::table('kartu_stok')->insert([
                    'id_barang' => $data['barang']->id_barang,
                    'tipe_transaksi' => 'IN',
                    'kuantitas' => $data['kuantitas'],
                    'keterangan' => "POS Pembelian #{$noFaktur}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update saldo pemasok if kredit
            if ($request->metode_pembayaran == 'Kredit') {
                Pemasok::where('id_pemasok', $request->id_pemasok)
                    ->increment('saldo_terkini_hutang', $totalPembelian);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembelian berhasil!',
                'no_faktur' => $noFaktur,
                'total' => $totalPembelian,
                'id_pembelian' => $pembelian->id_pembelian,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('POS Purchase Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Print sale receipt.
     */
    public function receipt($id)
    {
        $penjualan = Penjualan::with(['details.barang', 'pelanggan'])->findOrFail($id);
        $perusahaan = DB::table('perusahaan')->first();
        return view('pos.receipt', compact('penjualan', 'perusahaan'));
    }

    /**
     * Print purchase receipt.
     */
    public function purchaseReceipt($id)
    {
        $pembelian = Pembelian::with(['details.barang', 'pemasok'])->findOrFail($id);
        $perusahaan = DB::table('perusahaan')->first();
        return view('pos.purchase-receipt', compact('pembelian', 'perusahaan'));
    }

    /**
     * Shift report — preview & print.
     */
    public function shiftReport($id)
    {
        $session = PosSession::with('user')->findOrFail($id);
        $perusahaan = DB::table('perusahaan')->first();

        // Get all sales in this session with details
        $penjualan = Penjualan::with('details.barang')
            ->where('id_pos_session', $session->id)
            ->orderBy('tanggal_faktur')
            ->orderBy('no_faktur')
            ->get();

        // Get all purchases in this session with details
        $pembelian = Pembelian::with(['details.barang', 'pemasok'])
            ->where('id_pos_session', $session->id)
            ->orderBy('tanggal_faktur')
            ->orderBy('no_faktur_pembelian')
            ->get();

        // Summary calculations
        $totalPenjualan = $penjualan->sum('total');
        $totalDiskon = $penjualan->sum('diskon_total');
        $jumlahTransaksiSales = $penjualan->count();
        $jumlahItemSales = $penjualan->sum(fn($p) => $p->details->sum('kuantitas'));

        $totalPembelian = $pembelian->sum('total');
        $pembelianTunai = $pembelian->where('metode_pembayaran', 'Tunai')->sum('total');
        $pembelianKredit = $pembelian->where('metode_pembayaran', 'Kredit')->sum('total');
        $jumlahTransaksiBuy = $pembelian->count();

        $expectedCash = $session->saldo_awal + $totalPenjualan - $pembelianTunai;

        return view('pos.shift-report', compact(
            'session', 'perusahaan',
            'penjualan', 'pembelian',
            'totalPenjualan', 'totalDiskon', 'jumlahTransaksiSales', 'jumlahItemSales',
            'totalPembelian', 'pembelianTunai', 'pembelianKredit', 'jumlahTransaksiBuy',
            'expectedCash'
        ));
    }
}
