<?php

namespace App\Http\Controllers;

use App\Models\UnitUsaha;
use App\Models\Cabang;
use Illuminate\Http\Request;

class UnitUsahaController extends Controller
{
    public function index()
    {
        $units = UnitUsaha::with('cabang')->orderBy('kode_unit')->paginate(20);
        return view('unit-usaha.index', compact('units'));
    }

    public function create()
    {
        $cabang = Cabang::orderBy('nama_cabang')->get();
        return view('unit-usaha.create', compact('cabang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cabang' => 'required|exists:cabang,id',
            'kode_unit' => 'required|string|max:20|unique:unit_usaha,kode_unit',
            'nama_unit' => 'required|string|max:100',
            'jenis_usaha' => 'nullable|string|max:50',
        ]);

        UnitUsaha::create($request->only(['id_cabang', 'kode_unit', 'nama_unit', 'jenis_usaha']));

        return redirect()->route('unit-usaha.index')->with('success', 'Unit usaha berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $unit = UnitUsaha::findOrFail($id);
        $cabang = Cabang::orderBy('nama_cabang')->get();
        return view('unit-usaha.edit', compact('unit', 'cabang'));
    }

    public function update(Request $request, $id)
    {
        $unit = UnitUsaha::findOrFail($id);

        $request->validate([
            'id_cabang' => 'required|exists:cabang,id',
            'kode_unit' => 'required|string|max:20|unique:unit_usaha,kode_unit,' . $id,
            'nama_unit' => 'required|string|max:100',
            'jenis_usaha' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $unit->update($request->only(['id_cabang', 'kode_unit', 'nama_unit', 'jenis_usaha', 'is_active']));

        return redirect()->route('unit-usaha.index')->with('success', 'Unit usaha berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $unit = UnitUsaha::findOrFail($id);

        // Cek apakah ada transaksi terkait
        $tables = ['jurnal_umum', 'penjualan', 'pembelian', 'simpanan', 'pinjaman'];
        foreach ($tables as $table) {
            $count = \DB::table($table)->where('id_unit_usaha', $id)->count();
            if ($count > 0) {
                return back()->with('error', "Unit usaha tidak bisa dihapus karena masih ada transaksi terkait di tabel {$table}.");
            }
        }

        $unit->delete();
        return redirect()->route('unit-usaha.index')->with('success', 'Unit usaha berhasil dihapus.');
    }

    /**
     * API: Get units by cabang (untuk cascade dropdown).
     */
    public function getByCabang($cabangId)
    {
        $units = UnitUsaha::where('id_cabang', $cabangId)
            ->where('is_active', true)
            ->orderBy('nama_unit')
            ->get(['id', 'kode_unit', 'nama_unit']);

        return response()->json($units);
    }
}
