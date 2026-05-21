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

        $saldoAwalPeriode = $pelanggan->saldo_awal_piutang + $saldoSebelumnya;

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
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 1. BACKFILL: Hubungkan kembali jurnal lama (Multi-strategy)
            // Strategi A: Berdasarkan link id_jurnal di tabel penjualan
            \Illuminate\Support\Facades\DB::statement("
                UPDATE jurnal_umum j
                JOIN penjualan p ON j.id_jurnal = p.id_jurnal
                SET j.id_pelanggan = p.id_pelanggan
                WHERE j.id_pelanggan IS NULL
            ");

            // Strategi B: Berdasarkan kecocokan no_transaksi (Fallback)
            \Illuminate\Support\Facades\DB::statement("
                UPDATE jurnal_umum j
                JOIN penjualan p ON j.no_transaksi = p.no_faktur
                SET j.id_pelanggan = p.id_pelanggan
                WHERE j.id_pelanggan IS NULL
            ");

            $pelanggan = Pelanggan::all();
            foreach ($pelanggan as $p) {
                $netChange = \App\Models\JurnalDetail::whereHas('jurnal', function($q) use ($p) {
                        $q->where('id_pelanggan', $p->id_pelanggan);
                    })
                    ->whereHas('akun', function($q) {
                        $q->where('tipe_akun', 'Piutang');
                    })
                    ->select(\Illuminate\Support\Facades\DB::raw('SUM(debit) - SUM(kredit) as net_change'))
                    ->value('net_change') ?? 0;

                $p->saldo_terkini_piutang = $p->saldo_awal_piutang + $netChange;
                $p->save();
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('pelanggan.index')->with('success', 'Sinkronisasi saldo piutang berhasil disempurnakan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->route('pelanggan.index')->with('error', 'Gagal sinkronisasi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pelanggan $pelanggan)
    {
        // Cek apakah ada transaksi terkait
        $hasPenjualan = \App\Models\Penjualan::where('id_pelanggan', $pelanggan->id_pelanggan)->exists();
        $hasJurnal = \App\Models\JurnalUmum::where('id_pelanggan', $pelanggan->id_pelanggan)->exists();

        if ($hasPenjualan || $hasJurnal) {
            return back()->with('error', 'Gagal menghapus: Pelanggan ini sudah memiliki transaksi terkait.');
        }

        $pelanggan->delete();

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil dihapus.');
    }
}
