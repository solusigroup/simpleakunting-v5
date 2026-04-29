<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FixedAssetGroup;
use App\Models\Akun;

class FixedAssetGroupController extends Controller
{
    public function index()
    {
        $groups = FixedAssetGroup::all();
        return view('aset-tetap.group.index', compact('groups'));
    }

    public function create()
    {
        $akuns = Akun::all();
        return view('aset-tetap.group.create', compact('akuns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelompok' => 'required|string|max:255',
            'umur_ekonomis' => 'required|integer|min:1',
            'metode_penyusutan' => 'required|string',
            'akun_aset' => 'nullable|string',
            'akun_akumulasi_penyusutan' => 'nullable|string',
            'akun_beban_penyusutan' => 'nullable|string',
        ]);

        FixedAssetGroup::create($request->all());

        return redirect()->route('aset-tetap-group.index')->with('success', 'Kelompok aset berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $group = FixedAssetGroup::findOrFail($id);
        $akuns = Akun::all();
        return view('aset-tetap.group.edit', compact('group', 'akuns'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelompok' => 'required|string|max:255',
            'umur_ekonomis' => 'required|integer|min:1',
            'metode_penyusutan' => 'required|string',
            'akun_aset' => 'nullable|string',
            'akun_akumulasi_penyusutan' => 'nullable|string',
            'akun_beban_penyusutan' => 'nullable|string',
        ]);

        $group = FixedAssetGroup::findOrFail($id);
        $group->update($request->all());

        return redirect()->route('aset-tetap-group.index')->with('success', 'Kelompok aset berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $group = FixedAssetGroup::findOrFail($id);
        // Maybe check if it has assets before deleting
        $group->delete();

        return redirect()->route('aset-tetap-group.index')->with('success', 'Kelompok aset berhasil dihapus.');
    }
}
