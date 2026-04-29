<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FixedAsset;
use App\Models\FixedAssetGroup;
use Carbon\Carbon;

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
        
        return view('aset-tetap.asset.create', compact('groups', 'kodeAset'));
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
        ]);

        $data = $request->all();
        $data['nilai_buku_saat_ini'] = $data['harga_perolehan'];
        $data['status'] = 'Aktif';
        $data['cabang_id'] = session('cabang_id') ?? null;

        FixedAsset::create($data);

        return redirect()->route('aset-tetap.index')->with('success', 'Aset tetap berhasil ditambahkan.');
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
