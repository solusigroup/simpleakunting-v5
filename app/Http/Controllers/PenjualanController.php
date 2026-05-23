<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Pelanggan;
use App\Models\Persediaan;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Akun;
use App\Models\Cabang;
use App\Models\UnitUsaha;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenjualanController extends Controller
{
    use \App\Traits\CheckClosedPeriod;
    public function index(Request $request)
    {
        $query = Penjualan::with('pelanggan');

        if ($request->has('id_pelanggan')) {
            $query->where('id_pelanggan', $request->id_pelanggan);
        }

        $penjualan = $query->orderBy('tanggal_faktur', 'desc')->paginate(20);
        return view('penjualan.index', compact('penjualan'));
    }

    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['details.barang', 'pelanggan']);
        return view('penjualan.show', compact('penjualan'));
    }

    public function create(Request $request)
    {
        $pelanggan = Pelanggan::all();
        $barang = Persediaan::all();
        $akunKas = Akun::where('tipe_akun', 'Kas & Bank')->get(); // Asumsi tipe akun
        
        // Generate No Faktur Otomatis (Simple)
        $lastFaktur = Penjualan::orderBy('id_penjualan', 'desc')->first();
        $nextNo = $lastFaktur ? (int)substr($lastFaktur->no_faktur, 4) + 1 : 1;
        $noFaktur = 'INV-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $projects = Project::active()->orderBy('nama_project')->get();

        // Check if prefilling from a quotation
        $penawaran = null;
        if ($request->has('from_penawaran')) {
            $penawaran = \App\Models\PenjualanPenawaran::with('details.barang')->find($request->from_penawaran);
        }

        return view('penjualan.create', compact('pelanggan', 'barang', 'akunKas', 'noFaktur', 'cabang', 'unitUsaha', 'penawaran', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'id_cabang' => 'required|exists:cabang,id',
            'id_unit_usaha' => 'required|exists:unit_usaha,id',
            'id_project' => 'nullable|exists:projects,id_project',
            'id_penawaran' => 'nullable|integer',
            'no_faktur' => 'required|unique:penjualan,no_faktur',
            'tanggal_faktur' => 'required|date|before_or_equal:today',
            'metode_pembayaran' => 'required|in:Tunai,Kredit',
            'akun_kas_bank' => 'required_if:metode_pembayaran,Tunai',
            'details' => 'required|array|min:1',
            'details.*.id_barang' => 'required|exists:master_persediaan,id_barang',
            'details.*.kuantitas' => 'required|numeric|min:1',
            'details.*.harga' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // C3: Cek periode tutup buku
            $this->validatePeriodOpen($request->tanggal_faktur);

            // H5: Generate no_faktur atomik
            $lastFaktur = Penjualan::orderBy('id_penjualan', 'desc')->lockForUpdate()->first();
            $nextNo = $lastFaktur ? (int)substr($lastFaktur->no_faktur, 4) + 1 : 1;
            $noFaktur = 'INV-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

            $jurnal = Jurnal::create([
                'no_transaksi' => $noFaktur,
                'tanggal' => $request->tanggal_faktur,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'id_project' => $request->id_project,
                'id_pelanggan' => $request->id_pelanggan,
                'deskripsi' => "Penjualan Faktur #{$noFaktur}",
                'sumber_jurnal' => 'Penjualan',
                'is_locked' => 1
            ]);

            $penjualan = Penjualan::create([
                'id_pelanggan' => $request->id_pelanggan,
                'id_jurnal' => $jurnal->id_jurnal,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'id_project' => $request->id_project,
                'id_penawaran' => $request->id_penawaran,
                'no_faktur' => $noFaktur,
                'tanggal_faktur' => $request->tanggal_faktur,
                'total' => 0,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pembayaran' => 'Belum Lunas',
                'sisa_tagihan' => 0,
            ]);

            $this->applyPenjualanImpact($penjualan, $request);

            if ($request->filled('id_penawaran')) {
                \App\Models\PenjualanPenawaran::where('id_penawaran', $request->id_penawaran)->update(['status' => 'Dikonversi']);
            }

            DB::commit();
            return redirect()->route('penjualan.index')->with('success', 'Transaksi penjualan berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $msg = str_contains($e->getMessage(), 'Periode') || str_contains($e->getMessage(), 'Stok') || str_contains($e->getMessage(), 'Saldo')
                ? $e->getMessage()
                : 'Gagal menyimpan transaksi. Silakan coba lagi.';
            return back()->with('error', $msg)->withInput();
        }
    }

    public function edit(Penjualan $penjualan)
    {
        $penjualan->load('details.barang');
        $pelanggan = Pelanggan::all();
        $barang = Persediaan::all();
        $akunKas = Akun::where('tipe_akun', 'Kas & Bank')->get();
        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $projects = Project::active()->orderBy('nama_project')->get();

        return view('penjualan.edit', compact('penjualan', 'pelanggan', 'barang', 'akunKas', 'cabang', 'unitUsaha', 'projects'));
    }

    public function update(Request $request, Penjualan $penjualan)
    {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'id_cabang' => 'required|exists:cabang,id',
            'id_unit_usaha' => 'required|exists:unit_usaha,id',
            'id_project' => 'nullable|exists:projects,id_project',
            'tanggal_faktur' => 'required|date|before_or_equal:today',
            'metode_pembayaran' => 'required|in:Tunai,Kredit',
            'akun_kas_bank' => 'required_if:metode_pembayaran,Tunai',
            'details' => 'required|array|min:1',
            'details.*.id_barang' => 'required|exists:master_persediaan,id_barang',
            'details.*.kuantitas' => 'required|numeric|min:1',
            'details.*.harga' => 'required|numeric|min:0',
        ]);

        // Proteksi Sesi POS (Shift)
        if ($penjualan->id_pos_session) {
            $session = \App\Models\PosSession::find($penjualan->id_pos_session);
            if ($session && !$session->isOpen() && !auth()->user()->isSuperuser()) {
                return back()->with('error', 'Transaksi POS ini tidak dapat diubah karena sesi kasir (shift) sudah ditutup.');
            }
        }

        try {
            DB::beginTransaction();

            // C3: Cek periode tutup buku
            $this->validatePeriodOpen($penjualan->tanggal_faktur);
            $this->validatePeriodOpen($request->tanggal_faktur);

            $this->reversePenjualanImpact($penjualan);

            $this->applyPenjualanImpact($penjualan, $request);

            $this->syncPosSessionTotal($penjualan->id_pos_session);

            DB::commit();
            return redirect()->route('penjualan.index')->with('success', 'Transaksi penjualan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Penjualan $penjualan)
    {
        // Proteksi Sesi POS (Shift)
        if ($penjualan->id_pos_session) {
            $session = \App\Models\PosSession::find($penjualan->id_pos_session);
            if ($session && !$session->isOpen() && !auth()->user()->isSuperuser()) {
                return back()->with('error', 'Transaksi POS ini tidak dapat dihapus karena sesi kasir (shift) sudah ditutup.');
            }
        }

        try {
            DB::beginTransaction();

            $this->validatePeriodOpen($penjualan->tanggal_faktur);

            $this->reversePenjualanImpact($penjualan);

            if ($penjualan->id_jurnal) {
                Jurnal::where('id_jurnal', $penjualan->id_jurnal)->delete();
            }

            $posSessionId = $penjualan->id_pos_session;
            $penjualan->delete();

            $this->syncPosSessionTotal($posSessionId);

            DB::commit();
            return redirect()->route('penjualan.index')->with('success', 'Transaksi penjualan berhasil dihapus dan stok telah disesuaikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    private function reversePenjualanImpact(Penjualan $penjualan)
    {
        foreach ($penjualan->details as $oldDetail) {
            $barang = Persediaan::find($oldDetail->id_barang);
            if ($barang) {
                $barang->increment('stok_saat_ini', $oldDetail->kuantitas);
            }
            DB::table('kartu_stok')
                ->where('id_barang', $oldDetail->id_barang)
                ->where('keterangan', 'LIKE', "%#{$penjualan->no_faktur}%")
                ->delete();
        }

        if ($penjualan->metode_pembayaran == 'Kredit') {
            Pelanggan::where('id_pelanggan', $penjualan->id_pelanggan)
                ->decrement('saldo_terkini_piutang', $penjualan->total);
        }

        if ($penjualan->id_jurnal) {
            JurnalDetail::where('id_jurnal', $penjualan->id_jurnal)->delete();
        }

        $penjualan->details()->delete();
    }

    private function applyPenjualanImpact(Penjualan $penjualan, Request $request)
    {
        // 1. Hitung Total & Validasi Stok
        $totalPenjualan = 0;
        $detailsData = [];
        
        foreach ($request->details as $item) {
            $barang = Persediaan::findOrFail($item['id_barang']);
            
            if ($barang->stok_saat_ini < $item['kuantitas']) {
                throw new \Exception("Stok barang {$barang->nama_barang} tidak mencukupi. Sisa: {$barang->stok_saat_ini}");
            }

            $hargaInput = (float)($item['harga'] ?? $barang->harga_jual);
            $subtotal = $hargaInput * $item['kuantitas'];
            $totalPenjualan += $subtotal;

            $detailsData[] = [
                'barang' => $barang,
                'kuantitas' => $item['kuantitas'],
                'harga' => $hargaInput,
                'subtotal' => $subtotal,
            ];
        }

        // Update Jurnal Header
        $jurnal = Jurnal::findOrFail($penjualan->id_jurnal);
        $jurnal->update([
            'tanggal' => $request->tanggal_faktur,
            'id_cabang' => $request->id_cabang,
            'id_unit_usaha' => $request->id_unit_usaha,
            'id_project' => $request->id_project,
            'id_pelanggan' => $request->id_pelanggan,
            'deskripsi' => "Penjualan Faktur #{$penjualan->no_faktur}",
        ]);

        $perusahaan = DB::table('perusahaan')->first();
        $akunPiutang = $perusahaan->akun_piutang ?? '1-10100';
        $akunPendapatanDefault = $perusahaan->akun_pendapatan ?? '4-10000';
        $jenisUsaha = $perusahaan->jenis_usaha ?? 'dagang';

        // Debit: Kas (Tunai) atau Piutang (Kredit)
        $akunDebit = ($request->metode_pembayaran == 'Tunai') ? $request->akun_kas_bank : $akunPiutang;
        
        JurnalDetail::create([
            'id_jurnal' => $jurnal->id_jurnal,
            'kode_akun' => $akunDebit,
            'debit' => $totalPenjualan,
            'kredit' => 0
        ]);

        // Kredit: Pendapatan (Per Barang)
        foreach ($detailsData as $data) {
            $akunKredit = $data['barang']->akun_penjualan ?? $akunPendapatanDefault;
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $akunKredit,
                'debit' => 0,
                'kredit' => $data['subtotal']
            ]);
            
            // Jurnal HPP & Persediaan (Perpetual)
            if ($jenisUsaha !== 'jasa' && $data['barang']->akun_hpp && $data['barang']->akun_persediaan) {
                $totalHPP = $data['barang']->harga_beli * $data['kuantitas'];
                
                // Debit HPP
                JurnalDetail::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'kode_akun' => $data['barang']->akun_hpp,
                    'debit' => $totalHPP,
                    'kredit' => 0
                ]);
                
                // Kredit Persediaan
                JurnalDetail::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'kode_akun' => $data['barang']->akun_persediaan,
                    'debit' => 0,
                    'kredit' => $totalHPP
                ]);
            }
        }

        // Update Penjualan Header
        $penjualan->update([
            'id_pelanggan' => $request->id_pelanggan,
            'id_cabang' => $request->id_cabang,
            'id_unit_usaha' => $request->id_unit_usaha,
            'id_project' => $request->id_project,
            'tanggal_faktur' => $request->tanggal_faktur,
            'total' => $totalPenjualan,
            'keterangan' => $request->keterangan,
            'metode_pembayaran' => $request->metode_pembayaran,
            'akun_kas_bank' => ($request->metode_pembayaran == 'Tunai') ? $request->akun_kas_bank : null,
            'sisa_tagihan' => ($request->metode_pembayaran == 'Kredit') ? $totalPenjualan : 0,
            'status_pembayaran' => ($request->metode_pembayaran == 'Kredit') ? 'Belum Lunas' : 'Lunas',
        ]);

        // Simpan Detail & Update Stok
        foreach ($detailsData as $data) {
            PenjualanDetail::create([
                'id_penjualan' => $penjualan->id_penjualan,
                'id_barang' => $data['barang']->id_barang,
                'kuantitas' => $data['kuantitas'],
                'harga' => $data['harga'],
                'subtotal' => $data['subtotal'],
                'akun_pendapatan' => $data['barang']->akun_penjualan
            ]);

            // Kurangi Stok
            $data['barang']->decrement('stok_saat_ini', $data['kuantitas']);

            // Catat Kartu Stok
            DB::table('kartu_stok')->insert([
                'id_barang' => $data['barang']->id_barang,
                'tipe_transaksi' => 'OUT',
                'kuantitas' => $data['kuantitas'],
                'keterangan' => "Penjualan #{$penjualan->no_faktur}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update Saldo Pelanggan (Jika Kredit)
        if ($request->metode_pembayaran == 'Kredit') {
            Pelanggan::where('id_pelanggan', $request->id_pelanggan)
                ->increment('saldo_terkini_piutang', $totalPenjualan);
        }
    }

    private function syncPosSessionTotal($posSessionId)
    {
        if ($posSessionId) {
            $session = \App\Models\PosSession::find($posSessionId);
            if ($session) {
                $newTotal = Penjualan::where('id_pos_session', $session->id)->sum('total');
                $session->update(['total_penjualan' => $newTotal]);
            }
        }
    }
}
