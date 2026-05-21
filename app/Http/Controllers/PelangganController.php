<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pelanggan = Pelanggan::all();
        return view('pelanggan.index', compact('pelanggan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pelanggan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'saldo_awal_piutang' => 'required|numeric|min:0',
        ]);

        // Saldo terkini diinisialisasi sama dengan saldo awal
        $validatedData['saldo_terkini_piutang'] = $validatedData['saldo_awal_piutang'];

        Pelanggan::create($validatedData);

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Pelanggan $pelanggan)
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        // Ambil histori jurnal yang terkait dengan pelanggan ini (Tipe Piutang)
        $transactions = \App\Models\JurnalDetail::whereHas('jurnal', function($q) use ($pelanggan, $startDate, $endDate) {
                $q->where('id_pelanggan', $pelanggan->id_pelanggan)
                  ->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->whereHas('akun', function($q) {
                $q->where('tipe_akun', 'Piutang');
            })
            ->with('jurnal')
            ->get()
            ->sortBy('jurnal.tanggal');

        // Hitung Saldo Awal sebelum periode (untuk running balance)
        $saldoSebelumnya = \App\Models\JurnalDetail::whereHas('jurnal', function($q) use ($pelanggan, $startDate) {
                $q->where('id_pelanggan', $pelanggan->id_pelanggan)
                  ->where('tanggal', '<', $startDate);
            })
            ->whereHas('akun', function($q) {
                $q->where('tipe_akun', 'Piutang');
            })
            ->select(\Illuminate\Support\Facades\DB::raw('SUM(debit) - SUM(kredit) as total'))
            ->value('total') ?? 0;

        $saldoAwalPeriode = (float)($pelanggan->saldo_awal_piutang ?? 0) + (float)($saldoSebelumnya ?? 0);

        return view('pelanggan.show', compact('pelanggan', 'transactions', 'saldoAwalPeriode', 'startDate', 'endDate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pelanggan $pelanggan)
    {
        $validatedData = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            // Saldo awal biasanya tidak diubah setelah transaksi berjalan, tapi kita izinkan untuk koreksi data master
            'saldo_awal_piutang' => 'required|numeric|min:0', 
        ]);

        // Jika saldo awal berubah, kita perlu menyesuaikan saldo terkini
        // Logic sederhana: selisih saldo awal ditambahkan ke saldo terkini
        $selisih = $validatedData['saldo_awal_piutang'] - $pelanggan->saldo_awal_piutang;
        $validatedData['saldo_terkini_piutang'] = $pelanggan->saldo_terkini_piutang + $selisih;

        $pelanggan->update($validatedData);

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function recalculate()
    {
        \App\Jobs\RecalculatePelangganJob::dispatch();
        
        return redirect()->route('pelanggan.index')->with('success', 'Proses sinkronisasi saldo piutang sedang berjalan di latar belakang (Background Job).');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pelanggan $pelanggan)
    {
        // Cek apakah ada transaksi terkait
        $hasPenjualan = \App\Models\Penjualan::where('id_pelanggan', $pelanggan->id_pelanggan)->exists();
        $hasJurnal = \App\Models\Jurnal::where('id_pelanggan', $pelanggan->id_pelanggan)->exists();

        if ($hasPenjualan || $hasJurnal) {
            return back()->with('error', 'Gagal menghapus: Pelanggan ini sudah memiliki transaksi terkait.');
        }

        $pelanggan->delete();

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil dihapus.');
    }
}
