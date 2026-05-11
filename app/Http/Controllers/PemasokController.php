<?php

namespace App\Http\Controllers;

use App\Models\Pemasok;
use Illuminate\Http\Request;

class PemasokController extends Controller
{
    public function index()
    {
        $pemasok = Pemasok::all();
        return view('pemasok.index', compact('pemasok'));
    }

    public function create()
    {
        return view('pemasok.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_pemasok' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'saldo_awal_hutang' => 'required|numeric|min:0',
        ]);

        $validatedData['saldo_terkini_hutang'] = $validatedData['saldo_awal_hutang'];

        Pemasok::create($validatedData);

        return redirect()->route('pemasok.index')->with('success', 'Data pemasok berhasil ditambahkan.');
    }

    public function edit(Pemasok $pemasok)
    {
        return view('pemasok.edit', compact('pemasok'));
    }

    public function update(Request $request, Pemasok $pemasok)
    {
        $validatedData = $request->validate([
            'nama_pemasok' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'saldo_awal_hutang' => 'required|numeric|min:0',
        ]);

        $selisih = $validatedData['saldo_awal_hutang'] - $pemasok->saldo_awal_hutang;
        $validatedData['saldo_terkini_hutang'] = $pemasok->saldo_terkini_hutang + $selisih;

        $pemasok->update($validatedData);

        return redirect()->route('pemasok.index')->with('success', 'Data pemasok berhasil diperbarui.');
    }

    public function recalculate()
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 1. BACKFILL: Hubungkan kembali jurnal lama (Multi-strategy)
            // Strategi A: Berdasarkan link id_jurnal di tabel pembelian
            \Illuminate\Support\Facades\DB::statement("
                UPDATE jurnal_umum j
                JOIN pembelian p ON j.id_jurnal = p.id_jurnal
                SET j.id_pemasok = p.id_pemasok
                WHERE j.id_pemasok IS NULL
            ");

            // Strategi B: Berdasarkan kecocokan no_transaksi (Fallback)
            \Illuminate\Support\Facades\DB::statement("
                UPDATE jurnal_umum j
                JOIN pembelian p ON j.no_transaksi = p.no_faktur_pembelian
                SET j.id_pemasok = p.id_pemasok
                WHERE j.id_pemasok IS NULL
            ");

            $pemasok = Pemasok::all();
            foreach ($pemasok as $v) {
                $netChange = \App\Models\JurnalDetail::whereHas('jurnal', function($q) use ($v) {
                        $q->where('id_pemasok', $v->id_pemasok);
                    })
                    ->whereHas('akun', function($q) {
                        $q->where('tipe_akun', 'Utang Usaha');
                    })
                    ->select(\Illuminate\Support\Facades\DB::raw('SUM(kredit) - SUM(debit) as net_change'))
                    ->value('net_change') ?? 0;

                $v->saldo_terkini_hutang = $v->saldo_awal_hutang + $netChange;
                $v->save();
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('pemasok.index')->with('success', 'Sinkronisasi saldo hutang berhasil disempurnakan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->route('pemasok.index')->with('error', 'Gagal sinkronisasi: ' . $e->getMessage());
        }
    }

    public function destroy(Pemasok $pemasok)
    {
        $pemasok->delete();
        return redirect()->route('pemasok.index')->with('success', 'Data pemasok berhasil dihapus.');
    }
}
