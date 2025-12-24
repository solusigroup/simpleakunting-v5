<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnggotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Anggota::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_anggota', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $anggota = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('anggota.index', compact('anggota'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $noAnggota = Anggota::generateNoAnggota();
        return view('anggota.create', compact('noAnggota'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:anggota,nik',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'pekerjaan' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tanggal_daftar' => 'required|date',
        ]);

        $validated['no_anggota'] = Anggota::generateNoAnggota();
        $validated['status'] = 'aktif';

        // Handle foto upload
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = 'anggota_' . time() . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('public/anggota', $filename);
            $validated['foto'] = $filename;
        }

        Anggota::create($validated);

        return redirect()->route('anggota.index')
            ->with('success', 'Anggota berhasil ditambahkan dengan nomor: ' . $validated['no_anggota']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $anggota = Anggota::with(['simpanan.jenisSimpanan', 'pinjaman.jenisPinjaman'])
            ->findOrFail($id);
        
        return view('anggota.show', compact('anggota'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $anggota = Anggota::findOrFail($id);
        return view('anggota.edit', compact('anggota'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $anggota = Anggota::findOrFail($id);

        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:anggota,nik,' . $id . ',id_anggota',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'pekerjaan' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:aktif,non_aktif,keluar',
            'tanggal_keluar' => 'nullable|required_if:status,keluar|date',
        ]);

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Delete old foto
            if ($anggota->foto) {
                Storage::delete('public/anggota/' . $anggota->foto);
            }
            
            $foto = $request->file('foto');
            $filename = 'anggota_' . time() . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('public/anggota', $filename);
            $validated['foto'] = $filename;
        }

        $anggota->update($validated);

        return redirect()->route('anggota.index')
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $anggota = Anggota::findOrFail($id);
        
        // Check if anggota has simpanan or pinjaman
        if ($anggota->simpanan()->exists() || $anggota->pinjaman()->exists()) {
            return redirect()->route('anggota.index')
                ->with('error', 'Anggota tidak dapat dihapus karena masih memiliki data simpanan atau pinjaman.');
        }

        // Delete foto if exists
        if ($anggota->foto) {
            Storage::delete('public/anggota/' . $anggota->foto);
        }

        $anggota->delete();

        return redirect()->route('anggota.index')
            ->with('success', 'Data anggota berhasil dihapus.');
    }

    /**
     * Display kartu anggota (member card with summary)
     */
    public function kartu(string $id)
    {
        $anggota = Anggota::with([
            'simpanan.jenisSimpanan', 
            'pinjaman.jenisPinjaman',
            'pinjaman.jadwal'
        ])->findOrFail($id);

        // Calculate simpanan summary
        $simpananSummary = [
            'pokok' => 0,
            'wajib' => 0,
            'sukarela' => 0,
            'deposito' => 0,
        ];

        foreach ($anggota->simpanan as $s) {
            $tipe = $s->jenisSimpanan->tipe;
            $amount = $s->jenis_transaksi === 'setor' ? $s->jumlah : -$s->jumlah;
            $simpananSummary[$tipe] = ($simpananSummary[$tipe] ?? 0) + $amount;
        }

        // Calculate pinjaman summary
        $pinjamanAktif = $anggota->pinjaman()
            ->whereIn('status', ['active', 'disbursed'])
            ->get();

        return view('anggota.kartu', compact('anggota', 'simpananSummary', 'pinjamanAktif'));
    }
}
