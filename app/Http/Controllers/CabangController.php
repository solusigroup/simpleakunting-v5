<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cabang;

class CabangController extends Controller
{
    public function index()
    {
        $cabang = Cabang::all();
        return view('cabang.index', compact('cabang'));
    }

    public function create()
    {
        return view('cabang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_cabang' => 'required|string|max:20|unique:cabang,kode_cabang',
            'nama_cabang' => 'required|string|max:100',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
        ]);

        Cabang::create($request->only(['kode_cabang', 'nama_cabang', 'alamat', 'telepon']));

        return redirect()->route('cabang.index')->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $cabang = Cabang::findOrFail($id);
        return view('cabang.edit', compact('cabang'));
    }

    public function update(Request $request, $id)
    {
        $cabang = Cabang::findOrFail($id);
        
        $request->validate([
            'kode_cabang' => 'required|string|max:20|unique:cabang,kode_cabang,' . $id,
            'nama_cabang' => 'required|string|max:100',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
        ]);

        $cabang->update($request->only(['kode_cabang', 'nama_cabang', 'alamat', 'telepon']));

        return redirect()->route('cabang.index')->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $cabang = Cabang::findOrFail($id);
        $cabang->delete();

        return redirect()->route('cabang.index')->with('success', 'Cabang berhasil dihapus.');
    }
}
