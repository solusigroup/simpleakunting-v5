<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Pelanggan;
use App\Models\Pemasok;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Persediaan;
use App\Models\ReturPembelian;
use App\Models\ReturPembelianDetail;
use App\Models\ReturPenjualan;
use App\Models\ReturPenjualanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturController extends Controller
{
    use \App\Traits\CheckClosedPeriod;

    // --- RETUR PENJUALAN ---

    public function indexPenjualan()
    {
        $retur = ReturPenjualan::with(['pelanggan', 'penjualan'])->orderBy('tanggal', 'desc')->paginate(20);
        return view('retur.penjualan.index', compact('retur'));
    }

    public function createPenjualan(Request $request)
    {
        $penjualan = null;
        if ($request->has('id_penjualan')) {
            $penjualan = Penjualan::with(['pelanggan', 'details.barang'])->findOrFail($request->id_penjualan);
        }
        
        $allPenjualan = Penjualan::with('pelanggan')->orderBy('tanggal_faktur', 'desc')->limit(100)->get();
        return view('retur.penjualan.create', compact('penjualan', 'allPenjualan'));
    }

    public function storePenjualan(Request $request)
    {
        $request->validate([
            'id_penjualan' => 'required|exists:penjualan,id_penjualan',
            'tanggal' => 'required|date',
            'items' => 'required|array',
            'items.*.id_barang' => 'required|exists:master_persediaan,id_barang',
            'items.*.qty_retur' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $penjualan = Penjualan::findOrFail($request->id_penjualan);
            $totalRetur = 0;

            // 1. Generate Nomor Retur
            $noRetur = 'RJ-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

            // 2. Create Retur Header
            $retur = ReturPenjualan::create([
                'id_penjualan' => $penjualan->id_penjualan,
                'id_pelanggan' => $penjualan->id_pelanggan,
                'no_retur' => $noRetur,
                'tanggal' => $request->tanggal,
                'total_retur' => 0, // Update later
                'keterangan' => $request->keterangan
            ]);

            foreach ($request->items as $item) {
                if ($item['qty_retur'] <= 0) continue;

                $barang = Persediaan::findOrFail($item['id_barang']);
                $harga = $item['harga'] ?? 0;
                $subtotal = $item['qty_retur'] * $harga;
                $totalRetur += $subtotal;

                // Create Detail
                ReturPenjualanDetail::create([
                    'id_retur_penjualan' => $retur->id_retur_penjualan,
                    'id_barang' => $item['id_barang'],
                    'kuantitas' => $item['qty_retur'],
                    'harga' => $harga,
                    'subtotal' => $subtotal
                ]);

                // Update Stok
                $barang->stok_saat_ini += $item['qty_retur'];
                $barang->save();
            }

            $retur->update(['total_retur' => $totalRetur]);

            // 3. JURNAL OTOMATIS
            $jurnal = Jurnal::create([
                'no_transaksi' => $noRetur,
                'tanggal' => $request->tanggal,
                'deskripsi' => 'Retur Penjualan No: ' . $noRetur . ' (Faktur: ' . $penjualan->no_faktur . ')',
                'sumber_jurnal' => 'Retur Penjualan',
                'id_pelanggan' => $penjualan->id_pelanggan
            ]);

            // Debit: Retur Penjualan (4-30000)
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => '4-30000',
                'debit' => $totalRetur,
                'kredit' => 0
            ]);

            // Kredit: Piutang Usaha (1-10100)
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => '1-10100',
                'debit' => 0,
                'kredit' => $totalRetur
            ]);

            // Update Saldo Pelanggan
            $pelanggan = Pelanggan::find($penjualan->id_pelanggan);
            if ($pelanggan) {
                $pelanggan->saldo_terkini_piutang -= $totalRetur;
                $pelanggan->save();
            }

            $retur->update(['id_jurnal' => $jurnal->id_jurnal]);

            DB::commit();
            return redirect()->route('retur.penjualan.index')->with('success', 'Retur penjualan berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan retur: ' . $e->getMessage())->withInput();
        }
    }

    // --- RETUR PEMBELIAN ---

    public function indexPembelian()
    {
        $retur = ReturPembelian::with(['pemasok', 'pembelian'])->orderBy('tanggal', 'desc')->paginate(20);
        return view('retur.pembelian.index', compact('retur'));
    }

    public function createPembelian(Request $request)
    {
        $pembelian = null;
        if ($request->has('id_pembelian')) {
            $pembelian = Pembelian::with(['pemasok', 'details.barang'])->findOrFail($request->id_pembelian);
        }
        
        $allPembelian = Pembelian::with('pemasok')->orderBy('tanggal_faktur', 'desc')->limit(100)->get();
        return view('retur.pembelian.create', compact('pembelian', 'allPembelian'));
    }

    public function storePembelian(Request $request)
    {
        $request->validate([
            'id_pembelian' => 'required|exists:pembelian,id_pembelian',
            'tanggal' => 'required|date',
            'items' => 'required|array',
            'items.*.id_barang' => 'required|exists:master_persediaan,id_barang',
            'items.*.qty_retur' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $pembelian = Pembelian::findOrFail($request->id_pembelian);
            $totalRetur = 0;

            $noRetur = 'RB-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

            $retur = ReturPembelian::create([
                'id_pembelian' => $pembelian->id_pembelian,
                'id_pemasok' => $pembelian->id_pemasok,
                'no_retur' => $noRetur,
                'tanggal' => $request->tanggal,
                'total_retur' => 0,
                'keterangan' => $request->keterangan
            ]);

            foreach ($request->items as $item) {
                if ($item['qty_retur'] <= 0) continue;

                $barang = Persediaan::findOrFail($item['id_barang']);
                $harga = $item['harga'] ?? 0;
                $subtotal = $item['qty_retur'] * $harga;
                $totalRetur += $subtotal;

                ReturPembelianDetail::create([
                    'id_retur_pembelian' => $retur->id_retur_pembelian,
                    'id_barang' => $item['id_barang'],
                    'kuantitas' => $item['qty_retur'],
                    'harga' => $harga,
                    'subtotal' => $subtotal
                ]);

                $barang->stok_saat_ini -= $item['qty_retur'];
                $barang->save();
            }

            $retur->update(['total_retur' => $totalRetur]);

            // JURNAL
            $jurnal = Jurnal::create([
                'no_transaksi' => $noRetur,
                'tanggal' => $request->tanggal,
                'deskripsi' => 'Retur Pembelian No: ' . $noRetur . ' (Faktur: ' . $pembelian->no_faktur_pembelian . ')',
                'sumber_jurnal' => 'Retur Pembelian',
                'id_pemasok' => $pembelian->id_pemasok
            ]);

            // Debit: Utang Usaha (2-10100)
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => '2-10100',
                'debit' => $totalRetur,
                'kredit' => 0
            ]);

            // Kredit: Persediaan (1-10200)
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => '1-10200',
                'debit' => 0,
                'kredit' => $totalRetur
            ]);

            // Update Saldo Pemasok
            $pemasok = Pemasok::find($pembelian->id_pemasok);
            if ($pemasok) {
                $pemasok->saldo_terkini_hutang -= $totalRetur;
                $pemasok->save();
            }

            $retur->update(['id_jurnal' => $jurnal->id_jurnal]);

            DB::commit();
            return redirect()->route('retur.pembelian.index')->with('success', 'Retur pembelian berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan retur: ' . $e->getMessage())->withInput();
        }
    }

    public function showPenjualan($id)
    {
        $retur = ReturPenjualan::with(['pelanggan', 'penjualan', 'details.barang'])->findOrFail($id);
        return view('retur.penjualan.show', compact('retur'));
    }

    public function showPembelian($id)
    {
        $retur = ReturPembelian::with(['pemasok', 'pembelian', 'details.barang'])->findOrFail($id);
        return view('retur.pembelian.show', compact('retur'));
    }
}
