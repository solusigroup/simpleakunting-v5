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
        \App\Jobs\RecalculatePemasokJob::dispatch();
        
        return redirect()->route('pemasok.index')->with('success', 'Proses sinkronisasi saldo hutang sedang berjalan di latar belakang (Background Job).');
    }

    public function show(Request $request, Pemasok $pemasok)
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        // Ambil histori jurnal yang terkait dengan pemasok ini (Tipe Utang Usaha)
        $transactions = \App\Models\JurnalDetail::whereHas('jurnal', function($q) use ($pemasok, $startDate, $endDate) {
                $q->where('id_pemasok', $pemasok->id_pemasok)
                  ->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->whereHas('akun', function($q) {
                $q->where('tipe_akun', 'Utang Usaha');
            })
            ->with('jurnal')
            ->get()
            ->sortBy('jurnal.tanggal');

        // Hitung Saldo Awal sebelum periode (untuk running balance)
        $saldoSebelumnya = \App\Models\JurnalDetail::whereHas('jurnal', function($q) use ($pemasok, $startDate) {
                $q->where('id_pemasok', $pemasok->id_pemasok)
                  ->where('tanggal', '<', $startDate);
            })
            ->whereHas('akun', function($q) {
                $q->where('tipe_akun', 'Utang Usaha');
            })
            ->select(\Illuminate\Support\Facades\DB::raw('SUM(kredit) - SUM(debit) as total'))
            ->value('total') ?? 0;

        $saldoAwalPeriode = (float)($pemasok->saldo_awal_hutang ?? 0) + (float)($saldoSebelumnya ?? 0);

        return view('pemasok.show', compact('pemasok', 'transactions', 'saldoAwalPeriode', 'startDate', 'endDate'));
    }

    public function destroy(Pemasok $pemasok)
    {
        $hasPembelian = \App\Models\Pembelian::where('id_pemasok', $pemasok->id_pemasok)->exists();
        $hasJurnal = \App\Models\Jurnal::where('id_pemasok', $pemasok->id_pemasok)->exists();

        if ($hasPembelian || $hasJurnal) {
            return back()->with('error', 'Gagal menghapus: Pemasok ini sudah memiliki transaksi terkait.');
        }

        $pemasok->delete();
        return redirect()->route('pemasok.index')->with('success', 'Data pemasok berhasil dihapus.');
    }
}
