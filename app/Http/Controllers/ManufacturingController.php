<?php

namespace App\Http\Controllers;

use App\Models\Bom;
use App\Models\BomDetail;
use App\Models\Produksi;
use App\Models\ProduksiDetail;
use App\Models\Persediaan;
use App\Models\Cabang;
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

            $bom = Bom::with('details.material')->findOrFail($request->bom_id);
            
            // Calculate total needed materials
            $bomInfo = [];
            foreach ($bom->details as $detail) {
                $needed = ($detail->kuantitas / $bom->kuantitas_hasil) * $request->kuantitas_produksi;
                
                // Check stock
                // Assuming global stock check for now, ideally per branch if id_cabang is set
                $material = $detail->material;
                if ($material->stok_saat_ini < $needed) {
                    throw new \Exception("Stok {$material->nama_barang} tidak cukup. Butuh {$needed}, ada {$material->stok_saat_ini}");
                }
                
                $bomInfo[] = [
                    'material' => $material,
                    'qty' => $needed,
                    'cost' => $material->harga_beli // Moving Average or Standard Cost? Using price from master for simplicity
                ];
            }

            // Create Production Record
            $produksi = Produksi::create([
                'no_produksi' => 'PROD-' . time(),
                'tanggal' => $request->tanggal,
                'bom_id' => $request->bom_id,
                'id_cabang' => $request->id_cabang,
                'kuantitas_produksi' => $request->kuantitas_produksi,
                'status' => 'completed', // For simplicity, direct complete. Or 'process' then 'complete'.
                'keterangan' => $request->keterangan,
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
                    'id_cabang' => $request->id_cabang,
                    'tipe_transaksi' => 'OUT', // Production Usage
                    'kuantitas' => $item['qty'],
                    'keterangan' => "Produksi {$produksi->no_produksi}",
                    'created_at' => now(), 'updated_at' => now()
                ]);
            }

            // Increase Finished Good Stock
            $fg = $bom->barangJadi;
            $fg->increment('stok_saat_ini', $request->kuantitas_produksi);
            
            // Log Stock Card (FG IN)
            DB::table('kartu_stok')->insert([
                'id_barang' => $fg->id_barang,
                'id_cabang' => $request->id_cabang,
                'tipe_transaksi' => 'IN', // Production Result
                'kuantitas' => $request->kuantitas_produksi,
                'keterangan' => "Hasil Produksi {$produksi->no_produksi}",
                'created_at' => now(), 'updated_at' => now()
            ]);

            // Journal Entry (WIP/HPP)
            // Debit Persediaan Barang Jadi, Credit Persediaan Bahan Baku
            // Or Debit WIP, Credit Raw Material -> Debit FG, Credit WIP.
            // Simplified: Debit Inventory (FG), Credit Inventory (Raw)
            
            // Need Accounts
            $akunPersediaanFG = $fg->akun_persediaan; 
            // Composite Raw Material Account? Or loop through?
            // Usually we Credit the Raw Material Inventory Account.
            
            // Create Journal
            // ... (Omitting full Journal logic for brevity, but same pattern as Agriculture)

            DB::commit();
            return redirect()->route('manufacturing.production.index')->with('success', 'Produksi berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mencatat produksi: ' . $e->getMessage());
        }
    }
}
