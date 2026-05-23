<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\UnitUsaha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('unitUsaha.cabang')->orderBy('kode_project')->paginate(20);
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $units = UnitUsaha::with('cabang')->where('is_active', true)->orderBy('nama_unit')->get();
        return view('projects.create', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_unit_usaha' => 'required|exists:unit_usaha,id',
            'kode_project' => 'required|string|max:30|unique:projects,kode_project',
            'nama_project' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        Project::create([
            'id_unit_usaha' => $request->id_unit_usaha,
            'kode_project' => $request->kode_project,
            'nama_project' => $request->nama_project,
            'status' => 'Aktif',
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('projects.index')->with('success', 'Proyek/Program berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $units = UnitUsaha::with('cabang')->where('is_active', true)->orderBy('nama_unit')->get();
        return view('projects.edit', compact('project', 'units'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'id_unit_usaha' => 'required|exists:unit_usaha,id',
            'kode_project' => 'required|string|max:30|unique:projects,kode_project,' . $id . ',id_project',
            'nama_project' => 'required|string|max:100',
            'status' => 'required|in:Aktif,Selesai',
            'keterangan' => 'nullable|string',
        ]);

        $project->update([
            'id_unit_usaha' => $request->id_unit_usaha,
            'kode_project' => $request->kode_project,
            'nama_project' => $request->nama_project,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('projects.index')->with('success', 'Proyek/Program berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        // Cek apakah ada transaksi terkait
        $tables = ['jurnal_umum', 'penjualan', 'pembelian'];
        foreach ($tables as $table) {
            $count = DB::table($table)->where('id_project', $id)->count();
            if ($count > 0) {
                return back()->with('error', "Proyek tidak dapat dihapus karena masih digunakan di tabel {$table}.");
            }
        }

        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Proyek/Program berhasil dihapus.');
    }
}
