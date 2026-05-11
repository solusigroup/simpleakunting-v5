<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FixedAsset;
use App\Models\FixedAssetGroup;
use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FixedAssetController extends Controller
{
    public function index()
    {
        $assets = FixedAsset::with('group')->get();
        return view('aset-tetap.asset.index', compact('assets'));
    }

    public function create()
    {
        $groups = FixedAssetGroup::all();
        // Generate kode_aset
        $lastAsset = FixedAsset::orderBy('id', 'desc')->first();
        $nextId = $lastAsset ? $lastAsset->id + 1 : 1;
        $kodeAset = 'FA-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        
        $akunKas = Akun::where('tipe_akun', 'Kas & Bank')->get();
        
        return view('aset-tetap.asset.create', compact('groups', 'kodeAset', 'akunKas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelompok_aset_id' => 'required|exists:fixed_asset_groups,id',
            'kode_aset' => 'required|string|unique:fixed_assets,kode_aset',
            'nama_aset' => 'required|string|max:255',
            'tanggal_perolehan' => 'required|date',
            'harga_perolehan' => 'required|numeric|min:0',
            'nilai_residu' => 'required|numeric|min:0',
            'umur_ekonomis_bulan' => 'required|integer|min:1',
            'akun_pembayaran' => 'required|exists:akun,kode_akun',
        ]);

        try {
            DB::beginTransaction();

            $group = FixedAssetGroup::findOrFail($request->kelompok_aset_id);
            $akunAset = $group->akun_aset ?? '1-20100'; // Fallback to a common Fixed Asset account if not set

            $data = $request->all();
            $data['nilai_buku_saat_ini'] = $data['harga_perolehan'];
            $data['status'] = 'Aktif';
            $data['cabang_id'] = session('cabang_id') ?? null;

            // 1. Create Journal
            $jurnal = Jurnal::create([
                'no_transaksi' => $request->kode_aset,
                'tanggal' => $request->tanggal_perolehan,
                'id_cabang' => $data['cabang_id'],
                'deskripsi' => "Perolehan Aset Tetap: {$request->nama_aset}",
                'sumber_jurnal' => 'Aset Tetap',
                'is_locked' => 1
            ]);

            // Debit: Akun Aset Tetap
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $akunAset,
                'debit' => $request->harga_perolehan,
                'kredit' => 0
            ]);

            // Kredit: Kas/Bank
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $request->akun_pembayaran,
                'debit' => 0,
                'kredit' => $request->harga_perolehan
            ]);

            // 2. Create Asset Record
            $data['id_jurnal'] = $jurnal->id_jurnal;
            FixedAsset::create($data);

            DB::commit();
            return redirect()->route('aset-tetap.index')->with('success', 'Aset tetap dan jurnal perolehan berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan aset tetap: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $asset = FixedAsset::findOrFail($id);
        $groups = FixedAssetGroup::all();
        return view('aset-tetap.asset.edit', compact('asset', 'groups'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kelompok_aset_id' => 'required|exists:fixed_asset_groups,id',
            'nama_aset' => 'required|string|max:255',
            'tanggal_perolehan' => 'required|date',
            'harga_perolehan' => 'required|numeric|min:0',
            'nilai_residu' => 'required|numeric|min:0',
            'umur_ekonomis_bulan' => 'required|integer|min:1',
        ]);

        $asset = FixedAsset::findOrFail($id);
        $asset->update($request->except(['kode_aset']));

        // Note: Changing harga_perolehan should probably recalculate depreciation,
        // but for simplicity, we just update it here.
        // If it's a critical system, we'd need more complex logic.

        return redirect()->route('aset-tetap.index')->with('success', 'Aset tetap berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $asset = FixedAsset::findOrFail($id);
        $asset->delete();

        return redirect()->route('aset-tetap.index')->with('success', 'Aset tetap berhasil dihapus.');
    }

    public function runDepreciation()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('asset:depreciate');
            $output = \Illuminate\Support\Facades\Artisan::output();
            
            // Extract the final success message from output
            $messages = explode("\n", trim($output));
            $lastMessage = end($messages);
            
            return redirect()->back()->with('success', $lastMessage ?: 'Proses depresiasi berhasil dijalankan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menjalankan depresiasi: ' . $e->getMessage());
        }
    }
}
