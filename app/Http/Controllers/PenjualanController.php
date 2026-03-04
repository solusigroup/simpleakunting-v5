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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenjualanController extends Controller
{
    use \App\Traits\CheckClosedPeriod;
    public function index()
    {
        $penjualan = Penjualan::with('pelanggan')->orderBy('tanggal_faktur', 'desc')->paginate(20);
        return view('penjualan.index', compact('penjualan'));
    }

    public function create()
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

        return view('penjualan.create', compact('pelanggan', 'barang', 'akunKas', 'noFaktur', 'cabang', 'unitUsaha'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'id_cabang' => 'required|exists:cabang,id',
            'id_unit_usaha' => 'required|exists:unit_usaha,id',
            'no_faktur' => 'required|unique:penjualan,no_faktur',
            'tanggal_faktur' => 'required|date|before_or_equal:today',
            'metode_pembayaran' => 'required|in:Tunai,Kredit',
            'akun_kas_bank' => 'required_if:metode_pembayaran,Tunai',
            'details' => 'required|array|min:1',
            'details.*.id_barang' => 'required|exists:master_persediaan,id_barang',
            'details.*.kuantitas' => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            // C3: Cek periode tutup buku
            $this->validatePeriodOpen($request->tanggal_faktur);

            // H5: Generate no_faktur atomik
            $lastFaktur = Penjualan::orderBy('id_penjualan', 'desc')->lockForUpdate()->first();
            $nextNo = $lastFaktur ? (int)substr($lastFaktur->no_faktur, 4) + 1 : 1;
            $noFaktur = 'INV-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

            // 1. Hitung Total & Validasi Stok
            $totalPenjualan = 0;
            $detailsData = [];
            
            foreach ($request->details as $item) {
                $barang = Persediaan::findOrFail($item['id_barang']);
                
                if ($barang->stok_saat_ini < $item['kuantitas']) {
                    throw new \Exception("Stok barang {$barang->nama_barang} tidak mencukupi. Sisa: {$barang->stok_saat_ini}");
                }

                $subtotal = $barang->harga_jual * $item['kuantitas'];
                $totalPenjualan += $subtotal;

                $detailsData[] = [
                    'barang' => $barang,
                    'kuantitas' => $item['kuantitas'],
                    'harga' => $barang->harga_jual,
                    'subtotal' => $subtotal,
                ];
            }

            // H1: Dynamic account codes dari settings
            $perusahaan = DB::table('perusahaan')->first();
            $akunPiutang = $perusahaan->akun_piutang ?? '1-10100';
            $akunPendapatanDefault = $perusahaan->akun_pendapatan ?? '4-10000';

            $jurnal = Jurnal::create([
                'no_transaksi' => $noFaktur,
                'tanggal' => $request->tanggal_faktur,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'deskripsi' => "Penjualan Faktur #{$noFaktur}",
                'sumber_jurnal' => 'Penjualan',
                'is_locked' => 1
            ]);

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
                // Debit: HPP
                // Kredit: Persediaan
                // Skip HPP untuk perusahaan jasa
                $jenisUsaha = DB::table('perusahaan')->where('id', 1)->value('jenis_usaha') ?? 'dagang';
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

            // 3. Simpan Penjualan
            $penjualan = Penjualan::create([
                'id_pelanggan' => $request->id_pelanggan,
                'id_jurnal' => $jurnal->id_jurnal,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'no_faktur' => $noFaktur,
                'tanggal_faktur' => $request->tanggal_faktur,
                'total' => $totalPenjualan,
                'keterangan' => $request->keterangan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'akun_kas_bank' => ($request->metode_pembayaran == 'Tunai') ? $request->akun_kas_bank : null,
                'sisa_tagihan' => ($request->metode_pembayaran == 'Kredit') ? $totalPenjualan : 0,
                'status_pembayaran' => ($request->metode_pembayaran == 'Kredit') ? 'Belum Lunas' : 'Lunas',
            ]);

            // 4. Simpan Detail & Update Stok
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
                    'keterangan' => "Penjualan #{$noFaktur}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 5. Update Saldo Pelanggan (Jika Kredit)
            if ($request->metode_pembayaran == 'Kredit') {
                Pelanggan::where('id_pelanggan', $request->id_pelanggan)
                    ->increment('saldo_terkini_piutang', $totalPenjualan);
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

    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['details.barang', 'pelanggan']);
        return view('penjualan.show', compact('penjualan'));
    }
    
    public function destroy(Penjualan $penjualan)
    {
        // Implementasi pembatalan transaksi (Reverse Jurnal, Stok, Saldo)
        // Untuk kesederhanaan saat ini, kita skip dulu atau buat basic delete
        // Idealnya: Buat jurnal pembalik, kembalikan stok, kurangi saldo piutang
        
        return back()->with('error', 'Fitur hapus penjualan belum diimplementasikan demi keamanan data akuntansi.');
    }
}
