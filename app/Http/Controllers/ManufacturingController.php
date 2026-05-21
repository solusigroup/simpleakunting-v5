<?php

namespace App\Http\Controllers;

use App\Models\Bom;
use App\Models\BomDetail;
use App\Models\Produksi;
use App\Models\ProduksiDetail;
use App\Models\Persediaan;
use App\Models\Cabang;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ManufacturingController extends Controller
{
    // BOM Management
    public function bomIndex()
    {
        $boms = Bom::with('barangJadi')->get();
        return view('manufacturing.bom.index', compact('boms'));
    }

    public function bomCreate()
    {
        $products = Persediaan::where('jenis_barang', 'barang_jadi')->get();
        $materials = Persediaan::whereIn('jenis_barang', ['bahan_baku', 'barang_dalam_proses'])->get();
        return view('manufacturing.bom.create', compact('products', 'materials'));
    }

    public function bomStore(Request $request)
    {
        $request->validate([
            'nama_bom' => 'required|string',
            'barang_jadi_id' => 'required|exists:master_persediaan,id_barang',
            'kuantitas_hasil' => 'required|numeric|min:1',
            'details' => 'required|array|min:1',
            'details.*.material_id' => 'required|exists:master_persediaan,id_barang',
            'details.*.kuantitas' => 'required|numeric|min:0.0001',
        ]);

        try {
            DB::beginTransaction();

            $bom = Bom::create([
                'kode_bom' => 'BOM-' . time(), // Simple generation
                'nama_bom' => $request->nama_bom,
                'barang_jadi_id' => $request->barang_jadi_id,
                'kuantitas_hasil' => $request->kuantitas_hasil,
                'deskripsi' => $request->deskripsi,
            ]);

            foreach ($request->details as $detail) {
                BomDetail::create([
                    'bom_id' => $bom->id,
                    'material_id' => $detail['material_id'],
                    'kuantitas' => $detail['kuantitas'],
                    'satuan' => 'pcs', // Default or fetch from master
                ]);
            }

            DB::commit();
            return redirect()->route('manufacturing.bom.index')->with('success', 'BOM berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat BOM: ' . $e->getMessage());
        }
    }

    public function bomEdit($id)
    {
        $bom = Bom::with('details.material')->findOrFail($id);
        $products = Persediaan::where('jenis_barang', 'barang_jadi')->get();
        $materials = Persediaan::whereIn('jenis_barang', ['bahan_baku', 'barang_dalam_proses'])->get();
        return view('manufacturing.bom.edit', compact('bom', 'products', 'materials'));
    }

    public function bomUpdate(Request $request, $id)
    {
        $request->validate([
            'nama_bom' => 'required|string',
            'barang_jadi_id' => 'required|exists:master_persediaan,id_barang',
            'kuantitas_hasil' => 'required|numeric|min:1',
            'details' => 'required|array|min:1',
            'details.*.material_id' => 'required|exists:master_persediaan,id_barang',
            'details.*.kuantitas' => 'required|numeric|min:0.0001',
        ]);

        try {
            DB::beginTransaction();

            $bom = Bom::findOrFail($id);
            $bom->update([
                'nama_bom' => $request->nama_bom,
                'barang_jadi_id' => $request->barang_jadi_id,
                'kuantitas_hasil' => $request->kuantitas_hasil,
                'deskripsi' => $request->deskripsi,
            ]);

            // Clear old details and create new ones
            $bom->details()->delete();

            foreach ($request->details as $detail) {
                BomDetail::create([
                    'bom_id' => $bom->id,
                    'material_id' => $detail['material_id'],
                    'kuantitas' => $detail['kuantitas'],
                    'satuan' => 'pcs', // Default
                ]);
            }

            DB::commit();
            return redirect()->route('manufacturing.bom.index')->with('success', 'BOM berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui BOM: ' . $e->getMessage());
        }
    }

    public function bomDestroy($id)
    {
        try {
            DB::beginTransaction();

            $bom = Bom::findOrFail($id);
            // Check if this BOM has been used in any production.
            $used = Produksi::where('bom_id', $id)->exists();
            if ($used) {
                return back()->with('error', 'BOM tidak dapat dihapus karena sudah digunakan dalam produksi.');
            }

            $bom->details()->delete();
            $bom->delete();

            DB::commit();
            return redirect()->route('manufacturing.bom.index')->with('success', 'BOM berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus BOM: ' . $e->getMessage());
        }
    }

    // Production Management
    public function productionIndex()
    {
        $productions = Produksi::with(['bom.barangJadi', 'cabang'])->orderBy('tanggal', 'desc')->get();
        return view('manufacturing.production.index', compact('productions'));
    }

    public function productionCreate()
    {
        $boms = Bom::with('barangJadi')->get();
        $cabangs = Cabang::all();
        return view('manufacturing.production.create', compact('boms', 'cabangs'));
    }

    public function productionStore(Request $request)
    {
        $request->validate([
            'bom_id' => 'required|exists:bom,id',
            'tanggal' => 'required|date',
            'kuantitas_produksi' => 'required|numeric|min:1',
            'id_cabang' => 'nullable|exists:cabang,id',
        ]);

        try {
            DB::beginTransaction();

            $produksi = Produksi::create([
                'no_produksi' => 'PROD-' . time(),
                'tanggal' => $request->tanggal,
                'bom_id' => $request->bom_id,
                'id_cabang' => $request->id_cabang,
                'kuantitas_produksi' => $request->kuantitas_produksi,
                'status' => 'completed',
                'keterangan' => $request->keterangan,
            ]);

            $this->applyProductionImpact($produksi, $request->bom_id, $request->kuantitas_produksi, $request->id_cabang, $request->tanggal, $request->keterangan);

            DB::commit();
            return redirect()->route('manufacturing.production.index')->with('success', 'Produksi berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mencatat produksi: ' . $e->getMessage());
        }
    }

    // =====================================================
    // LAPORAN MANUFAKTUR
    // =====================================================

    /**
     * Laporan Biaya Produksi
     * Menampilkan rincian biaya untuk setiap nomor produksi.
     */
    public function laporanBiayaProduksi(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $idCabang = $request->input('id_cabang');

        $query = Produksi::with(['bom.barangJadi', 'cabang', 'details.material'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->where('status', 'completed');

        if ($idCabang) {
            $query->where('id_cabang', $idCabang);
        }

        $productions = $query->get();
        $cabangs = Cabang::all();
        $perusahaan = DB::table('perusahaan')->first();

        return view('manufacturing.laporan.biaya_produksi', compact('productions', 'startDate', 'endDate', 'idCabang', 'cabangs', 'perusahaan'));
    }

    /**
     * Laporan Penggunaan Material
     * Menampilkan total kuantitas material yang digunakan dalam periode tertentu.
     */
    public function laporanPenggunaanMaterial(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        $materialUsage = ProduksiDetail::select(
                'material_id',
                DB::raw('SUM(kuantitas_digunakan) as total_qty'),
                DB::raw('SUM(total_biaya) as total_cost')
            )
            ->whereHas('produksi', function($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate])
                  ->where('status', 'completed');
            })
            ->with('material')
            ->groupBy('material_id')
            ->get();

        $perusahaan = DB::table('perusahaan')->first();

        return view('manufacturing.laporan.penggunaan_material', compact('materialUsage', 'startDate', 'endDate', 'perusahaan'));
    }

    /**
     * Laporan WIP Valuation (Work In Process)
     * Menampilkan nilai produksi yang masih dalam status 'draft' atau 'process'.
     */
    public function laporanWipValuation(Request $request)
    {
        $wipProductions = Produksi::with(['bom.barangJadi', 'cabang', 'details'])
            ->whereIn('status', ['draft', 'process'])
            ->get();

        $totalWipValue = 0;
        foreach ($wipProductions as $p) {
            $p->total_value = $p->details->sum('total_biaya');
            $totalWipValue += $p->total_value;
        }

        $perusahaan = DB::table('perusahaan')->first();

        return view('manufacturing.laporan.wip_valuation', compact('wipProductions', 'totalWipValue', 'perusahaan'));
    }
    public function productionEdit($id)
    {
        $produksi = Produksi::with(['details.material', 'bom.barangJadi'])->findOrFail($id);
        $boms = Bom::with('barangJadi')->get();
        $cabangs = Cabang::all();
        return view('manufacturing.production.edit', compact('produksi', 'boms', 'cabangs'));
    }

    public function productionUpdate(Request $request, $id)
    {
        $request->validate([
            'bom_id' => 'required|exists:bom,id',
            'tanggal' => 'required|date',
            'kuantitas_produksi' => 'required|numeric|min:1',
            'id_cabang' => 'nullable|exists:cabang,id',
        ]);

        try {
            DB::beginTransaction();

            $produksi = Produksi::with('details.material', 'bom.barangJadi')->findOrFail($id);
            
            $this->reverseProductionImpact($produksi);

            $this->applyProductionImpact($produksi, $request->bom_id, $request->kuantitas_produksi, $request->id_cabang, $request->tanggal, $request->keterangan);

            DB::commit();
            return redirect()->route('manufacturing.production.index')->with('success', 'Produksi berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui produksi: ' . $e->getMessage());
        }
    }

    public function productionDestroy($id)
    {
        try {
            DB::beginTransaction();

            $produksi = Produksi::with('details.material', 'bom.barangJadi')->findOrFail($id);
            
            $this->reverseProductionImpact($produksi);

            if ($produksi->id_jurnal) {
                Jurnal::where('id_jurnal', $produksi->id_jurnal)->delete();
            }

            $produksi->delete();

            DB::commit();
            return redirect()->route('manufacturing.production.index')->with('success', 'Produksi berhasil dibatalkan dan stok dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus produksi: ' . $e->getMessage());
        }
    }

    private function reverseProductionImpact(Produksi $produksi)
    {
        // Reverse Materials
        foreach ($produksi->details as $oldDetail) {
            $material = $oldDetail->material;
            if ($material) {
                $material->increment('stok_saat_ini', $oldDetail->kuantitas_digunakan);
            }
        }
        // Reverse Finished Good
        $oldFg = $produksi->bom->barangJadi;
        if ($oldFg) {
            $oldFg->decrement('stok_saat_ini', $produksi->kuantitas_produksi);
        }
        // Delete old kartu stok entries
        DB::table('kartu_stok')
            ->where('keterangan', 'LIKE', "%{$produksi->no_produksi}%")
            ->delete();
        // Delete old journal details
        if ($produksi->id_jurnal) {
            JurnalDetail::where('id_jurnal', $produksi->id_jurnal)->delete();
        }
        // Clear old details
        $produksi->details()->delete();
    }

    private function applyProductionImpact(Produksi $produksi, $bomId, $kuantitasProduksi, $idCabang, $tanggal, $keterangan)
    {
        $bom = Bom::with('details.material')->findOrFail($bomId);
        
        // Calculate total needed materials
        $bomInfo = [];
        foreach ($bom->details as $detail) {
            $needed = ($detail->kuantitas / $bom->kuantitas_hasil) * $kuantitasProduksi;
            $material = $detail->material;
            
            if ($material->stok_saat_ini < $needed) {
                throw new \Exception("Stok {$material->nama_barang} tidak cukup. Butuh {$needed}, ada {$material->stok_saat_ini}");
            }
            
            $bomInfo[] = [
                'material' => $material,
                'qty' => $needed,
                'cost' => $material->harga_beli
            ];
        }

        // Update Production Record
        $produksi->update([
            'tanggal' => $tanggal,
            'bom_id' => $bomId,
            'id_cabang' => $idCabang,
            'kuantitas_produksi' => $kuantitasProduksi,
            'keterangan' => $keterangan,
        ]);

        $totalCost = 0;
        foreach ($bomInfo as $item) {
            $subtotalCost = $item['qty'] * $item['cost'];
            $totalCost += $subtotalCost;

            ProduksiDetail::create([
                'produksi_id' => $produksi->id,
                'material_id' => $item['material']->id_barang,
                'kuantitas_digunakan' => $item['qty'],
                'biaya_satuan' => $item['cost'],
                'total_biaya' => $subtotalCost,
            ]);

            // Reduce Material Stock
            $item['material']->decrement('stok_saat_ini', $item['qty']);
            
            // Log Stock Card (Material OUT)
            DB::table('kartu_stok')->insert([
                'id_barang' => $item['material']->id_barang,
                'id_cabang' => $idCabang,
                'tipe_transaksi' => 'OUT',
                'kuantitas' => $item['qty'],
                'keterangan' => "Produksi {$produksi->no_produksi}",
                'created_at' => now(), 'updated_at' => now()
            ]);
        }

        // Increase Finished Good Stock
        $fg = $bom->barangJadi;
        $fg->increment('stok_saat_ini', $kuantitasProduksi);
        
        // Log Stock Card (FG IN)
        DB::table('kartu_stok')->insert([
            'id_barang' => $fg->id_barang,
            'id_cabang' => $idCabang,
            'tipe_transaksi' => 'IN',
            'kuantitas' => $kuantitasProduksi,
            'keterangan' => "Hasil Produksi {$produksi->no_produksi}",
            'created_at' => now(), 'updated_at' => now()
        ]);

        // Journal Entry (Production Costing)
        $perusahaan = DB::table('perusahaan')->first();
        $akunPersediaanDefault = $perusahaan->akun_persediaan ?? '1-10200';

        if ($produksi->id_jurnal) {
            $jurnal = Jurnal::findOrFail($produksi->id_jurnal);
            $jurnal->update([
                'tanggal' => $tanggal,
                'id_cabang' => $idCabang,
                'deskripsi' => "Produksi #{$produksi->no_produksi}",
            ]);
        } else {
            $jurnal = Jurnal::create([
                'no_transaksi' => $produksi->no_produksi,
                'tanggal' => $tanggal,
                'id_cabang' => $idCabang,
                'deskripsi' => "Produksi #{$produksi->no_produksi}",
                'sumber_jurnal' => 'Produksi',
                'is_locked' => 1
            ]);
            $produksi->update(['id_jurnal' => $jurnal->id_jurnal]);
        }

        // Debit: Persediaan Barang Jadi
        JurnalDetail::create([
            'id_jurnal' => $jurnal->id_jurnal,
            'kode_akun' => $fg->akun_persediaan ?? $akunPersediaanDefault,
            'debit' => $totalCost,
            'kredit' => 0
        ]);

        // Kredit: Persediaan Bahan Baku
        foreach ($bomInfo as $item) {
            $subtotalCost = $item['qty'] * $item['cost'];
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $item['material']->akun_persediaan ?? $akunPersediaanDefault,
                'debit' => 0,
                'kredit' => $subtotalCost
            ]);
        }
    }
}

