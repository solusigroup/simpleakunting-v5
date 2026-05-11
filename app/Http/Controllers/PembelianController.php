<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Pemasok;
use App\Models\Persediaan;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Akun;
use App\Models\Cabang;
use App\Models\UnitUsaha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembelianController extends Controller
{
    use \App\Traits\CheckClosedPeriod;
    public function index(Request $request)
    {
        $query = Pembelian::with('pemasok');

        if ($request->has('id_pemasok')) {
            $query->where('id_pemasok', $request->id_pemasok);
        }

        $pembelian = $query->orderBy('tanggal_faktur', 'desc')->paginate(20);
        return view('pembelian.index', compact('pembelian'));
    }

    public function create()
    {
        $pemasok = Pemasok::all();
        $barang = Persediaan::all();
        $akunKas = Akun::where('tipe_akun', 'Kas & Bank')->get();
        
        // Generate No Faktur Otomatis (Simple)
        $lastFaktur = Pembelian::orderBy('id_pembelian', 'desc')->first();
        // Use no_faktur_pembelian column
        $nextNo = $lastFaktur ? (int)substr($lastFaktur->no_faktur_pembelian, 4) + 1 : 1;
        $noFaktur = 'PUR-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();

        return view('pembelian.create', compact('pemasok', 'barang', 'akunKas', 'noFaktur', 'cabang', 'unitUsaha'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pemasok' => 'required|exists:pemasok,id_pemasok',
            'id_cabang' => 'required|exists:cabang,id',
            'id_unit_usaha' => 'required|exists:unit_usaha,id',
            'no_faktur' => 'required|unique:pembelian,no_faktur_pembelian',
            'tanggal_faktur' => 'required|date|before_or_equal:today',
            'metode_pembayaran' => 'required|in:Tunai,Kredit',
            'akun_kas_bank' => 'required_if:metode_pembayaran,Tunai',
            'details' => 'required|array|min:1',
            'details.*.id_barang' => 'required|exists:master_persediaan,id_barang',
            'details.*.kuantitas' => 'required|numeric|min:1',
            'details.*.harga_beli' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // C3: Cek periode tutup buku
            $this->validatePeriodOpen($request->tanggal_faktur);

            // H5: Generate no_faktur atomik
            $lastFaktur = Pembelian::orderBy('id_pembelian', 'desc')->lockForUpdate()->first();
            $nextNo = $lastFaktur ? (int)substr($lastFaktur->no_faktur_pembelian, 4) + 1 : 1;
            $noFaktur = 'PUR-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

            $totalPembelian = 0;
            $detailsData = [];
            
            foreach ($request->details as $item) {
                $barang = Persediaan::findOrFail($item['id_barang']);
                $subtotal = $item['harga_beli'] * $item['kuantitas'];
                $totalPembelian += $subtotal;

                $detailsData[] = [
                    'barang' => $barang,
                    'kuantitas' => $item['kuantitas'],
                    'harga' => $item['harga_beli'],
                    'subtotal' => $subtotal,
                ];
            }

            // H1: Dynamic account codes dari settings
            $perusahaan = DB::table('perusahaan')->first();
            $akunUtang = $perusahaan->akun_utang ?? '2-10100';
            $akunPersediaanDefault = $perusahaan->akun_persediaan ?? '1-10200';

            $jurnal = Jurnal::create([
                'no_transaksi' => $noFaktur,
                'tanggal' => $request->tanggal_faktur,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'deskripsi' => "Pembelian Faktur #{$noFaktur}",
                'sumber_jurnal' => 'Pembelian',
                'is_locked' => 1
            ]);

            // Debit: Persediaan (Per Barang)
            foreach ($detailsData as $data) {
                $akunDebit = $data['barang']->akun_persediaan ?? $akunPersediaanDefault;
                JurnalDetail::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'kode_akun' => $akunDebit,
                    'debit' => $data['subtotal'],
                    'kredit' => 0
                ]);
            }

            // Kredit: Kas (Tunai) atau Utang (Kredit)
            $akunKredit = ($request->metode_pembayaran == 'Tunai') ? $request->akun_kas_bank : $akunUtang;
            
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $akunKredit,
                'debit' => 0,
                'kredit' => $totalPembelian
            ]);

            // 3. Simpan Pembelian
            $pembelian = Pembelian::create([
                'id_pemasok' => $request->id_pemasok,
                'id_jurnal' => $jurnal->id_jurnal,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'no_faktur_pembelian' => $noFaktur,
                'tanggal_faktur' => $request->tanggal_faktur,
                'total' => $totalPembelian,
                'keterangan' => $request->keterangan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'akun_kas_bank' => ($request->metode_pembayaran == 'Tunai') ? $request->akun_kas_bank : null,
                'sisa_tagihan' => ($request->metode_pembayaran == 'Kredit') ? $totalPembelian : 0,
                'status_pembayaran' => ($request->metode_pembayaran == 'Kredit') ? 'Belum Lunas' : 'Lunas',
            ]);

            // 4. Simpan Detail & Update Stok
            foreach ($detailsData as $data) {
                PembelianDetail::create([
                    'id_pembelian' => $pembelian->id_pembelian,
                    'id_barang' => $data['barang']->id_barang,
                    'kuantitas' => $data['kuantitas'],
                    'harga' => $data['harga'],
                    'subtotal' => $data['subtotal'],
                ]);

                // Tambah Stok & Update Harga Beli Terakhir
                $data['barang']->increment('stok_saat_ini', $data['kuantitas']);
                $data['barang']->update(['harga_beli' => $data['harga']]);

                // Catat Kartu Stok
                DB::table('kartu_stok')->insert([
                    'id_barang' => $data['barang']->id_barang,
                    'tipe_transaksi' => 'IN',
                    'kuantitas' => $data['kuantitas'],
                    'keterangan' => "Pembelian #{$noFaktur}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 5. Update Saldo Pemasok (Jika Kredit)
            if ($request->metode_pembayaran == 'Kredit') {
                Pemasok::where('id_pemasok', $request->id_pemasok)
                    ->increment('saldo_terkini_hutang', $totalPembelian);
            }

            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Transaksi pembelian berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $msg = str_contains($e->getMessage(), 'Periode') || str_contains($e->getMessage(), 'Stok')
                ? $e->getMessage()
                : 'Gagal menyimpan transaksi. Silakan coba lagi.';
            return back()->with('error', $msg)->withInput();
        }
    }

    public function show(Pembelian $pembelian)
    {
        $pembelian->load(['details.barang', 'pemasok']);
        return view('pembelian.show', compact('pembelian'));
    }

    public function edit(Pembelian $pembelian)
    {
        $pembelian->load('details.barang');
        $pemasok = Pemasok::all();
        $barang = Persediaan::all();
        $akunKas = Akun::where('tipe_akun', 'Kas & Bank')->get();
        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();

        return view('pembelian.edit', compact('pembelian', 'pemasok', 'barang', 'akunKas', 'cabang', 'unitUsaha'));
    }

    public function update(Request $request, Pembelian $pembelian)
    {
        $request->validate([
            'id_pemasok' => 'required|exists:pemasok,id_pemasok',
            'id_cabang' => 'required|exists:cabang,id',
            'id_unit_usaha' => 'required|exists:unit_usaha,id',
            'tanggal_faktur' => 'required|date|before_or_equal:today',
            'metode_pembayaran' => 'required|in:Tunai,Kredit',
            'akun_kas_bank' => 'required_if:metode_pembayaran,Tunai',
            'details' => 'required|array|min:1',
            'details.*.id_barang' => 'required|exists:master_persediaan,id_barang',
            'details.*.kuantitas' => 'required|numeric|min:1',
            'details.*.harga_beli' => 'required|numeric|min:0',
        ]);

        // Proteksi Sesi POS (Shift)
        if ($pembelian->id_pos_session) {
            $session = \App\Models\PosSession::find($pembelian->id_pos_session);
            if ($session && !$session->isOpen() && !auth()->user()->isSuperuser()) {
                return back()->with('error', 'Transaksi POS ini tidak dapat diubah karena sesi kasir (shift) sudah ditutup.');
            }
        }

        try {
            DB::beginTransaction();

            // C3: Cek periode tutup buku (Old date and New date)
            $this->validatePeriodOpen($pembelian->tanggal_faktur);
            $this->validatePeriodOpen($request->tanggal_faktur);

            // 1. REVERSE OLD IMPACT
            // Reverse Stock & Remove Kartu Stok
            foreach ($pembelian->details as $oldDetail) {
                $barang = Persediaan::find($oldDetail->id_barang);
                if ($barang) {
                    $barang->decrement('stok_saat_ini', $oldDetail->kuantitas);
                }
                DB::table('kartu_stok')
                    ->where('id_barang', $oldDetail->id_barang)
                    ->where('keterangan', 'LIKE', "%#{$pembelian->no_faktur_pembelian}%")
                    ->delete();
            }

            // Reverse Supplier Balance (If was Kredit)
            if ($pembelian->metode_pembayaran == 'Kredit') {
                Pemasok::where('id_pemasok', $pembelian->id_pemasok)
                    ->decrement('saldo_terkini_hutang', $pembelian->total);
            }

            // Delete Old Journal Details (We reuse the Jurnal header)
            JurnalDetail::where('id_jurnal', $pembelian->id_jurnal)->delete();

            // 2. APPLY NEW IMPACT
            $totalPembelian = 0;
            $detailsData = [];
            foreach ($request->details as $item) {
                $barang = Persediaan::findOrFail($item['id_barang']);
                $subtotal = $item['harga_beli'] * $item['kuantitas'];
                $totalPembelian += $subtotal;

                $detailsData[] = [
                    'barang' => $barang,
                    'kuantitas' => $item['kuantitas'],
                    'harga' => $item['harga_beli'],
                    'subtotal' => $subtotal,
                ];
            }

            // Update Jurnal Header
            $jurnal = Jurnal::findOrFail($pembelian->id_jurnal);
            $jurnal->update([
                'tanggal' => $request->tanggal_faktur,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'deskripsi' => "Update Pembelian Faktur #{$pembelian->no_faktur_pembelian}",
            ]);

            // Re-create Journal Details
            $perusahaan = DB::table('perusahaan')->first();
            $akunUtang = $perusahaan->akun_utang ?? '2-10100';
            $akunPersediaanDefault = $perusahaan->akun_persediaan ?? '1-10200';

            // Debit: Persediaan
            foreach ($detailsData as $data) {
                $akunDebit = $data['barang']->akun_persediaan ?? $akunPersediaanDefault;
                JurnalDetail::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'kode_akun' => $akunDebit,
                    'debit' => $data['subtotal'],
                    'kredit' => 0
                ]);
            }

            // Kredit: Kas or Utang
            $akunKredit = ($request->metode_pembayaran == 'Tunai') ? $request->akun_kas_bank : $akunUtang;
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $akunKredit,
                'debit' => 0,
                'kredit' => $totalPembelian
            ]);

            // Update Pembelian Header
            $pembelian->update([
                'id_pemasok' => $request->id_pemasok,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'tanggal_faktur' => $request->tanggal_faktur,
                'total' => $totalPembelian,
                'keterangan' => $request->keterangan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'akun_kas_bank' => ($request->metode_pembayaran == 'Tunai') ? $request->akun_kas_bank : null,
                'sisa_tagihan' => ($request->metode_pembayaran == 'Kredit') ? $totalPembelian : 0,
                'status_pembayaran' => ($request->metode_pembayaran == 'Kredit') ? 'Belum Lunas' : 'Lunas',
            ]);

            // Delete old details from DB
            $pembelian->details()->delete();

            // Save New Details & Update Stock
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
                    'keterangan' => "Update Pembelian #{$pembelian->no_faktur_pembelian}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update Supplier Balance (If new is Kredit)
            if ($request->metode_pembayaran == 'Kredit') {
                Pemasok::where('id_pemasok', $request->id_pemasok)
                    ->increment('saldo_terkini_hutang', $totalPembelian);
            }

            // Sync POS Session Total
            if ($pembelian->id_pos_session) {
                $session = \App\Models\PosSession::find($pembelian->id_pos_session);
                if ($session) {
                    $newTotal = Pembelian::where('id_pos_session', $session->id)->sum('total');
                    $session->update(['total_pembelian' => $newTotal]);
                }
            }

            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Transaksi pembelian berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Pembelian $pembelian)
    {
        // Proteksi Sesi POS (Shift)
        if ($pembelian->id_pos_session) {
            $session = \App\Models\PosSession::find($pembelian->id_pos_session);
            if ($session && !$session->isOpen() && !auth()->user()->isSuperuser()) {
                return back()->with('error', 'Transaksi POS ini tidak dapat dihapus karena sesi kasir (shift) sudah ditutup.');
            }
        }

        try {
            DB::beginTransaction();

            // C3: Cek periode tutup buku
            $this->validatePeriodOpen($pembelian->tanggal_faktur);

            // 1. Kembalikan Stok & Hapus Kartu Stok
            foreach ($pembelian->details as $detail) {
                $barang = Persediaan::find($detail->id_barang);
                if ($barang) {
                    // Cek apakah stok cukup untuk dikurangi (untuk menghindari stok negatif jika diinginkan)
                    // Namun untuk pembatalan pembelian biasanya kita kurangi saja
                    $barang->decrement('stok_saat_ini', $detail->kuantitas);
                }

                // Hapus Kartu Stok
                DB::table('kartu_stok')
                    ->where('id_barang', $detail->id_barang)
                    ->where('keterangan', 'LIKE', "%#{$pembelian->no_faktur_pembelian}%")
                    ->delete();
            }

            // 2. Update Saldo Pemasok (Jika Kredit)
            if ($pembelian->metode_pembayaran == 'Kredit') {
                Pemasok::where('id_pemasok', $pembelian->id_pemasok)
                    ->decrement('saldo_terkini_hutang', $pembelian->total);
            }

            // 3. Hapus Jurnal Terkait
            if ($pembelian->id_jurnal) {
                JurnalDetail::where('id_jurnal', $pembelian->id_jurnal)->delete();
                Jurnal::where('id_jurnal', $pembelian->id_jurnal)->delete();
            }

            // 4. Hapus Detail & Header Pembelian
            $pembelian->details()->delete();
            $pembelian->delete();

            // Sync POS Session Total
            if ($pembelian->id_pos_session) {
                $session = \App\Models\PosSession::find($pembelian->id_pos_session);
                if ($session) {
                    $newTotal = Pembelian::where('id_pos_session', $session->id)->sum('total');
                    $session->update(['total_pembelian' => $newTotal]);
                }
            }

            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Transaksi pembelian berhasil dihapus. Stok dan saldo pemasok telah disesuaikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting pembelian: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
}
